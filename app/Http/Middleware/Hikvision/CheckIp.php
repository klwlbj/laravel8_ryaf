<?php

namespace App\Http\Middleware\Hikvision;

use App\Http\Server\Hikvision\Response;
use Closure;

class CheckIp
{
    public function handle($request, Closure $next)
    {
        $clientIp = $request->ip();

        $serverName = gethostname(); // 获取当前主机名
        $localIP = gethostbyname($serverName);


        $localIps = ['127.0.0.1', $localIP];

        if(!in_array($clientIp,$localIps)){
            return Response::returnJson(['code' => 405,'message' => 'ip有误','date' => []]);
        }
        return $next($request);
    }
}
