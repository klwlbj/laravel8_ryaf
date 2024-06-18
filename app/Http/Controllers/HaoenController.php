<?php

namespace App\Http\Controllers;

use App\Utils\CTWing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HaoenController extends BaseController
{
    public function createCmdCommand($productId, $deviceId, $masterKey, $cmdType)
    {
        $client = new CTWing();
        return $client->createCmdCommand($productId, $deviceId, $masterKey, $cmdType);
    }

    /**
     * 豪恩声光报警测试回调地址
     * @param Request $request
     * @return void
     */
    public function haoenSoundLigntAlarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('haoenCtwing:' . json_encode($jsonData));

        return response('', 200);
    }

    /**
     * 豪恩手报报警测试回调地址（暂时仅对接电信）
     * @param Request $request
     * @return void
     */
    public function haoenManualAlarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('haoen2Ctwing:' . json_encode($jsonData));

        return response('', 200);
    }
}
