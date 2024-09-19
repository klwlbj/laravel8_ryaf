<?php

namespace App\Http\Controllers;

use App\Utils\LiuRui;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Nonstandard\Uuid;

class LiuRuiCloudController
{
    public function toDecrypt(Request $request, string $string)
    {
        $util = new LiuRui();
        try {
            $data = $util->toDecrypt($string);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::channel('liurui')->info('liurui analyze exception: ' . $e->getMessage());
        }
    }

    public function muffling($productId, $deviceId, $masterKey)
    {
        return (new LiuRui())->muffling($productId, $deviceId, $masterKey);
    }

    public function report(Request $request)
    {
        $params = $request->all();
        //        print_r($params);die;
        $params = Tools::jsonDecode($params);

        if(isset($params['streamId']) && !in_array($params['streamId'],['heartbeat','integral','self_check','fireAlarm'])){
            return response()->json(['code' => 0, 'message' => '']);
        }

        $util   = new LiuRui();

        if (isset($params['rawData'])) {
            try {
                $data                   = $util->toDecrypt($params['rawData']);
                $params['analyze_data'] = $data;
                if(isset($params['analyze_data']['data']['smoke_concentration'])){
                    $params['analyze_data']['data']['sensitivity']['value'] = round($params['analyze_data']['data']['smoke_concentration']['value'] / 255,2);
                }
            } catch (\Exception $e) {
                Tools::writeLog('liurui analyze exception: ' . $e->getMessage() . ' this json:', 'liuruicloud_exception', $params, 'exception');
                //                Log::channel('liurui')->info('liurui analyze exception: ' . $e->getMessage() . ' this json:' . json_encode($params));
            }
        }

        if(isset($params['analyze_data']['cmd_type'])){
            $logFileName = date('YmdHis') . '-' . $params['ext']['IMEI'] . '-' . md5(Uuid::uuid4()->toString());
            Tools::writeLog('', 'liuruicloud', $params, $logFileName, '%message%%context% %extra%');
        }

        return response()->json(['code' => 0, 'message' => 0]);
    }
}
