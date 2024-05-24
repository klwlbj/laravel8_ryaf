<?php

namespace App\Http\Controllers\Hikvision;
use App\Http\Controllers\Controller;
use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\ReportServer;
use App\Http\Server\Platform\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public function add(Request $request){
        $params = $request->all();

        $validate = ReportServer::getInstance()->cellInfoValidator($params);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }


//
//        if(!$res){
//            return Response::apiErrorResult(Response::getMsg());
//        }
//        return Response::apiResult(200,'请求成功!',$res);
    }


}
