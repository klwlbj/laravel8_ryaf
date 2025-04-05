<?php

namespace App\Utils;

use App\Http\Logic\ToolsLogic;
use App\Utils\Apis\Aep_device_command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class YuanLiu
{
    #阈值对照表
    public $thresholdArr = [
        ["dbm" => 0.239, "value" => 1500],
        ["dbm" => 0.257, "value" => 1600],
        ["dbm" => 0.274, "value" => 1700],
        ["dbm" => 0.291, "value" => 1800],
        ["dbm" => 0.308, "value" => 1900],
        ["dbm" => 0.325, "value" => 2000],
        ["dbm" => 0.342, "value" => 2100],
        ["dbm" => 0.346, "value" => 2200],
        ["dbm" => 0.351, "value" => 2300],
        ["dbm" => 0.368, "value" => 2400],
        ["dbm" => 0.385, "value" => 2500],
        ["dbm" => 0.403, "value" => 2600],
        ["dbm" => 0.420, "value" => 2700],
        ["dbm" => 0.437, "value" => 2800],
        ["dbm" => 0.455, "value" => 2900],
        ["dbm" => 0.472, "value" => 3000],
        ["dbm" => 0.490, "value" => 3100],
        ["dbm" => 0.496, "value" => 3200],
        ["dbm" => 0.501, "value" => 3300],
        ["dbm" => 0.528, "value" => 3400],
        ["dbm" => 0.554, "value" => 3500],
    ];

    public function convertDbm($number){
        $realDbm = null;
        foreach ($this->thresholdArr as $key => $value) {
            if($value['value'] >= $number){
                $realDbm = $value['dbm'];
                break;
            }
        }

        if(empty($realDbm)){
            $realDbm = $this->thresholdArr[count($this->thresholdArr) - 1]['value'];
        }

        return $realDbm;
    }

    public function convertValue($alarmValue)
    {
        $realValue = null;
        foreach ($this->thresholdArr as $key => $value) {
            if($value['dbm'] >= $alarmValue){
                $realValue = $value['value'];
                break;
            }
        }

        if(empty($realValue)){
            $realValue = $this->thresholdArr[count($this->thresholdArr) - 1]['value'];
        }

        return $realValue;
    }

    /**计算信号得分
     * @param $rssi
     * @param $csq
     * @param $rsrq
     * @param $rsrp
     * @return float
     */
    public function calculateSignalScore($rssi, $csq, $rsrq, $rsrp)
    {
        #先把数据归一
        $nRssi = ($rssi - (-120)) / ((-30) - (-120));
        $nCsq = $csq / 31;
        $nRsrq = ($rsrq - (-20)) / ((-3) - (-20));
        $nRsrp = ($rsrp - (-140)) / ((-44) - (-140));

        print_r($nRssi . "\n");
        print_r($nCsq . "\n");
        print_r($nRsrq . "\n");
        print_r($nRsrp . "\n");

        $score = (0.25 * $nRssi) + (0.30 * $nCsq)  + (0.20 * $nRsrq) + (0.25 * $nRsrp);

        return $score;
    }

    public function handleOneNetReport($params)
    {
        $data = $params['msg'];
        $analyzeData = [];

        if(!isset($data['notifyType'])){
            return $params;
        }
        #心跳包
        if(isset($data['notifyType']) && $data['notifyType'] == 'property'){
            $analyzeData = [
                'terminal_type' => $data['data']['params']['terminal_type']['value'] ?? '',
                'ICCID' => $data['data']['params']['ICCID']['value'] ?? '',
                'battery_voltage' => $data['data']['params']['battery_voltage']['value'] ?? '',
                'Smoke_Alarm_Value' => $this->convertDbm($data['data']['params']['Smoke_Alarm_Value']['value'] ?? 0),
                'humility' => $data['data']['params']['humility']['value'] ?? '',
                'smoke_value' => $data['data']['params']['smoke_value']['value'] ?? '',
                'High_Temperature' => $data['data']['params']['High_Temperature']['value'] ?? '',
                'IMEI' => $data['data']['params']['IMEI']['value'] ?? '',
                'rsrp' => $data['data']['params']['rsrp']['value'] ?? '',
                'CSQ' => $data['data']['params']['CSQ']['value'] ?? '',
                'RSRQ' => $data['data']['params']['RSRQ']['value'] ?? '',
                'manufacturer_name' => $data['data']['params']['manufacturer_name']['value'] ?? '',
                'hardware_version' => $data['data']['params']['hardware_version']['value'] ?? '',
                'sinr' => $data['data']['params']['sinr']['value'] ?? '',
                'ecl' => $data['data']['params']['ecl']['value'] ?? '',
                'smoke_concentration' => $data['data']['params']['smoke_concentration']['value'] ?? '',
                'heartbeat_time' => $data['data']['params']['heartbeat_time']['value'] ?? '',
                'pci' => $data['data']['params']['pci']['value'] ?? '',
                'IMSI' => $data['data']['params']['IMSI']['value'] ?? '',
                'temperature' => $data['data']['params']['temperature']['value'] ?? '',
                'Maze_pollution_level' => $data['data']['params']['Maze_pollution_level']['value'] ?? '',
                'software_version' => $data['data']['params']['software_version']['value'] ?? '',
                'battery_value' => $data['data']['params']['battery_value']['value'] ?? '',
                'cell_ID' => $data['data']['params']['battery_value']['cell_ID'] ?? '',
                'cmd_type' => 'CMD_BEAT',
            ];

            if(!empty($analyzeData['RSRQ'])){
                $analyzeData['RSRQ'] = $analyzeData['RSRQ']/2 - 19.5;
            }

            $analyzeData['RSSI'] = ($analyzeData['rsrp'] ?: 0) + 10;

            $analyzeData['signalPower'] = $analyzeData['rsrp'];

            if(isset($data['deviceName']) && !empty($data['deviceName'])){
                DB::connection('mysql2')->table('smoke_detector')
                    ->where('smde_imei',$data['deviceName'])
                    ->update([
                        'smde_last_smokescope' => floatval($analyzeData['smoke_concentration']) * 100,
                        'smde_last_temperature' => floatval($analyzeData['temperature']) * 100,
                        'smde_last_humidity' => $analyzeData['humility'],
//                        'smde_last_signal_intensity' => $analyzeData['rsrp'],
                        'smde_last_maze_pollution' => $analyzeData['Maze_pollution_level'],
                        'smde_threshold_smoke_scope' => floatval($analyzeData['Smoke_Alarm_Value']) * 100,
                        'smde_threshold_temperature' => floatval($analyzeData['High_Temperature']) * 100,
                    ]);
            }


            $params['msg']['serviceId'] = 'heartbeat';
        }elseif ($data['notifyType'] == 'event' && isset($data['data']['params']['tamper_report'])){
            #防拆报警
            $analyzeData = $data['data']['params']['tamper_report']['value'] ?? [];
            if (isset($analyzeData['tamper_alarm']) && $analyzeData['tamper_alarm'] == 0) {
                $analyzeData['cmd_type'] = 'CMD_FIX_ON';
            } else {
                $analyzeData['cmd_type'] = 'CMD_PULL_DOWN';
            }

            $params['msg']['serviceId'] = 'tamper_report';
        }elseif ($data['notifyType'] == 'event' && isset($data['data']['params']['smoke_state_report'])){
            #火警或自检
            $analyzeData = $data['data']['params']['smoke_state_report']['value'] ?? [];
            if (isset($analyzeData['smoke_state']) && $analyzeData['smoke_state'] == 0) {
                $analyzeData['cmd_type'] = 'CMD_FIRE';
            } elseif (isset($analyzeData['smoke_state']) && $analyzeData['smoke_state'] == 1) {
                $analyzeData['cmd_type'] = 'CMD_FIRE_RM';
            }else{
                $analyzeData['cmd_type'] = 'CMD_SELF_CHECK';
            }

            $params['msg']['serviceId'] = 'smoke_state_report';
        }elseif ($data['notifyType'] == 'event' && isset($data['data']['params']['temperature_report'])){
            #温度报警
            $analyzeData = $data['data']['params']['temperature_report']['value'] ?? [];
            if (isset($analyzeData['temperature_state']) && $analyzeData['temperature_state'] == 0) {
                $analyzeData['cmd_type'] = 'CMD_TEMPERATURE_RM';
            }else{
                $analyzeData['cmd_type'] = 'CMD_TEMPERATURE';
            }

            $params['msg']['serviceId'] = 'temperature_report';
        }elseif ($data['notifyType'] == 'event' && isset($data['data']['params']['muffling_report'])){
            #消声
            $analyzeData = $data['data']['params']['muffling_report']['value'] ?? [];
            $analyzeData['cmd_type'] = 'CMD_MUFFLING';

            $params['msg']['serviceId'] = 'muffling_report';
        }


        unset($params['msg']['data']);
        $params['msg']['analyze_data'] = $analyzeData;
        return $params;
    }

    public function muffling($productId, $deviceId, $masterKey){
        #nb设备
//        $res = Aep_device_command::CreateCommand(
//            env('CTWING_KEY'),
//            env('CTWING_SECRET'),
//            $masterKey,
//            json_encode([
//                "content"   => [
//                    'params' => [
//                        'muffling' => 1
//                    ],
//                    'serviceIdentifier' => 'muffling_cmd'
//                ],
//                "deviceId"  => $deviceId,
//                "operator"  => "ryaf", // 操作者，暂时写死
//                "productId" => $productId,
//            ])
//        );

        #4G设备
        $res = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'params' => [
                        'muffling' => 1
                    ],
                    'serviceIdentifier' => 'cmd'
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
            ])
        );

        return $res;
    }

    /**设置烟雾阈值
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param $alarmValue
     * @return 返回响应：bool|null
     */
    public function setThreshold($productId, $deviceId, $masterKey, $alarmValue)
    {
        #nb设备
//        $res = Aep_device_command::CreateCommand(
//            env('CTWING_KEY'),
//            env('CTWING_SECRET'),
//            $masterKey,
//            json_encode([
//                "content"   => [
//                    'params' => [
//                        'alarm_value' => $alarmValue
//                    ],
//                    'serviceIdentifier' => 'alarm_thes_set'
//                ],
//                "deviceId"  => $deviceId,
//                "operator"  => "ryaf", // 操作者，暂时写死
//                "productId" => $productId,
//            ])
//        );

        $realValue = $this->convertValue($alarmValue);

        #4g设备
        $res = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'params' => [
                        'Smoke_Alarm_Value' => $realValue
                    ],
                    'serviceIdentifier' => 'Smoke_Value_down'
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
            ])
        );

        return $res;
    }

    /**设置报报警持续监测时间
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param $time
     * @return 返回响应：bool|null
     */
    public function setDetectionTime($productId, $deviceId, $masterKey, $time)
    {
        $res = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'params' => [
                        'Smoke_Detection_Timer' => $time
                    ],
                    'serviceIdentifier' => 'Detection_Timer_down'
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
            ])
        );

        return $res;
    }

    /**设置永久消音
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param $state
     * @return 返回响应：bool|null
     */
    public function setSilencing($productId, $deviceId, $masterKey, $state)
    {
        $res = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'params' => [
                        'Permanent_Silencing' => $state
                    ],
                    'serviceIdentifier' => 'Silencing_down'
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
            ])
        );

        return $res;
    }

    /**设置温度阈值
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param $value
     * @return 返回响应：bool|null
     */
    public function setTempThreshold($productId, $deviceId, $masterKey, $value)
    {
        $res = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'params' => [
                        'High_Temperature' => $value
                    ],
                    'serviceIdentifier' => 'High_Temp_down'
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
            ])
        );

        return $res;
    }

    protected $productIdByOneNet = 'HzFl9NvY5q';

    public function mufflingByOneNet($imei)
    {
        $ontNet = new OneNet();
        $res = $ontNet->callService([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'cmd',
            'params' => [
                'muffling' => 1
            ]
        ]);

        return $res;
    }

    public function setThresholdByOneNet($imei,$alarmValue)
    {
        $ontNet = new OneNet();

        $realValue = $this->convertValue($alarmValue);

        $res = $ontNet->callService([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'Smoke_Value_down',
            'params' => [
                'Smoke_Alarm_Value' => $realValue
            ]
        ]);

        return $res;
    }

    public function setDetectionTimeByOneNet($imei,$time)
    {
        $ontNet = new OneNet();
        $res = $ontNet->callService([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'Detection_Timer_down',
            'params' => [
                'Smoke_Detection_Timer' => $time
            ]
        ]);

        return $res;
    }

    public function setTempThresholdByOneNet($imei,$value)
    {
        $ontNet = new OneNet();
        $res = $ontNet->callService([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'High_Temp_down',
            'params' => [
                'High_Temperature' => $value
            ]
        ]);

        return $res;
    }

    public function getCommandByOneNet($imei)
    {
        return [];
        if(!in_array($imei,['863705079999249','867708075680744'])){
            return [];
        }
        $client = new Client(['verify' => false]);
        $response = $client->post(
            'https://pingansuiyue2.crzfxjzn.com/device/smokeDetector/getOneNetCommand',
            [
                'headers' => [

                ],
                'json'    => (object)[
                    'imei' => $imei
                ],
            ]);

        $res = $response->getBody()->getContents();

        $res = ToolsLogic::jsonDecode($res);

        if($res['code'] != 0){
            return [];
        }

        return $res['data'];
    }

    public function sendCommandByOneNet($imei,$identifier,$params)
    {
        $ontNet = new OneNet();
        $res = $ontNet->callService([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => $identifier,
            'params' => $params
        ]);
        Tools::writeLog('res：','yuanliutest',$res);
        return $res;
    }
}
