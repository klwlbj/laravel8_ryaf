<?php

namespace App\Http\Middleware\Hikvision;

use App\Http\Server\Hikvision\Response;
use App\Utils\Tools;
use Closure;

class CheckIp
{
    public function handle($request, Closure $next)
    {
        $clientIp = $request->ip();

        $serverName = gethostname(); // 获取当前主机名
        $localIP = gethostbyname($serverName);
//        Tools::writeLog('clientIp：' . $clientIp,'haikan');
        Tools::writeLog('localIP：' . $localIP,'haikan');

        $localIps = ['127.0.0.1','47.104.10.228', $localIP];

        if(!in_array($clientIp,$localIps)){
            return Response::returnJson(['code' => 405,'message' => 'ip有误','date' => []]);
        }
        return $next($request);
    }
}
