<?php

namespace App\Http\Controllers;

use App\Utils\Tools;
use App\Utils\YuanLiu;
use Illuminate\Http\Request;

class YuanLiuController
{
    public function report(Request $request){
        $params = $request->all();

        $params = Tools::jsonDecode($params);

        try {
            #心跳包
            if (isset($params['serviceId']) && $params['serviceId'] == 'heartbeat') {
                $imei = $params['payload']['IMEI'];
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
                } elseif ($params['serviceId'] == 'heartbeat') { #心跳包
                    $params['analyze_data']['cmd_type'] = 'CMD_BEAT';
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
        }

        return response()->json(['code' => 0, 'message' => 0]);
    }

    public function muffling($productId, $deviceId, $masterKey)
    {
        return (new YuanLiu())->muffling($productId, $deviceId, $masterKey);
    }

    public function setThreshold($productId, $deviceId, $masterKey, $alarmValue)
    {
        return (new YuanLiu())->setThreshold($productId, $deviceId, $masterKey, $alarmValue);
    }

    public function setDetectionTime($productId, $deviceId, $masterKey, $alarmValue)
    {
        return (new YuanLiu())->setDetectionTime($productId, $deviceId, $masterKey, $alarmValue);
    }

    public function setSilencing($productId, $deviceId, $masterKey, $state)
    {
        return (new YuanLiu())->setSilencing($productId, $deviceId, $masterKey, $state);
    }
}
