<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Tenant_device_statistics
{
    public static function QueryTenantDeviceCount($appKey, $appSecret)
    {
        $path    = "/tenant_device_statistics/queryTenantDeviceCount";
        $headers = null;
        $param   = null;
        $version = "20201225122555";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数dateType: 类型String, 参数不可以为空
    //  描述:时间类型：d:天；m:月
    //参数type: 类型String, 参数不可以为空
    //  描述:数据类型：1：设备总数量，激活数，活跃数；3：设备活跃数，活跃率
    public static function QueryTenantDeviceTrend($appKey, $appSecret, $dateType, $type)
    {
        $path              = "/tenant_device_statistics/queryTenantDeviceTrend";
        $headers           = null;
        $param             = [];
        $param["dateType"] = $dateType;
        $param["type"]     = $type;

        $version = "20201225122550";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    public static function QueryTenantDeviceActiveCount($appKey, $appSecret)
    {
        $path    = "/tenant_device_statistics/queryTenantDeviceActiveCount";
        $headers = null;
        $param   = null;
        $version = "20201225122558";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
