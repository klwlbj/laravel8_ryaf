<?php

namespace App\Http\Controllers;

use App\Utils\DaHua;
use App\Utils\CTWing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DaHuaController extends BaseController
{
    public array $heartbeat = [
        "battery_voltage"   => 3.140000104904175, // 电池电压
        "alarm_value"       => 399, // 报警灵敏度
        "temperature_value" => 0, // 温度探测值
        "sensor_value"      => 22, // 蓝光采样值
        "ecl"               => 0, // ecl
        "signalPower"       => -775, // 信号强度
        "humidity_value"    => 0, //湿度检测值
        "snr"               => 123, // 信噪比
        "pci"               => 212, // 物理小区标识
        "operation"         => 1, // 操作上报, 枚举值 : 1--自检 2--屏蔽 3--解除屏蔽 4--上电复位 5--消音
        "cell_ID"           => 123018322, // 小区位置信息
        "battery_value"     => 100, // 电池电量
        "sensor_redvalue"   => 870, // 红光采样值
        "timestamp"         => 1744095949839,
    ];

    public function analyze(string $string)
    {
        $util = new DaHua();
        return $util->parseString(strtolower(trim(urldecode($string))));
    }

    public function analyze2(string $string)
    {
        $spaced_string = chunk_split($string, 2, ' ');
        echo $spaced_string;
    }

    public function analyze3($string)
    {
        $util = new DaHua();

        // 处理请求
        echo chunk_split($util->createCmd($string), 2, ' ');
    }

    /**
     * 大华报警回调
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function dhCTWingWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::channel('dahua')->info('dhctwingWarm:' . json_encode($jsonData));

        return response('', 200);
    }

    /**
     * 大华雷达报警回调
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function dhCTWingRadarWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::channel('dahua')->info('dhctwingRadarWarm:' . json_encode($jsonData));

        if (isset($jsonData['payload']) || isset($jsonData['moduleParams']) || isset($jsonData['eventContent'])) {
            $imei       = $jsonData['IMEI']; // 设备imei
            $deviceId   = $jsonData['deviceId'] ?? '';
            $decodedMsg = $jsonData['payload'] ?? ($jsonData['moduleParams'] ?? $jsonData['eventContent']);

            $this->insertRadarWarm($decodedMsg, $jsonData, $imei, $deviceId);
        }
        // Log::channel('dahua')->info("大华电信雷达:" . json_encode($jsonData));

        return response('', 200);
    }

    public function insertRadarWarm($decodedMsg, $jsonData, $imei, $deviceId)
    {
        // 设备属性变更
        $time      = time();
        $productId = 17207128;

        $alarmStatus    = []; // 默认心跳包
        $heartbeatTime  = date("Y-m-d H:i:s.Y", (int) ($data['timestamp'] ?? microtime()) / 1000);
        $ionoSmokeScope = percentToDbm($decodedMsg['smoke_value'] ?? 0) * 100;
        $ionoIMSI       = $jsonData['IMSI'] ?? '';

        // $ionoBatteryVoltage = $decodedMsg['battery_voltage'] ?? '';
        $ionoTemperture = $decodedMsg['temperature_value'] ?? 0;
        $ionoBattery    = $decodedMsg['battery_value'] ?? '';
        $ionoIMEI       = $imei;
        $ionoSnr        = $decodedMsg['snr'] ?? '';
        $ionoHumidity   = $decodedMsg['humidity_value'] ?? '';
        $ionoPlatform   = 'CTWING_AEP';
        $ionoICCID      = $decodedMsg['iccid'] ?? '';
        $ionoRsrp       = $decodedMsg['rsrp'] ?? '';

        if (!empty($decodedMsg['smoke_state'])) {
            $alarmStatus[] = 1; // 烟感报警
        }
        if (!empty($decodedMsg['temperature_state'])) {
            $alarmStatus[] = 3; // 温度报警
        }
        if (!empty($decodedMsg['humidity_state'])) {
            // 湿度报警 todo
        }
        if (!empty($decodedMsg['move_state'])) {
            $alarmStatus[] = 101; // 移动报警
        }
        if (empty($alarmStatus)) {
            $alarmStatus[] = 0; // 默认心跳包
        }
        $this->insertWarm($heartbeatTime, $ionoHumidity, $ionoSmokeScope, $ionoRsrp, $ionoTemperture, $ionoIMSI, $ionoBattery, $ionoICCID, $ionoPlatform, $jsonData, $time, $ionoIMEI, $imei, $ionoSnr, $productId, $alarmStatus, '', $deviceId);
    }

    private function insertWarm($heartbeatTime, $ionoHumidity, $ionoSmokeScope, $ionoRsrp, $ionoTemperture, $ionoIMSI, $ionoBattery, $ionoICCID, $ionoPlatform, $data, $time, $ionoIMEI, $imei, $ionoSnr, $productId, $alarmStatus, string $ionoMsgValueHex = '', string $deviceId = '')
    {
        $deviceUpdateData = [
            'smde_last_heart_beat'           => $heartbeatTime,
            'smde_last_smokescope'           => $ionoSmokeScope,
            'smde_last_signal_intensity'     => $ionoRsrp,
            'smde_last_humidity'             => $ionoHumidity,
            'smde_last_temperature'          => (int) $ionoTemperture * 100,
            'smde_nb_iid'                    => $ionoIMSI,
            'smde_last_smoke_module_battery' => $ionoBattery,
            'smde_last_nb_module_battery'    => $ionoBattery,
            'smde_nb_iid2'                   => $ionoICCID,
            'smde_online'                    => 1,
            'smde_online_real'               => 1,
            'smde_iot_platform'              => $ionoPlatform,
            'smde_ctwing_device_id'          => $deviceId, // 针对电信平台
        ];
        $notificationInsertData = [
            'iono_platform'             => $ionoPlatform,
            'iono_body'                 => json_encode($data),
            'iono_timestamp'            => $time,
            'iono_msg_at'               => $data['time'] ?? $time,
            'iono_msg_imei'             => $ionoIMEI ?? '',
            'iono_msg_type'             => 1,
            'iono_nonce'                => $data['nonce'] ?? '',
            'iono_smoke_scope'          => $ionoSmokeScope ?? '',
            'iono_temperature'          => $ionoTemperture * 100 ?? '',
            'iono_smoke_module_battery' => $ionoBattery ?? '',
            'iono_nb_module_battery'    => $ionoBattery ?? '',
            // 'iono_type' => $ionoType ?? '',
            'iono_imei'                 => $imei,
            'iono_imsi'                 => $ionoIMSI ?? '',
            'iono_maze_pollution'       => $ionoMazePollution ?? '',
            'iono_nb_iccid'             => $ionoICCID ?? '',
            'iono_rsrp'                 => $ionoRsrp ?? '',
            'iono_rsrq'                 => $ionoRsrq ?? '',
            'iono_snr'                  => $ionoSnr ?? '',
            'iono_category'             => '烟感',
            'iono_status'               => config('alarm_setting.other_alarm.status'),
            // 'iono_smde_id' => $smdeId,
            'iono_crt_time'             => date("Y-m-d H:i:s.u"), // like 2025-01-09 16:38:45.098261
            'iono_alert_status'         => -1,
            'iono_device_status'        => -1,
            'iono_product_id'           => $productId,
            'iono_msg_value_hex'        => $ionoMsgValueHex,
            // 'iono_type_list'            => $alarmStatus,
        ];

        Log::channel('dahua')->info('大华雷达烟感insert' . json_encode($notificationInsertData));

        // return;

        $this->insertIOT($deviceUpdateData, $notificationInsertData, $alarmStatus, $imei);
    }

    // 消音
    public function muffling($productId, $imei, $masterKey)
    {
        $client = new CTWing();
        // 通过imei查询deviceId todo
        $deviceId = 'c6cf6f487cd24e8cbd3bed7daf0f4dbf';// 先写死$deviceId

        return $client->createNTTMufflingCommand($productId, $deviceId, $masterKey, 120);
    }

    // 布防时间设置
    public function deploymentTimeSetting($productId, $imei, $masterKey)
    {
        $client = new CTWing();
        // 通过imei查询deviceId todo
        $deviceId = 'c6cf6f487cd24e8cbd3bed7daf0f4dbf';// 先写死$deviceId

        $serviceIdentifier = 'Conf_Ex';
        $params            = [
            'cmd_EX'  => "1",
            'Opt_EX'  => "1",
            'para_EX' => '01001a1a18001a1a18001a1a18001a1a18001a1a18001a1a18001a1a18',
        ];

        return $client->createCustomCommand($productId, $deviceId, $masterKey, $serviceIdentifier, $params);
    }

    // 灵敏度设置
    public function sensitivityStting($productId, $imei, $masterKey)
    {
        $client = new CTWing();
        // 通过imei查询deviceId todo
        $deviceId = 'c6cf6f487cd24e8cbd3bed7daf0f4dbf';// 先写死$deviceId

        $serviceIdentifier = 'Conf_Ex';
        $params            = [
            'cmd_EX'  => "2",
            'Opt_EX'  => "1",
            'para_EX' => '00', // 00高，01中，02低
        ];

        return $client->createCustomCommand($productId, $deviceId, $masterKey, $serviceIdentifier, $params);
    }

    public function insertRadarDetector(string $imei)
    {
        // return;
        $smde_type       = "雷达烟感";
        $smde_brand_name = "大华";
        $smde_model_name = "DH-HY-SAR4NA";
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

    // 将百分比转换为dBm
    public function percentToDbm($percent)
    {
        if ($percent === 100) {
            return 100;
        }
        return 10 * log10(1 / (1 - $percent / 100));
    }
}
