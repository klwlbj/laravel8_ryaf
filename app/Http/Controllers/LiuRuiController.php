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
        $data = $util->toDecrypt($string);
        return response()->json($data);
    }

    public function muffling($productId, $deviceId, $masterKey)
    {
        return (new LiuRui())->muffling($productId, $deviceId, $masterKey);
    }

    public function report(Request $request)
    {
        $params = $request->all();
        $params = Tools::jsonDecode($params);
        $util = new LiuRui();

        if (isset($params['messageType']) && $params['messageType'] == 'dataReport' && isset($params['payload']['APPdata'])) {
            $decodeCode = base64_decode($params['payload']['APPdata']);
            try {
                $data                   = $util->toDecrypt($decodeCode);
                $params['analyze_data'] = $data;
            } catch (\Exception $e) {
                Log::channel('liurui')->info('liurui analyze exception: ' . $e->getMessage());
            }
        }

        Log::channel('liurui')->info('liurui analyze data: ' . json_encode($params));

        return response()->json(['code' => 0, 'message' => 0]);
    }

    public function oneNetReport(Request $request){
        $params = $request->all();
        $params = Tools::jsonDecode($params);
        $util  = new LiuRui();
        $msg = $params['msg'] ?? [];
        if($msg && isset($msg['value'])){
            $decodeCode = base64_decode($msg['value']);
            try {
                $data                   = $util->toDecrypt($decodeCode);
                $msg['analyze_data']    = $data;
            } catch (\Exception $e) {
                Log::channel('liurui_ontnet')->info('liurui_ontnet analyze exception: ' . $e->getMessage());
            }
        }

        Log::channel('liurui_ontnet')->info('liurui_ontnet analyze data: ' . $msg);

        return $params['msg'];
        // return response()->json(['code' => 0, 'message' => 0]);
    }

    public function mufflingByOneNet($imei)
    {
        return (new LiuRui())->mufflingByOneNet($imei);
    }
}
