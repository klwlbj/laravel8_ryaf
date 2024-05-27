<?php

namespace App\Http\Controllers\Hikvision;
use App\Http\Controllers\Controller;
use App\Http\Server\Hikvision\DevicesServer;
use App\Http\Server\Hikvision\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DevicesController extends Controller
{
    public function add(Request $request){
        $params = $request->all();

//        print_r(Tools::calculateCheckDigit('11440111000114111'));die;

        $res = DevicesServer::getInstance()->add($params);

        return Response::returnJson($res);
    }

    public function update(Request $request){
        $params = $request->all();

//        if(!UnitsServer::getInstance()->verifyParams($params)){
//            return Response::apiErrorResult(Response::getMsg());
//        }

        $res = DevicesServer::getInstance()->update($params);

        return Response::returnJson($res);
    }

    public function delete(Request $request)
    {
        $params = $request->all();

//        if(!UnitsServer::getInstance()->verifyParams($params)){
//            return Response::apiErrorResult(Response::getMsg());
//        }

        $res = DevicesServer::getInstance()->delete($params);

        return Response::returnJson($res);
    }


}
