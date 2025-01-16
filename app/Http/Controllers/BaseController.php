<?php

namespace App\Http\Controllers;

use Exception;
use App\Utils\OneNet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceCacheCommands;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BaseController extends \Illuminate\Routing\Controller
{
    /**
     * 生成json格式响应结果
     *
     * @param array $data
     * @param string $msg
     * @param int $statusCode
     * @return JsonResponse
     */
    public function response($data = [], $msg = 'success', $code = 0, $statusCode = 200)
    {
        return new JsonResponse(
            ['data' => $data, 'msg' => $msg, 'code' => $code],
            $statusCode,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    protected function validateParams($request, $rules, &$input)
    {
        // 进行验证
        // $rules     = [];// todo 待删
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
    }

    /**
     * 验签
     * @param string $msg
     * @param string $nonce
     * @param string $signature
     * @return bool
     */
    protected function checkSign(string $msg, string $nonce, string $signature): bool
    {
        $token = config('services.nb_manual_alarm.key');

        $sign = base64_encode(md5($token . $nonce . $msg, true));

        Log::info('sign:' . $sign);
        Log::info('signature:' . $signature);

        // 验证token
        if ($signature === $sign) {
            return true;
        }
        return false;
    }

    protected function aesDecrypt($encryptedData)
    {
        return openssl_decrypt($encryptedData, 'AES-128-CBC', env('NB_KEY'), OPENSSL_RAW_DATA, config('services.nb_manual_alarm.key'));
    }

    /**
     * 获取设备缓存命令并下发
     * @param $imei
     * @param string $msgId
     * @return void
     */
    public function getAndSendDeviceCacheCMD($imei, string $msgId = '', $sleepSecond = 1)
    {
        DeviceCacheCommands::query()
            ->where('imei', $imei)
            ->where('is_success', 0)
            ->get()
            ->each(function ($item) use ($msgId, $sleepSecond) {
                sleep($sleepSecond);
                // 下发命令
                $res = (new OneNet())->callService(json_decode($item->json));
                Log::info('消音返回:' . json_encode($res));
                if ($res['code'] == 0) {
                    $item->is_success = 1;
                    $item->msg_id     = $msgId;
                    $item->save();
                }
            });
    }

    /**
     * 插入设备缓存命令
     * @param $imei
     * @param $cmdJson
     * @return mixed
     */
    public function insertDeviceCacheCMD($imei, $cmdJson)
    {
        return DeviceCacheCommands::query()->insert([
            'imei'       => $imei,
            'json'       => $cmdJson,
            'type'       => 1, // 1:消音 2:解除消音...暂时写死
            'is_success' => 0, // 设定是否成功
            'created_at' => now(),
        ]);
    }

    public function getInnoType($alarmCode, $alarmCodes)
    {
        $innoType = 0;
        foreach ($alarmCodes as $key => $value) {
            if ($key == $alarmCode) {
                $innoType = $value['iono_type'];
                break;
            }
        }
        return $innoType;
    }

    public function insertIOT($data, $imei, $infoType, $ionoPlatform = 'ONENET')
    {
        // 属性插入smoke_detector表
        // 可变参数

        /*if ($infoType == 2) {
            // 事件上报不处理 todo
            $alarmCode = $data['params']['value']['alarmCode'] ?? 0;
            // inno_type 类型：0：无报警；1：烟雾报警；2：烟雾报警解除；3：温度报警；4：温度报警解除；5：烟感低电量报警；6：烟感低电量报警解除；7：NB低电量报警；8：NB低电量报警解除；9：烟雾传感器故障；10：烟雾传感器故障解除；11：温湿度传感器故障；12：温湿度传感器故障解除；13：自检测试开始；14：自检测试完成；15：防拆触发；16：防拆恢复；17：烟雾板连接断开；18：烟雾板连接恢复；；组包时高字节在前 ，低字节在后。201-电流过大；

            // 海曼告警码
            $alarmCodes = [
                0  => ['comment' => '正常', 'iono_type' => 0],
                1  => ['comment' => 'test（协议保留）', 'iono_type' => 0],
                2  => ['comment' => '自检', 'iono_type' => 13],
                3  => ['comment' => '自检完成', 'iono_type' => 14],
                4  => ['comment' => '烟雾告警', 'iono_type' => 1],
                5  => ['comment' => '烟雾告警解除', 'iono_type' => 2],
                6  => ['comment' => '温度告警', 'iono_type' => 3],
                7  => ['comment' => '温度告警解除', 'iono_type' => 4],
                8  => ['comment' => '防拆告警', 'iono_type' => 15],
                9  => ['comment' => '防拆告警解除', 'iono_type' => 16],
                11 => ['comment' => '设备低压', 'iono_type' => 7],
            ];
            $ionoType = $this->getInnoType($alarmCode, $alarmCodes);
        }*/
        Log::info('data:' . $infoType);
        $url = '';
        if ($infoType == 3) {
            // laravel事务代码
            try {
                DB::connection('mysql2')->transaction(function () use ($data, $imei, $ionoPlatform, &$url) {
                    $productId = 'E2dMYR85jh';
                    // 设备属性变更=>心跳包
                    $alarmStatus[] = 21; // 默认心跳包
                    $heartbeatTime = date("Y-m-d H:i:s.Y", (int) ($data['msg']['data']['params']['heartbeat_time']['time'] ?? microtime()) / 1000);
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
                    $ionoAlarmSta  = decbin((int) ($data['msg']['data']['params']['alarmSta']['value'] ?? 0));
                    $ionoAlarmStas = [
                        // ['comment', 'iono_type'],
                        ['保留', 0],
                        ['自检', 13],
                        ['烟雾告警', 1],
                        ['高温告警', 3],
                        ['防拆告警', 15],
                        ['低压', 7],
                    ];
                    // ionoAlarmSta 反转后，对比$ionoAlarmStas，根据bit生成对应状态
                    $ionoAlarmSta = strrev($ionoAlarmSta);
                    // $ionoAlarmSta = '000';
                    if ((int) $ionoAlarmSta != '0') {
                        $alarmStatus = [];
                    }
                    // 把字符串转换为数组
                    $ionoAlarmSta = str_split($ionoAlarmSta);
                    // dd($ionoAlarmSta);
                    foreach ($ionoAlarmSta as $key => $value) {
                        if ($value == 1) {
                            $alarmStatus[] = $ionoAlarmStas[$key][1];// 暂时保留
                            break;
                        }
                    }
                    $smdeId = DB::connection('mysql2')->table('smoke_detector')->where('smde_imei', $imei)->value('smde_id') ?: 0;
                    if (empty($smdeId)) {
                        return;
                    }
                    // dd($alarmStatus);
                    DB::connection('mysql2')->table('smoke_detector')->where('smde_imei', $imei)->update([
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
                    ]);

                    foreach ($alarmStatus as $ionoType) {
                        Log::info('data:innotype:' . $ionoType);

                        $insertData = [
                            'iono_platform'                    => $ionoPlatform,
                            'iono_body'                        => json_encode($data),
                            'iono_timestamp'                   => time(),
                            'iono_msg_at'                      => $data['time'] ?? time(),
                            'iono_msg_imei'                    => $ionoIMEI ?? '',
                            'iono_msg_type'                    => 1,
                            'iono_nonce'                       => $data['nonce'] ?? '',
                            'iono_threshold_smoke_scope'       => $ionoThresholdSmokeScope ?? '',
                            'iono_threshold_temperature'       => $ionoThresholdTemperature ?? '',
                            // 'iono_threshold_humidity'          => '1234567890',
                            // 'iono_humidity'                    => '1234567890',
                            'iono_threshold_nb_module_battery' => $ionoThresholdNbModuleBattery ?? '',
                            'iono_smoke_scope'                 => $ionoSmokeScope ?? '',
                            'iono_temperature'                 => $ionoTemperture * 100 ?? '',
                            'iono_smoke_module_battery'        => $ionoBattery ?? '',
                            'iono_nb_module_battery'           => $ionoBattery ?? '',
                            'iono_type'                        => $ionoType ?? '',
                            'iono_imei'                        => $imei,
                            'iono_imsi'                        => $ionoIMSI ?? '',
                            'iono_maze_pollution'              => $ionoMazePollution ?? '',
                            'iono_nb_iccid'                    => $ionoICCID ?? '',
                            'iono_rsrp'                        => $ionoRsrp ?? '',
                            'iono_rsrq'                        => $ionoRsrq ?? '',
                            'iono_snr'                         => $ionoSnr ?? '',
                            'iono_alert_status'                => '-1',
                            'iono_category'                    => '烟感',
                            'iono_status'                      => '待处理',
                            'iono_smde_id'                     => $smdeId,
                            'iono_crt_time'                    => date("Y-m-d H:i:s.u"), // like 2025-01-09 16:38:45.098261
                            'iono_alert_status'                => -1,
                            'iono_device_status'               => -1,
                            'iono_product_id'                  => $productId,
                        ];

                        $ionoId = DB::connection('mysql2')->table('iot_notification')->insertGetId($insertData);


                        $insertData['iono_id'] = $ionoId;
                        $orderId               = DB::connection('mysql2')->table('smoke_detector')->where('smde_imei', $imei)->value('smde_order_id');

                        if (!empty($orderId)) {
                            // 自检
                            if ($ionoType == 21 || $ionoType == 13) {
                                DB::connection('mysql2')->table('iot_notification_self_check')->insert($insertData);
                            } else {
                                // or报警
                                DB::connection('mysql2')->table('iot_notification_alert')->insert($insertData);
                                $url = 'https://pingansuiyue.crzfxjzn.com/async.php?oper=send_alert&iono_id=' . $ionoId;
                            }
                        }
                        // return $ionoId;
                    }
                });
                // 发送告警电话和短信
                if(!empty($url)){
                    Http::withOptions(['verify' => false])->get($url);
                }
            } catch (Exception $e) {
                // 在异常情况下报错
                Log::info('海曼4g 移动 Transaction failed:' . $e->getLine() . ':' . $e->getMessage());
                // throw new Exception('海曼4g 移动 Transaction failed' . $e->getLine() . ':' . $e->getMessage());
            }
        }
    }
}
