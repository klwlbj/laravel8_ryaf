<?php

namespace App\Http\Controllers;

use App\Utils\LiuRui;
use App\Utils\Tools;
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

    public function muffling($productId, $deviceId, $masterKey){
        return (new LiuRui())->muffling($productId, $deviceId, $masterKey);
    }

    public function report(Request $request){
        $params = $request->all();

        $params = Tools::jsonDecode($params);
        $util = new LiuRui();

        if($params['messageType'] == 'dataReport' && isset($params['payload']['APPdata'])){
            $data = $util->toDecrypt(base64_decode($params['payload']['APPdata']));
            $params['analyze_data'] = $data;
        }

        Log::channel('liurui')->info('liurui analyze data: ' . json_encode($params));
        // ToolsLogic::writeLog('params','report',$params);

        return response()->json(['code' => 0,'message' => 0]);
    }
}
