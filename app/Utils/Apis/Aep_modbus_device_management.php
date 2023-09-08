<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_modbus_device_management
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数deviceId: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateDevice($appKey, $appSecret, $MasterKey, $deviceId, $body)
    {
        $path                 = "/aep_modbus_device_management/modbus/device";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param             = [];
        $param["deviceId"] = $deviceId;

        $version = "20200404012440";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateDevice($appKey, $appSecret, $body)
    {
        $path    = "/aep_modbus_device_management/modbus/device";
        $headers = null;
        $param   = null;
        $version = "20200404012437";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数deviceId: 类型String, 参数不可以为空
    //  描述:
    //参数productId: 类型long, 参数不可以为空
    //  描述:
    public static function QueryDevice($appKey, $appSecret, $MasterKey, $deviceId, $productId)
    {
        $path                 = "/aep_modbus_device_management/modbus/device";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["deviceId"]  = $deviceId;
        $param["productId"] = $productId;

        $version = "20200404012435";

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
    //  描述:
    //参数searchValue: 类型String, 参数可以为空
    //  描述:设备名称，设备编号，设备Id
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function QueryDeviceList($appKey, $appSecret, $MasterKey, $productId, $searchValue = "", $pageNow = "", $pageSize = "")
    {
        $path                 = "/aep_modbus_device_management/modbus/devices";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param                = [];
        $param["productId"]   = $productId;
        $param["searchValue"] = $searchValue;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;

        $version = "20200404012428";

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
    //  描述:
    //参数deviceIds: 类型String, 参数不可以为空
    //  描述:
    public static function DeleteDevice($appKey, $appSecret, $MasterKey, $productId, $deviceIds)
    {
        $path                 = "/aep_modbus_device_management/modbus/device";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["productId"] = $productId;
        $param["deviceIds"] = $deviceIds;

        $version = "20200404012425";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function ListDeviceInfo($appKey, $appSecret, $MasterKey, $body)
    {
        $path                 = "/aep_modbus_device_management/listByDeviceIds";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20210828063614";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
