<?php

namespace App\Http\Controllers\DaHua;

use App\Http\Server\DaHua\Response;
use App\Http\Server\DaHua\UnitsServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController
{
    public function getList(Request $request)
    {
        $params = $request->all();
        $validate = Validator::make($params, [

        ],[

        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = UnitsServer::getInstance()->getList($params);
        if(!$res){
            return Response::returnJson(['code' => -1,'message' => Response::getMsg(),'date' => []]);
        }

        return Response::apiResult($res);
    }
}
