<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_nb_device_management
{
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function BatchCreateNBDevice($appKey, $appSecret, $body)
    {
        $path="/aep_nb_device_management/batchNBDevice";
        $headers=null;
        $param=null;
        $version ="20200828140355";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function BatchCancelDevices($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_nb_device_management/cancelledDevices";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20211009093738";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function DeleteDeviceByImei($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_nb_device_management/deleteDeviceByImei";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20220226071405";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数不可以为空
    //  描述:
    //参数imei: 类型String, 参数不可以为空
    //  描述:
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    public static function QueryDeviceByImei($appKey, $appSecret, $productId, $imei, $MasterKey)
    {
        $path="/aep_nb_device_management/getDeviceByImei";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["imei"]=$imei;

        $version ="20221130175656";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
