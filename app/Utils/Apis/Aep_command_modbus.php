<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_command_modbus
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数productId: 类型String, 参数不可以为空
    //  描述:产品ID，必填
    //参数deviceId: 类型String, 参数不可以为空
    //  描述:设备ID，必填
    //参数status: 类型String, 参数可以为空
    //  描述:状态可选填： 1：指令已保存 2：指令已发送 3：指令已送达 4：指令已完成 6：指令已取消 999：指令失败
    //参数startTime: 类型String, 参数可以为空
    //  描述:
    //参数endTime: 类型String, 参数可以为空
    //  描述:
    //参数pageNow: 类型String, 参数可以为空
    //  描述:
    //参数pageSize: 类型String, 参数可以为空
    //  描述:
    public static function QueryCommandList($appKey, $appSecret, $MasterKey, $productId, $deviceId, $status = "", $startTime = "", $endTime = "", $pageNow = "", $pageSize = "")
    {
        $path                 = "/aep_command_modbus/modbus/commands";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["productId"] = $productId;
        $param["deviceId"]  = $deviceId;
        $param["status"]    = $status;
        $param["startTime"] = $startTime;
        $param["endTime"]   = $endTime;
        $param["pageNow"]   = $pageNow;
        $param["pageSize"]  = $pageSize;

        $version = "20200904171008";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品ID
    //参数deviceId: 类型String, 参数不可以为空
    //  描述:设备ID
    //参数commandId: 类型String, 参数不可以为空
    //  描述:指令ID
    public static function QueryCommand($appKey, $appSecret, $MasterKey, $productId, $deviceId, $commandId)
    {
        $path                 = "/aep_command_modbus/modbus/command";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["productId"] = $productId;
        $param["deviceId"]  = $deviceId;
        $param["commandId"] = $commandId;

        $version = "20200904172207";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CancelCommand($appKey, $appSecret, $MasterKey, $body)
    {
        $path                 = "/aep_command_modbus/modbus/cancelCommand";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20200404012453";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateCommand($appKey, $appSecret, $MasterKey, $body)
    {
        $path                 = "/aep_command_modbus/modbus/command";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20200404012449";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
