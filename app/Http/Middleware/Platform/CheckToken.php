<?php

namespace App\Http\Middleware\Platform;

use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\Response;
use App\Utils\Tools;
use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class CheckToken
{
    public function handle($request, Closure $next)
    {
        Tools::writeLog('header:','platform',$request->header());
        Tools::writeLog('params:','platform',$request->all());
        $token = $request->header('Token');

        if (empty($token)) {
            return Response::apiErrorResult('token 不能为空');
        }

        $operatorId = $request->header('OperatorId');


        if (empty($operatorId)) {
            return Response::apiErrorResult('operatorId 不能为空');
        }


        if($token != Auth::getInstance()->getToken($operatorId)){
            return Response::apiErrorResult('token 已过期');
        }

        Auth::$token = $token;

        return $next($request);
    }
}
