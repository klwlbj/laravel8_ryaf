<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_device_command_lwm_profile
{
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateCommandLwm2mProfile($appKey, $appSecret, $body, $MasterKey = "")
    {
        $path                 = "/aep_device_command_lwm_profile/commandLwm2mProfile";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20191231141545";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
