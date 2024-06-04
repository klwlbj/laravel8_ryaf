<?php

namespace App\Http\Controllers\Hikvision;

use Illuminate\Http\Request;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\MonitorServer;

class MonitorController
{
    public function report(Request $request)
    {
        $params = $request->all();

        $res = MonitorServer::getInstance()->report($params);

        return Response::returnJson($res);
    }
}
