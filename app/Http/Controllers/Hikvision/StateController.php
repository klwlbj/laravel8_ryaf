<?php

namespace App\Http\Controllers\Hikvision;

use App\Utils\Tools;
use Illuminate\Http\Request;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\StateServer;

class StateController
{
    public function report(Request $request)
    {
        $rule = [
            'unitId'       => 'required|integer',
            'statusId'     => 'required',
            'imei'         => 'required|integer',
            'deviceName'   => 'required',
            'dateTime'     => 'required|date_format:Y-m-d H:i:s',
            'onlineStatus' => 'required|in:-1,1,0',
        ];
        $input    = [];
        $valicate = Tools::validateParams($request, $rule, $input);
        if ($valicate) {
            return $valicate;
        }

        $res = StateServer::getInstance()->report($input);

        return Response::returnJson($res);
    }
}
