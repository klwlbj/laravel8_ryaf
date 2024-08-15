<?php

namespace App\Http\Controllers;

use App\Utils\LiuRui;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
//        $util   = new LiuRui();
//
//        if (isset($params['messageType']) && $params['messageType'] == 'dataReport' && isset($params['payload']['APPdata'])) {
//            $decodeCode = base64_decode($params['payload']['APPdata']);
//            try {
//                $data                   = $util->toDecrypt($decodeCode);
//                $params['analyze_data'] = $data;
//            } catch (\Exception $e) {
//                Tools::writeLog('liurui analyze exception: ' . $e->getMessage() . ' this json:', 'liurui_exception', $params, 'exception');
//                //                Log::channel('liurui')->info('liurui analyze exception: ' . $e->getMessage() . ' this json:' . json_encode($params));
//            }
//        }
//
//        if(isset($params['analyze_data']['cmd_type'])){
//            Tools::deviceLog('aep',$params['IMEI'],'liurui',$params);
////            $logFileName = 'aep-'.date('YmdHis').'-'.$params['IMEI']. '-' .md5(Uuid::uuid4()->toString());
////            Tools::writeLog('', 'liurui', $params, $logFileName , '%message%%context% %extra%');
//                //            Log::channel('liurui')->info('liurui analyze data: ' . json_encode($params));
//        }
        Tools::writeLog('','liuruicloud',$params);
        return response()->json(['code' => 0, 'message' => 0]);
    }
}
