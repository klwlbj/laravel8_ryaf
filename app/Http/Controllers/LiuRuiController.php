<?php

namespace App\Http\Controllers;

use App\Utils\Tools;
use App\Utils\LiuRui;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Nonstandard\Uuid;

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
        //        print_r($params);die;
        $params = Tools::jsonDecode($params);
        $util   = new LiuRui();

        if (isset($params['messageType']) && $params['messageType'] == 'dataReport' && isset($params['payload']['APPdata'])) {
            $decodeCode = base64_decode($params['payload']['APPdata']);
            try {
                $data                   = $util->toDecrypt($decodeCode);
                $params['analyze_data'] = $data;
            } catch (\Exception $e) {
                Tools::writeLog('liurui analyze exception: ' . $e->getMessage() . ' this json:', 'liurui_exception', $params, 'exception');
                //                Log::channel('liurui')->info('liurui analyze exception: ' . $e->getMessage() . ' this json:' . json_encode($params));
            }
        }

        if(isset($params['analyze_data']['cmd_type'])){
            Tools::deviceLog('aep',$params['IMEI'],'liurui',$params);
//            $logFileName = 'aep-'.date('YmdHis').'-'.$params['IMEI']. '-' .md5(Uuid::uuid4()->toString());
//            Tools::writeLog('', 'liurui', $params, $logFileName , '%message%%context% %extra%');
                //            Log::channel('liurui')->info('liurui analyze data: ' . json_encode($params));
        }

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
                Tools::writeLog('' . $e->getMessage() .$e->getLine() . ' this json:','liurui_exception',$params,'exception');
//                Log::channel('liurui_ontnet')->info('liurui_ontnet analyze exception: ' . $e->getMessage());
            }
        }
        if(isset($params['msg']['analyze_data']['cmd_type'])){
            Tools::deviceLog('onenet',$params['msg']['imei'],'liurui',$params);
//            $logFileName = 'onenet-'.date('YmdHis').'-'.$params['msg']['imei']. '-' .md5(Uuid::uuid4()->toString());
//            Tools::writeLog('','liurui',$params,$logFileName,'%message%%context% %extra%');
        }


        return $params['msg'];
        // return response()->json(['code' => 0, 'message' => 0]);
    }

    public function mufflingByOneNet($imei)
    {
        return (new LiuRui())->mufflingByOneNet($imei);
    }
}
