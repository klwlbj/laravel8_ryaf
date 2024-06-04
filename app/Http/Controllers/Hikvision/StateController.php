<?php

namespace App\Http\Controllers\Hikvision;

use Illuminate\Http\Request;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\StateServer;

class StateController
{
    public function report(Request $request)
    {
        $params = $request->all();

        $res = StateServer::getInstance()->report($params);

        return Response::returnJson($res);
    }
}
