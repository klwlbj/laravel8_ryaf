<?php

namespace App\Http\Middleware\LiangXin;

use App\Http\Server\Hikvision\Response;
use App\Utils\LiangXin;
use App\Utils\Tools;
use Closure;

class CheckSign
{
    public function handle($request, Closure $next)
    {
        Tools::writeLog('params','liangxin',$request->all());

        if(!LiangXin::checkSign()){
            return LiangXin::errorRes();
        }

        return $next($request);
    }
}
