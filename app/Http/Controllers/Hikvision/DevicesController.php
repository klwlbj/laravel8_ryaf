<?php

namespace App\Http\Controllers\Hikvision;
use App\Http\Controllers\Controller;
use App\Http\Server\Hikvision\DevicesServer;
use App\Http\Server\Hikvision\Response;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DevicesController extends Controller
{
    public function add(Request $request){
        $params = $request->all();
        Tools::writeLog('device add params','haikan',$params);
        $validate = Validator::make($params, [
            'unitId' => 'required',
            'imei' => 'required',
            'deviceName' => 'required',
            'deviceType' => 'required',
            'notifyPhone' => 'required',
            'pointX' => 'required',
            'pointY' => 'required',
        ],[
            'unitId.required' => 'unitId 不得为空',
            'imei.required' => 'imei 不得为空',
            'deviceName.required' => 'deviceName 不得为空',
            'deviceType.required' => 'deviceType 不得为空',
            'notifyPhone.required' => 'notifyPhone 不得为空',
            'pointX.required' => 'ID 不得为空',
            'pointY.required' => 'ID 不得为空',
        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = DevicesServer::getInstance()->add($params);

        return Response::returnJson($res);
    }

    public function update(Request $request){
        $params = $request->all();
        Tools::writeLog('device update param','haikan',$params);
        $validate = Validator::make($params, [
            'unitId' => 'required',
            'imei' => 'required',
            'deviceName' => 'required',
            'deviceType' => 'required',
            'notifyPhone' => 'required',
            'pointX' => 'required',
            'pointY' => 'required',
        ],[
            'unitId.required' => 'unitId 不得为空',
            'imei.required' => 'imei 不得为空',
            'deviceName.required' => 'deviceName 不得为空',
            'deviceType.required' => 'deviceType 不得为空',
            'notifyPhone.required' => 'notifyPhone 不得为空',
            'pointX.required' => 'ID 不得为空',
            'pointY.required' => 'ID 不得为空',
        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = DevicesServer::getInstance()->update($params);

        return Response::returnJson($res);
    }

    public function delete(Request $request)
    {
        $params = $request->all();
        Tools::writeLog('device delete param','haikan',$params);
        $validate = Validator::make($params, [
            'unitId' => 'required',
            'imei' => 'required'
        ],[
            'unitId.required' => 'unitId 不得为空',
            'imei.required' => 'imei 不得为空'
        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = DevicesServer::getInstance()->delete($params);

        return Response::returnJson($res);
    }


}
