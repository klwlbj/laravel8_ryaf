<?php

namespace App\Http\Controllers;

use App\Utils\Tools;
use App\Utils\LiuRui;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiuRuiController
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
        $params = Tools::jsonDecode($params);
        $util   = new LiuRui();

        if (isset($params['messageType']) && $params['messageType'] == 'dataReport' && isset($params['payload']['APPdata'])) {
            $decodeCode = base64_decode($params['payload']['APPdata']);
            try {
                $data                   = $util->toDecrypt($decodeCode);
                $params['analyze_data'] = $data;
            } catch (\Exception $e) {
                Log::channel('liurui')->info('liurui analyze exception: ' . $e->getMessage() . ' this json:' . json_encode($params));
            }
        }

        Log::channel('liurui')->info('liurui analyze data: ' . json_encode($params));

        return response()->json(['code' => 0, 'message' => 0]);
    }

    public function oneNetReport(Request $request)
    {
        $params        = $request->all();
        $params        = Tools::jsonDecode($params);
        $util          = new LiuRui();
        $params['msg'] = Tools::jsonDecode($params['msg']);

        if ($params['msg'] && isset($params['msg']['value'])) {
            //            $decodeCode = base64_decode($msg['value']);
            try {
                $data                          = $util->toDecrypt($params['msg']['value']);
                $params['msg']['analyze_data'] = $data;
            } catch (\Exception $e) {
                Log::channel('liurui_ontnet')->info('liurui_ontnet analyze exception: ' . $e->getMessage());
            }
        }

        Log::channel('liurui_ontnet')->info('liurui_ontnet analyze data: ' . json_encode($params));

        return $params['msg'];
        // return response()->json(['code' => 0, 'message' => 0]);
    }

    public function mufflingByOneNet($imei)
    {
        return (new LiuRui())->mufflingByOneNet($imei);
    }
}
