<?php

namespace App\Http\Controllers\Platform;
use App\Http\Controllers\Controller;
use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\ReportServer;
use App\Http\Server\Platform\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function chargingMetaInfo(Request $request){
        $params = $request->all();

        $validate = ReportServer::getInstance()->cellInfoValidator($params);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $res = ReportServer::getInstance()->cellInfoReport($params);

        if(!$res){
            return Response::apiErrorResult(Response::getMsg());
        }
        return Response::apiResult(200,'请求成功!',$res);
    }

    public function chargingMetaInfoList(Request $request){
        $params = $request->all();

        #验证数据
        foreach ($params as $key => $item){
            $validate = ReportServer::getInstance()->cellInfoValidator($item);

            if($validate->fails())
            {
                return Response::apiErrorResult('第' . ($key + 1) . '个数据有误：' . $validate->errors()->first());
            }

            if($item['operatorId'] != Auth::$operatorId){
                return Response::apiErrorResult('第' . ($key + 1) . '个数据有误：运营商id不为授权运营商');
            }
        }

        $error = 0;
        #循环执行单条数据上报操作
        foreach ($params as $key => $item){
            $res = ReportServer::getInstance()->cellInfoReport($item);
            if(!$res){
                $error++;
            }
        }
        if(!empty($error)){
            return Response::apiErrorResult('上报失败 ' . $error . '条数据');
        }
        return Response::apiResult(200,'请求成功!','上报成功');
    }
}
