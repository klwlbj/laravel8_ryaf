<?php

namespace App\Http\Controllers\Hikvision;

use App\Utils\Tools;
use Illuminate\Http\Request;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\AlarmServer;

class AlarmController
{
    public function report(Request $request)
    {
        $rule = [
            'unitId'     => 'required|integer',
            'alarmId'    => 'required',
            'imei'       => 'required|integer',
            'dateTime'   => 'required|date_format:Y-m-d H:i:s',
            'alarmType'  => 'required|integer',
            'alarmLevel' => 'required|integer',
        ];
        $input    = [];
        $valicate = Tools::validateParams($request, $rule, $input);
        if ($valicate) {
            return $valicate;
        }
        $res = AlarmServer::getInstance()->report($input);

        return Response::returnJson($res);
    }

    public function confirm(Request $request)
    {
        $rule = [
            'unitId'         => 'required|integer',
            'alarmId'        => 'required',
            'imei'           => 'required|integer',
            'dateTime'       => 'required|date_format:Y-m-d H:i:s',
            'handleUserName' => 'required',
            'handleStatus'   => 'required|in:1,2',
            'handleRemark'   => 'nullable',
        ];
        $input    = [];
        $valicate = Tools::validateParams($request, $rule, $input);
        if ($valicate) {
            return $valicate;
        }
        $res = AlarmServer::getInstance()->confirm($input);

        return Response::returnJson($res);
    }

    // public function reConfirm(Request $request)
    // {
    //     $res = AlarmServer::getInstance()->reConfirm();
    //
    //     return Response::returnJson($res);
    // }
}
