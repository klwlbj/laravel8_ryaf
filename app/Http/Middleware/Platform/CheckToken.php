<?php

namespace App\Http\Middleware\Platform;

use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\Response;
use Closure;
use Illuminate\Support\Facades\Redis;

class CheckToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Token');

        if (empty($token)) {
            return Response::apiErrorResult('token 不能为空');
        }

        $data = Redis::get('platform_token:' . $token);

        if(empty($data)){
            return Response::apiErrorResult('token 已过期');
        }

        Auth::$token = $token;

        return $next($request);
    }
}
