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

        #心跳包
        if(isset($params['serviceId']) && $params['serviceId'] == 'heartbeat'){
            $imei = $params['payload']['IMEI'];
        } elseif(isset($params['deviceSn']) && !empty($params['deviceSn'])){ // 如果存在设备编号
            $imei = $params['deviceSn'];
        }elseif(isset($params['IMEI']) && !empty($params['IMEI'])){ // 如果存在设备IMEI
            $imei = $params['IMEI'];
        }else{
            $imei = '';
        }

        if(isset($params['payload']) && !empty($params['payload'])){
            $params['analyze_data'] = $params['payload'];
        }

        if(isset($params['eventContent']) && !empty($params['eventContent'])){
            $params['analyze_data'] = $params['eventContent'];
        }

        if(!empty($imei)){
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
}
