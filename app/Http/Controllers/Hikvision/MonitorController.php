<?php

namespace App\Http\Controllers\Hikvision;

use App\Utils\Tools;
use Illuminate\Http\Request;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\MonitorServer;

class MonitorController
{
    public function report(Request $request)
    {
        $rule = [
            'unitId'      => 'required|integer',
            'monitorId'   => 'required',
            'imei'        => 'required|integer',
            'deviceName'  => 'required',
            'dateTime'    => 'required|date_format:Y-m-d H:i:s',
            'battery'     => 'required|integer',
            // 'humidness'   => 'required|integer',
            'temperature' => 'required|integer',
            'signal'      => 'required|integer',
            'pollution'   => 'required|integer',
        ];
        $input    = [];
        $valicate = Tools::validateParams($request, $rule, $input);
        if ($valicate) {
            return $valicate;
        }

        $res = MonitorServer::getInstance()->report($input);

        return Response::returnJson($res);
    }
}
