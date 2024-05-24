<?php

namespace App\Http\Controllers\Hikvision;
use App\Http\Controllers\Controller;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\UnitsServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public function add(Request $request){
        $params = $request->all();

        if(!UnitsServer::getInstance()->verifyParams($params)){
            return Response::apiErrorResult(Response::getMsg());
        }

        $res = UnitsServer::getInstance()->add($params);

        if(!$res){
            return Response::apiErrorResult(Response::getMsg());
        }
        return Response::apiResult(200,'请求成功!',$res);
    }

    public function update(Request $request){
        $params = $request->all();

        if(!UnitsServer::getInstance()->verifyParams($params)){
            return Response::apiErrorResult(Response::getMsg());
        }

        $res = UnitsServer::getInstance()->update($params);

        if(!$res){
            return Response::apiErrorResult(Response::getMsg());
        }
        return Response::apiResult(200,'请求成功!',$res);
    }


}
