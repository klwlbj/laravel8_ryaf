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

        #4g设备
        $res = Aep_device_command::CreateCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $masterKey,
            json_encode([
                "content"   => [
                    'params' => [
                        'Smoke_Alarm_Value' => $alarmValue
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
}
