<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_edge_gateway
{
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function DeleteEdgeInstanceDevice($appKey, $appSecret, $body)
    {
        $path    = "/aep_edge_gateway/instance/devices";
        $headers = null;
        $param   = null;
        $version = "20201226000026";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数gatewayDeviceId: 类型String, 参数不可以为空
    //  描述:
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function QueryEdgeInstanceDevice($appKey, $appSecret, $gatewayDeviceId, $pageNow = "", $pageSize = "")
    {
        $path                     = "/aep_edge_gateway/instance/devices";
        $headers                  = null;
        $param                    = [];
        $param["gatewayDeviceId"] = $gatewayDeviceId;
        $param["pageNow"]         = $pageNow;
        $param["pageSize"]        = $pageSize;

        $version = "20201226000022";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateEdgeInstance($appKey, $appSecret, $body)
    {
        $path    = "/aep_edge_gateway/instance";
        $headers = null;
        $param   = null;
        $version = "20201226000017";

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
    public static function EdgeInstanceDeploy($appKey, $appSecret, $body)
    {
        $path    = "/aep_edge_gateway/instance/settings";
        $headers = null;
        $param   = null;
        $version = "20201226000010";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:
    public static function DeleteEdgeInstance($appKey, $appSecret, $id)
    {
        $path        = "/aep_edge_gateway/instance";
        $headers     = null;
        $param       = [];
        $param["id"] = $id;

        $version = "20201225235957";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function AddEdgeInstanceDevice($appKey, $appSecret, $body)
    {
        $path    = "/aep_edge_gateway/instance/device";
        $headers = null;
        $param   = null;
        $version = "20201226000004";

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
    public static function AddEdgeInstanceDrive($appKey, $appSecret, $body)
    {
        $path    = "/aep_edge_gateway/instance/drive";
        $headers = null;
        $param   = null;
        $version = "20201225235952";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
