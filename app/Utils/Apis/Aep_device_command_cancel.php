<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_device_command_cancel
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CancelAllCommand($appKey, $appSecret, $MasterKey, $body)
    {
        $path                 = "/aep_device_command_cancel/cancelAllCommand";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20230419143717";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
