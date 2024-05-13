<?php

namespace App\Http\Controllers\Platform;
use App\Http\Controllers\Controller;
use App\Http\Server\Platform\ChargingRecordServer;
use App\Http\Server\Platform\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChargingRecordController extends Controller
{
    public function chargingRecordSubmit(Request $request){
        $params = $request->all();

        $validate = ChargingRecordServer::getInstance()->chargingRecordValidator($params);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $res = ChargingRecordServer::getInstance()->chargingRecordSubmit($params);

        if(!$res){
            return Response::apiErrorResult('上报失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }


    public function chargingRecordListSubmit(Request $request){
        $params = $request->all();

        #验证数据
        foreach ($params as $key => $item){
            $validate = ChargingRecordServer::getInstance()->chargingRecordValidator($item);

            if($validate->fails())
            {
                return Response::apiErrorResult('第' . ($key + 1) . '个数据有误' . $validate->errors()->first());
            }
        }

        $res = ChargingRecordServer::getInstance()->chargingRecordListSubmit($params);

        if(!$res){
            return Response::apiErrorResult('上报失败');
        }
        return Response::apiResult(200,'请求成功!','上报成功');
    }

    public function chargingRecordList(Request $request){
        $params = $request->all();

        $validate = Validator::make($params, [
            'operatorId'         => 'required',
            'pageIndex'    => 'required',
            'pageSize'     => 'required'
        ], [
            'operatorId.required'         => 'operatorId不能为空',
            'pageIndex.required'    => 'pageIndex不能为空',
            'pageSize.required'     => 'pageSize不能为空',
        ]);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $res = ChargingRecordServer::getInstance()->chargingRecordList($params);

        if(!$res){
            return Response::apiErrorResult('获取失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }
}
