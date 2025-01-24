<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Utils\Apis\Aep_device_command;
use Illuminate\Contracts\Foundation\Application;

class HaimanController extends BaseController
{
    public function mufflingByOneNet($imei)
    {
        // 拼接json数据
        $json = json_encode([
            "device_name" => $imei,
            "product_id"  => 'E2dMYR85jh', // 写死
            'identifier'  => 'set_mute',
            'params'      => [
                'mute' => 1,
            ],
        ]);

        return $this->insertDeviceCacheCMD($imei, $json);
    }

    public function mufflingByCTWing($productId, $deviceId, $masterKey, $cmd = '1f560002157c')
    {
        #获取结果日志
        return Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'dataType' => 1,
                    "payload"  => $cmd,
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
    }

    /**
     * 移动海曼烟感4G回调地址
     * @param Request $request
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hmOneNet4GWarm(Request $request)
    {
        $data         = $request->input();
        $msg          = json_decode($data['msg'], true);
        $data['msg']  = $msg;
        $nonce        = $data['nonce'];
        $ionoPlatform = 'ONENET';

        Log::channel('haiman')->info("海曼移动4G msg:" . json_encode([
            'msg'   => $msg,
            'nonce' => $nonce,
            'time'  => $data['time'],
            'id'    => $data['id'],
        ]));

        $imei = $msg['deviceName'] ?? ($msg['dev_name'] ?? 0); // 设备imei
        if (!empty($imei) /*&& $type == 2*/) { // 心跳包时才下发 todo
            // 区分消息类型
            if (isset($msg['dev_name']) && $msg['type'] == 2) {
                // 在离线状态 不处理
            }
            if (isset($msg['notifyType']) && $msg['notifyType'] === 'event' && isset($msg['deviceName'])) {
                // 事件上报 不处理
            }
            if (isset($msg['notifyType']) && $msg['notifyType'] === 'property' && isset($msg['deviceName'], $msg['data']['params']) && count($msg['data']['params']) !== 5) {
                // 设备属性变更
                $time      = time();
                $productId = 'E2dMYR85jh'; // 写死，海曼烟感产品id

                $alarmStatus[]                = 21; // 默认心跳包
                $heartbeatTime                = date("Y-m-d H:i:s.Y", (int) ($data['msg']['data']['params']['heartbeat_time']['time'] ?? microtime()) / 1000);
                $ionoMazePollution            = $data['msg']['data']['params']['MazePollution']['value'] ?? '';
                $ionoSmokeScope               = $data['msg']['data']['params']['smoke_value']['value'] ?? '';
                $ionoRsrp                     = $data['msg']['data']['params']['rsrp']['value'] ?? '';
                $ionoIMSI                     = $data['msg']['data']['params']['IMSI']['value'] ?? '';
                $ionoThresholdTemperature     = $data['msg']['data']['params']['tempLimit']['value'] ?? '';
                $ionoThresholdNbModuleBattery = $data['msg']['data']['params']['batteryPercentL']['value'] ?? '';
                $ionoTemperture               = $data['msg']['data']['params']['temp']['value'] ?? 0;
                $ionoIMEI                     = $data['msg']['data']['params']['IMEI']['value'] ?? '';
                $ionoRsrq                     = $data['msg']['data']['params']['rsrq']['value'] ?? '';
                $ionoSnr                      = $data['msg']['data']['params']['snr']['value'] ?? '';
                $ionoThresholdSmokeScope      = $data['msg']['data']['params']['smoke_threshold']['value'] ?? '';
                $ionoBattery                  = $data['msg']['data']['params']['battery_value']['value'] ?? '';
                $ionoICCID                    = $data['msg']['data']['params']['ICCID']['value'] ?? '';
                // 10进制转2进制代码
                $ionoAlarmSta     = decbin((int) ($data['msg']['data']['params']['alarmSta']['value'] ?? 0));
                $ionoAlarmStaList = [
                    // ['comment', 'iono_type'],
                    ['保留', 0],
                    ['自检', 13],
                    ['烟雾告警', 1],
                    ['高温告警', 3],
                    ['防拆告警', 15],
                    ['低压', 7],
                ];

                // ionoAlarmSta 反转后，对比$ionoAlarmStaList，根据bit生成对应状态
                $ionoAlarmSta = strrev($ionoAlarmSta);
                // 示例$ionoAlarmSta = '001'; 反转后变成'100'
                if ((int) $ionoAlarmSta == '0') {
                    $alarmStatus = [];
                }else{
                    // 把字符串转换为数组
                    $ionoAlarmSta = str_split($ionoAlarmSta);
                    foreach ($ionoAlarmSta as $key => $value) {
                        if ($value == 1) {
                            $alarmStatus[] = $ionoAlarmStaList[$key][1];// 暂时保留
                            break;
                        }
                    }
                }

                $deviceUpdateData = [
                    'smde_last_heart_beat'           => $heartbeatTime,
                    'smde_last_maze_pollution'       => $ionoMazePollution,
                    'smde_last_smokescope'           => $ionoSmokeScope,
                    'smde_last_signal_intensity'     => $ionoRsrp,
                    'smde_last_temperature'          => (int) $ionoTemperture * 100,
                    'smde_nb_iid'                    => $ionoIMSI,
                    'smde_threshold_temperature'     => $ionoThresholdTemperature * 100,
                    'smde_threshold_smoke_scope'     => $ionoThresholdSmokeScope,
                    'smde_last_smoke_module_battery' => $ionoBattery,
                    'smde_last_nb_module_battery'    => $ionoBattery,
                    'smde_nb_iid2'                   => $ionoICCID,
                    'smde_online'                    => 1,
                    'smde_online_real'               => 1,
                    'smde_iot_platform'              => $ionoPlatform,
                ];
                $notificationInsertData = [
                    'iono_platform'                    => $ionoPlatform,
                    'iono_body'                        => json_encode($data),
                    'iono_timestamp'                   => $time,
                    'iono_msg_at'                      => $data['time'] ?? $time,
                    'iono_msg_imei'                    => $ionoIMEI ?? '',
                    'iono_msg_type'                    => 1,
                    'iono_nonce'                       => $data['nonce'] ?? '',
                    'iono_threshold_smoke_scope'       => $ionoThresholdSmokeScope ?? '',
                    'iono_threshold_temperature'       => $ionoThresholdTemperature ?? '',
                    'iono_threshold_nb_module_battery' => $ionoThresholdNbModuleBattery ?? '',
                    'iono_smoke_scope'                 => $ionoSmokeScope ?? '',
                    'iono_temperature'                 => $ionoTemperture * 100 ?? '',
                    'iono_smoke_module_battery'        => $ionoBattery ?? '',
                    'iono_nb_module_battery'           => $ionoBattery ?? '',
                    // 'iono_type' => $ionoType ?? '',
                    'iono_imei'                        => $imei,
                    'iono_imsi'                        => $ionoIMSI ?? '',
                    'iono_maze_pollution'              => $ionoMazePollution ?? '',
                    'iono_nb_iccid'                    => $ionoICCID ?? '',
                    'iono_rsrp'                        => $ionoRsrp ?? '',
                    'iono_rsrq'                        => $ionoRsrq ?? '',
                    'iono_snr'                         => $ionoSnr ?? '',
                    'iono_category'                    => '烟感',
                    'iono_status'                      => '待处理',
                    // 'iono_smde_id' => $smdeId,
                    'iono_crt_time'                    => date("Y-m-d H:i:s.u"), // like 2025-01-09 16:38:45.098261
                    'iono_alert_status'                => -1,
                    'iono_device_status'               => -1,
                    'iono_product_id'                  => $productId,
                ];

                $this->insertIOT($deviceUpdateData, $notificationInsertData, $alarmStatus, $imei);

                // $this->insertIOT($data, $imei, $infoType);
            }
            // 从命令缓存表中，获取命令，马上下发
            $this->getAndSendDeviceCacheCMD($imei, $data['id'] ?? '', 3);
        }

        return response()->json(['message' => 'Success']);
    }

    public function insertSmokeDetector(string $imei)
    {
        return;
        $smde_type       = "烟感";
        $smde_brand_name = "海曼";
        $smde_model_name = "HM-618PH-4G";
        $smde_model_tag  = "";
        $smde_part_id    = 1; // 如约自己的设备
        return DB::connection('mysql2')->table('smoke_detector')->insert([
            "smde_type"       => $smde_type,
            "smde_brand_name" => $smde_brand_name,
            "smde_model_name" => $smde_model_name,
            "smde_imei"       => $imei,
            "smde_model_tag"  => $smde_model_tag,
            "smde_part_id"    => $smde_part_id,
        ]);
    }
}
