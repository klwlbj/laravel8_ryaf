<?php

namespace App\Http\Controllers;

use App\Utils\LiangXin;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LiangXinController extends BaseController
{
    public function __construct()
    {
        DB::setDefaultConnection('mysql2');
    }
    public function getTown(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [

        ],[

        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->getTown($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function getRust(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'townId' => 'required',
        ],[
            'townId.required' => '街道不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->getRust($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function addUnit(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'oid' => 'required',
            'townId' => 'required',
            'rustId' => 'required',
            'name' => 'required',
            'type' => 'required',
            'address' => 'required',
            'gpsLnt' => 'required',
            'gpsLat' => 'required',
            'chargeUsers' => 'required',
        ],[
            'oid.required' => 'oid不得为空',
            'townId.required' => 'townId不得为空',
            'rustId.required' => 'rustId不得为空',
            'name.required' => 'name不得为空',
            'type.required' => 'type不得为空',
            'address.required' => 'address不得为空',
            'gpsLnt.required' => 'gpsLnt不得为空',
            'gpsLat.required' => 'gpsLat不得为空',
            'chargeUsers.required' => 'chargeUsers不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->addUnit($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function updateUnit(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'unitId' => 'required',
        ],[
            'unitId.required' => 'unitId不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->updateUnit($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function unregisterUnit(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'unitId' => 'required',
        ],[
            'unitId.required' => 'unitId不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->unregisterUnit($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function addDevice(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'uid' => 'required',
            'oid' => 'required',
            'oidType' => 'required',
            'unitId' => 'required',
            'name' => 'required',
            'provider' => 'required',
            'type' => 'required',
            'address' => 'required',
            'standardAddress' => 'required',
            'addrRoom' => 'required',
            'installLocation' => 'required',
            'gpsLnt' => 'required',
            'gpsLat' => 'required',
        ],[
            'uid.required' => 'uid不得为空',
            'oid.required' => 'oid不得为空',
            'oidType.required' => 'oidType不得为空',
            'unitId.required' => 'unitId不得为空',
            'name.required' => 'name不得为空',
            'provider.required' => 'provider不得为空',
            'type.required' => 'type不得为空',
            'address.required' => 'address不得为空',
            'standardAddress.required' => 'standardAddress不得为空',
            'addrRoom.required' => 'addrRoom不得为空',
            'installLocation.required' => 'installLocation不得为空',
            'gpsLnt.required' => 'gpsLnt不得为空',
            'gpsLat.required' => 'gpsLat不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->addDevice($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function updateDevice(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'deviceId' => 'required',
        ],[
            'deviceId.required' => 'deviceId不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->updateDevice($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function unregisterDevice(Request $request)
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'deviceId' => 'required',
        ],[
            'deviceId.required' => 'deviceId不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->unregisterDevice($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }

    public function notify(Request $request)
    {
        $params = $request->all();

        Tools::writeLog('report','liangxin',$params);
        $validate = Validator::make($params, [
            'keyCode' => 'required',
            'current' => 'required',
        ],[
            'keyCode.required' => 'keyCode不得为空',
            'current.required' => 'current不得为空',
        ]);

        if($validate->fails())
        {
            return LiangXin::errorRes($validate->errors()->first());
        }

        $util = new LiangXin();

        $res = $util->notify($params);

        if($res === false){
            return LiangXin::errorRes();
        }

        return LiangXin::successRes($res);
    }
}
