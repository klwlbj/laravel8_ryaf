<?php

namespace App\Http\Controllers;

use App\Utils\Apis\Aep_device_event;

class newController extends BaseController
{
    public static function Demo()
    {
        $result = Aep_device_event::QueryDeviceEventList(env('CTWING_KEY'), env('CTWING_SECRET'), env('CTWING_MASTER_KEY'), json_encode([
            "productId" => "16908680", //必填
            "deviceId"  => "c6951ad66e004229896dee108e7193ff", //必填
            "startTime" => time() * 1000, //必填
            "endTime"   => time() * 1000 + 3600, //必填
            "pageSize"  => 10, //必填
        ]));
        echo("result = " . $result . "\n");
    }
}
