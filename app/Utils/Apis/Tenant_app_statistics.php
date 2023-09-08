<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Tenant_app_statistics
{
    public static function QueryTenantApiMonthlyCount($appKey, $appSecret)
    {
        $path    = "/tenant_app_statistics/queryTenantApiMonthlyCount";
        $headers = null;
        $param   = null;
        $version = "20201225122609";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    public static function QueryTenantAppCount($appKey, $appSecret)
    {
        $path    = "/tenant_app_statistics/queryTenantAppCount";
        $headers = null;
        $param   = null;
        $version = "20201225122611";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数dateType: 类型String, 参数不可以为空
    //  描述:日期格式 m：月  d：日
    //参数dataType: 类型String, 参数不可以为空
    //  描述:数据格式 1：api调用量分析
    public static function QueryTenantApiTrend($appKey, $appSecret, $dateType, $dataType)
    {
        $path              = "/tenant_app_statistics/queryTenantApiTrend";
        $headers           = null;
        $param             = [];
        $param["dateType"] = $dateType;
        $param["dataType"] = $dataType;

        $version = "20201225122606";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
