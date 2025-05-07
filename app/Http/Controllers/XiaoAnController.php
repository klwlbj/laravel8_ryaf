<?php

namespace App\Http\Controllers;

use App\Utils\Tools;
use App\Utils\XiaoAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class XiaoAnController
{
    public function report(Request $request)
    {
        $params = $request->json()->all();
        Tools::writeLog('params:','xiaoan',$params);
//        print_r($params);die;
        $util = new XiaoAn();

        if($util->checkSign($params,$params['sign'])){

        }

        $res = $util->analysisReport($params);


        return response()->json($res);
    }

    public function muffling(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'imei' => 'required',
        ],[
            'imei.required' => 'imei不得为空',
        ]);

        if($validate->fails())
        {
            return response()->json(['code' => -1, 'message' => $validate->errors()->first()]);
        }

        $util = new XiaoAn();

        return $util->sendCommand($params['imei'],[
            'cmd' => 'stop_alarm',
        ]);
    }

    public function setGuardTime(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'imei' => 'required',
            'start' => 'required',
            'end' => 'required',
            'action' => 'required',
        ],[
            'imei.required' => 'imei不得为空',
            'start.required' => 'start不得为空',
            'end.required' => 'end不得为空',
            'action.required' => 'action不得为空',
        ]);

        if($validate->fails())
        {
            return response()->json(['code' => -1, 'message' => $validate->errors()->first()]);
        }

        $util = new XiaoAn();

        return $util->sendCommand($params['imei'],[
            'cmd' => 'motion_detection_config',
            'start' => $params['start'],
            'end' => $params['end'],
            'action' => $params['action'],
        ]);
    }

    public function setGuardSensitivity(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'imei' => 'required',
            'sensitivity' => 'required|integer|between:0,255',
        ],[
            'imei.required' => 'imei不得为空',
            'sensitivity.required' => 'sensitivity不得为空',
        ]);

        if($validate->fails())
        {
            return response()->json(['code' => -1, 'message' => $validate->errors()->first()]);
        }

//        print_r($params['sensitivity']);die;

        $util = new XiaoAn();

        return $util->sendCommand($params['imei'],[
            'cmd' => 'PIR_sensitivity',
            'sensitivity' => $params['sensitivity']
        ]);


    }
}
