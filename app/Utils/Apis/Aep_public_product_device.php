<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_public_product_device
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:公共产品的MasterKey
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function QueryDeviceToken($appKey, $appSecret, $MasterKey, $body)
    {
        $path                 = "/aep_public_product_device/queryDeviceToken";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20230330172346";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
