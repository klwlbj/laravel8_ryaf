<?php

namespace App\Http\Controllers\Hikvision;

use Illuminate\Http\Request;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\AlarmServer;

class AlarmController
{
    public function report(Request $request)
    {
        $res = AlarmServer::getInstance()->report();

        return Response::returnJson($res);
    }

    public function confirm(Request $request)
    {
        $res = AlarmServer::getInstance()->confirm();

        return Response::returnJson($res);
    }

    public function reConfirm(Request $request)
    {
        $res = AlarmServer::getInstance()->reConfirm();

        return Response::returnJson($res);
    }
}
