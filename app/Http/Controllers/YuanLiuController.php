<?php

namespace App\Http\Controllers;

use App\Utils\Tools;
use App\Utils\YuanLiu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YuanLiuController extends BaseController
{
    protected $productIdByOneNet = 'HzFl9NvY5q';

    public function report(Request $request){
        $params = $request->all();

        $params = Tools::jsonDecode($params);
//        Tools::writeLog('params:','yuanliu',$params);
        try {
            #心跳包
            if (isset($params['serviceId']) && in_array($params['serviceId'],['ruyue_heartbeat','heartbeat'])) {
                $version = substr($params['payload']['software_version'],-3);
                if($version > 101 && $params['serviceId'] == 'ruyue_heartbeat'){
                    $imei = $params['payload']['IMEI'];
                }elseif ($version <= 101 && $params['serviceId'] == 'heartbeat'){
                    $imei = $params['payload']['IMEI'];
                }

            } elseif (isset($params['deviceSn']) && !empty($params['deviceSn'])) { // 如果存在设备编号
                $imei = $params['deviceSn'];
            } elseif (isset($params['IMEI']) && !empty($params['IMEI'])) { // 如果存在设备IMEI
                $imei = $params['IMEI'];
            } else {
                $imei = '';
            }

            if (isset($params['payload']) && !empty($params['payload'])) {
                $params['analyze_data'] = $params['payload'];
                unset($params['payload']);
            }

            if (isset($params['eventContent']) && !empty($params['eventContent'])) {
                $params['analyze_data'] = $params['eventContent'];
                unset($params['eventContent']);
            }

            if (isset($params['analyze_data'])) {
                if ($params['serviceId'] == 'smoke_state_report') { #烟雾状态上报
                    if ($params['analyze_data']['smoke_state'] == 0) {
                        $params['analyze_data']['cmd_type'] = 'CMD_FIRE';
                    } elseif ($params['analyze_data']['smoke_state'] == 1) {
                        $params['analyze_data']['cmd_type'] = 'CMD_FIRE_RM';
                    } else {
                        $params['analyze_data']['cmd_type'] = 'CMD_SELF_CHECK';
                    }
                } elseif ($params['serviceId'] == 'tamper_report') {
                    if ($params['analyze_data']['tamper_alarm'] == 0) {
                        $params['analyze_data']['cmd_type'] = 'CMD_FIX_ON';
                    } else {
                        $params['analyze_data']['cmd_type'] = 'CMD_PULL_DOWN';
                    }
                } elseif (in_array($params['serviceId'],['heartbeat','ruyue_heartbeat'])) { #心跳包
                    $params['analyze_data']['cmd_type'] = 'CMD_BEAT';
                    $version = substr($params['analyze_data']['software_version'],-3);

                    if($version <= 101){
                        #第一版
                        if(isset($params['analyze_data']['rsrp']) && is_numeric($params['analyze_data']['rsrp'])){
                            $params['analyze_data']['rsrp'] = $params['analyze_data']['rsrp'] / 10;
                        }
                        $params['analyze_data']['High_Temperature'] = 0;
                        $params['analyze_data']['Smoke_Alarm_Value'] = 0;
                        $params['analyze_data']['humility'] = 0;
                        $params['analyze_data']['smoke_concentration'] = 0;
                        $params['analyze_data']['temperature'] = 0;
                        $params['analyze_data']['signalPower'] = $params['analyze_data']['rsrp'];
                    }else{
                        #第二版
                        #处理烟雾阈值转换
                        if(isset($params['analyze_data']['Smoke_Alarm_Value'])){
                            $params['analyze_data']['Smoke_Alarm_Value'] = (new YuanLiu())->convertDbm($params['analyze_data']['Smoke_Alarm_Value']);
                        }

                        if(isset($params['analyze_data']['rsrp'])){
                            $params['analyze_data']['RSSI'] = ($params['analyze_data']['rsrp'] ?: 0) + 10;
                        }

                        if(isset($params['analyze_data']['RSRQ'])){
                            $params['analyze_data']['RSRQ'] = $params['analyze_data']['RSRQ']/2 - 19.5;
                        }

                        $params['analyze_data']['signalPower'] = $params['analyze_data']['rsrp'];

                        if(!empty($imei)){
                            DB::connection('mysql2')->table('smoke_detector')
                                ->where('smde_imei',$imei)
                                ->update([
                                    'smde_last_smokescope' => floatval($params['analyze_data']['smoke_concentration']) * 100,
                                    'smde_last_temperature' => floatval($params['analyze_data']['temperature']) * 100,
                                    'smde_last_humidity' => $params['analyze_data']['humility'],
//                                    'smde_last_signal_intensity' => $params['analyze_data']['rsrp'],
                                    'smde_last_maze_pollution' => $params['analyze_data']['Maze_pollution_level'],
                                    'smde_threshold_smoke_scope' => floatval($params['analyze_data']['Smoke_Alarm_Value']) * 100,
                                    'smde_threshold_temperature' => floatval($params['analyze_data']['High_Temperature']) * 100,
                                ]);
                        }
//                        $params['analyze_data']['signal_score'] = (new YuanLiu())->calculateSignalScore($params['analyze_data']['RSSI'],$params['analyze_data']['CSQ'],$params['analyze_data']['RSRQ'],$params['analyze_data']['rsrp']);

                    }
                } elseif ($params['serviceId'] == 'muffling_report') { #消音
                    $params['analyze_data']['cmd_type'] = 'CMD_MUFFLING';
                } elseif ($params['serviceId'] == 'temperature_report') { #消音
                    if ($params['analyze_data']['temperature_state'] == 0) {
                        $params['analyze_data']['cmd_type'] = 'CMD_TEMPERATURE_RM';
                    } else {
                        $params['analyze_data']['cmd_type'] = 'CMD_TEMPERATURE';
                    }

                }
            }
        } catch (\Exception $e) {
            Tools::writeLog('' . $e->getMessage() .$e->getLine() . ' this json:','yuanliu_exception',$params,'exception');
        }


        if(!empty($imei) && isset($params['analyze_data'])){
            Tools::deviceLog('aep',$imei,'yuanliu',$params);
        }else{
//            Tools::writeLog('this json:','yuanliu_ignore',$params,'exception');
        }

        return response()->json(['code' => 0, 'message' => 0]);
    }

    public function oneNetReport(Request $request)
    {
        $params = $request->all();

        $params = Tools::jsonDecode($params);
        if(!isset($params['msg'])){
            return true;
        }

        $params['msg'] = Tools::jsonDecode($params['msg']);
//        Tools::writeLog('$params','yuanliutest',$params);
        $yuanLiu = (new YuanLiu());
        try {
            $imei = $params['msg']['deviceName'] ?? '';

            if($imei == 863705079999280){
                return $params['msg'];
            }
            $params = $yuanLiu->handleOneNetReport($params);
            if(!empty($imei) && !empty($params['msg']['analyze_data'])){
                $arr = [
                    'CMD_BEAT' => 'heartbeat',
                    'CMD_FIRE' => 'smoke_state_report',
                    'CMD_FIRE_RM' => 'smoke_state_report',
                    'CMD_SELF_CHECK' => 'smoke_state_report',
                    'CMD_TEMPERATURE' => 'temperature_report',
                    'CMD_TEMPERATURE_RM' => 'temperature_report',
                    'CMD_FIX_ON' => 'tamper_report',
                    'CMD_PULL_DOWN' => 'tamper_report'
                ];

                $aepData = [
                    'upPacketSN' => -1,
                    "upDataSN" => -1,
                    'topic' => -1,
                    'timestamp' => time() . '000',
                    'tenantId' => null,
                    'serviceId' => $arr[$params['msg']['analyze_data']['cmd_type'] ?? ''] ?? '',
                    'messageType' => 'dataReport',
                    'deviceType' => null,
                    'assocAssetId' => null,
                    'deviceSn' => $imei,
                    'IMSI' => null,
                    'IMEI' => $imei,
                    'analyze_data' => $params['msg']['analyze_data'],
                ];
//                Tools::deviceLog('onenet',$imei,'yuanliutest',$aepData);
                Tools::deviceLog('onenet',$imei,'yuanliu',$aepData);
//                $commandList = $yuanLiu->getCommandByOneNet($imei);
//                if(!empty($commandList)){
//                    foreach ($commandList as $command){
//                        $yuanLiu->sendCommandByOneNet($imei,$command['identifier'],$command['params']);
//                    }
//                }
            }else{
                if(isset($params['msg']['type']) && isset($params['msg']['status']) && $params['msg']['status'] == 1 && isset($params['msg']['dev_name'])){
//                    $this->getAndSendDeviceCacheCMD($params['msg']['dev_name'],$params['id'] ?? '');
                }
            }
        } catch (\Exception $e) {
            Tools::writeLog('' . $e->getMessage() .$e->getLine() . ' this json:','yuanliu_exception',$params,'exception');
        }

        return $params['msg'];
    }

    public function muffling($productId, $deviceId, $masterKey)
    {
        return (new YuanLiu())->muffling($productId, $deviceId, $masterKey);
    }

    public function setThreshold($productId, $deviceId, $masterKey, $alarmValue)
    {
        return (new YuanLiu())->setThreshold($productId, $deviceId, $masterKey, $alarmValue);
    }

    public function setDetectionTime($productId, $deviceId, $masterKey, $time)
    {
        return (new YuanLiu())->setDetectionTime($productId, $deviceId, $masterKey, $time);
    }

    public function setSilencing($productId, $deviceId, $masterKey, $state)
    {
        return (new YuanLiu())->setSilencing($productId, $deviceId, $masterKey, $state);
    }

    public function setTempThreshold($productId, $deviceId, $masterKey, $value)
    {
        return (new YuanLiu())->setTempThreshold($productId, $deviceId, $masterKey, $value);
    }

    public function mufflingByOneNet($imei)
    {
        return $this->insertDeviceCacheCMD($imei,Tools::jsonEncode([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'cmd',
            'params' => [
                'muffling' => 1
            ]
        ]));
    }

    public function setThresholdByOneNet($imei,$alarmValue)
    {
        $realValue = (new YuanLiu())->convertValue($alarmValue);
        return $this->insertDeviceCacheCMD($imei,Tools::jsonEncode([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'Smoke_Value_down',
            'params' => [
                'Smoke_Alarm_Value' => $realValue
            ]
        ]));
    }

    public function setDetectionTimeByOneNet($imei,$time)
    {
        return $this->insertDeviceCacheCMD($imei,Tools::jsonEncode([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'Detection_Timer_down',
            'params' => [
                'Smoke_Detection_Timer' => $time
            ]
        ]));
    }

    public function setTempThresholdByOneNet($imei,$value)
    {
        return $this->insertDeviceCacheCMD($imei,Tools::jsonEncode([
            "device_name"        => $imei,
            "product_id"      => $this->productIdByOneNet,
            'identifier' => 'High_Temp_down',
            'params' => [
                'High_Temperature' => $value
            ]
        ]));
    }

    public function gasReport(Request $request)
    {
        $params = $request->all();
        Tools::writeLog('gasReport','yuanliugas',$params);

        return response()->json(['code' => 0, 'message' => 0]);
    }
}
