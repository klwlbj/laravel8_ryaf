<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_device_status
{
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function QueryDeviceStatus($appKey, $appSecret, $body)
    {
        $path    = "/aep_device_status/deviceStatus";
        $headers = null;
        $param   = null;
        $version = "20181031202028";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function QueryDeviceStatusList($appKey, $appSecret, $body)
    {
        $path    = "/aep_device_status/deviceStatusList";
        $headers = null;
        $param   = null;
        $version = "20181031202403";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function getDeviceStatusHisInTotal($appKey, $appSecret, $body)
    {
        $path    = "/aep_device_status/api/v1/getDeviceStatusHisInTotal";
        $headers = null;
        $param   = null;
        $version = "20190928013529";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function getDeviceStatusHisInPage($appKey, $appSecret, $body)
    {
        $path    = "/aep_device_status/getDeviceStatusHisInPage";
        $headers = null;
        $param   = null;
        $version = "20190928013337";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
