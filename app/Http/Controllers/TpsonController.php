<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Server\TpsonServer;
use Illuminate\Support\Facades\Log;

class TpsonController extends BaseController
{
    public function data(Request $request)
    {
        $jsonData = $request->all();
        Log::info('Tpson data' . url()->current() . json_encode($jsonData));
        (new TpsonServer())->createDeviceLastData($jsonData);
        return response('', 200);
    }

    public function alarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('Tpson alarm' . url()->current() . json_encode($jsonData));
        (new TpsonServer())->createNotification($jsonData);
        return response('200', 200);
    }

    // 手动导入设备
    public function importDevice($imei, $nodeId)
    {
        return (new TpsonServer())->createDevice($imei, $nodeId);
    }
}
