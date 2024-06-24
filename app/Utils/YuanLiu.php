<?php

namespace App\Utils;

use App\Utils\Apis\Aep_device_command;

class YuanLiu
{
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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

        #4g设备
        $res = Aep_device_command::CreateCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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

    public function setDetectionTime($productId, $deviceId, $masterKey, $time)
    {
        $res = Aep_device_command::CreateCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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

    public function setSilencing($productId, $deviceId, $masterKey, $state)
    {
        $res = Aep_device_command::CreateCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
}
