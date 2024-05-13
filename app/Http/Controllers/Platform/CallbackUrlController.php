<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Server\Platform\CallbackUrlServer;
use App\Http\Server\Platform\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallbackUrlController extends Controller
{
    /**修改上报失败回调地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReportFailure(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'operatorId'         => 'required',
            'url'         => 'required',
        ], [
            'operatorId.required'         => 'operatorId不能为空',
            'url.required'         => 'url不能为空',
        ]);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $params['type'] = 'report_failure';

        $res = CallbackUrlServer::getInstance()->updateCallbackUrl($params);

        if(!$res){
            return Response::apiErrorResult('修改失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }

    /**删除上报失败回调地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteReportFailure(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'operatorId'         => 'required',
        ], [
            'operatorId.required'         => 'operatorId不能为空',
        ]);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $params['type'] = 'report_failure';

        $res = CallbackUrlServer::getInstance()->deleteCallbackUrl($params);

        if(!$res){
            return Response::apiErrorResult('删除失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }


    /**修改查询充电口实时数据回调地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGetChargingMetaInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'operatorId'         => 'required',
            'url'         => 'required',
        ], [
            'operatorId.required'         => 'operatorId不能为空',
            'url.required'         => 'url不能为空',
        ]);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $params['type'] = 'get_charging_meta_info';

        $res = CallbackUrlServer::getInstance()->updateCallbackUrl($params);

        if(!$res){
            return Response::apiErrorResult('修改失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }

    /**删除查询充电口实时数据回调地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGetChargingMetaInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'operatorId'         => 'required',
        ], [
            'operatorId.required'         => 'operatorId不能为空',
        ]);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $params['type'] = 'get_charging_meta_info';

        $res = CallbackUrlServer::getInstance()->deleteCallbackUrl($params);

        if(!$res){
            return Response::apiErrorResult('删除失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }

    /**修改安全预警回调地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotifyCellRisk(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $validate = Validator::make($params, [
            'operatorId'         => 'required',
            'url'         => 'required',
        ], [
            'operatorId.required'         => 'operatorId不能为空',
            'url.required'         => 'url不能为空',
        ]);

        if($validate->fails())
        {
            return Response::apiErrorResult($validate->errors()->first());
        }

        $params['type'] = 'notify_cell_risk';

        $res = CallbackUrlServer::getInstance()->updateCallbackUrl($params);

        if(!$res){
            return Response::apiErrorResult('修改失败');
        }
        return Response::apiResult(200,'请求成功!',$res);
    }
}
