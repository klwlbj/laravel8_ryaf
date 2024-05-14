<?php

namespace App\Http\Middleware\Platform;

use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\Response;
use Closure;
use Illuminate\Support\Facades\Redis;

class CheckSign
{
    public function handle($request, Closure $next)
    {
        $datetime = $request->header('Datetime');

        $operatorId = $request->header('OperatorId');

        $signature = $request->header('Signature');

        if (empty($datetime)) {
            return Response::apiErrorResult('Datetime 不能为空');
        }

        if (empty($operatorId)) {
            return Response::apiErrorResult('OperatorId 不能为空');
        }

        if (empty($signature)) {
            return Response::apiErrorResult('Signature 不能为空');
        }

        if(!Auth::getInstance()->checkSign($datetime,$operatorId,$signature)){
            return Response::apiErrorResult(Response::getMsg());
        }

        Auth::$operatorId = $operatorId;

        return $next($request);
    }
}
