<?php
namespace App\Utils\Apis;


use App\Utils\Apis\Core\AepSdkCore;

class Aep_device_event
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function QueryDeviceEventList($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_device_event/device/events";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20210327064751";

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
    public static function QueryDeviceEventTotal($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_device_event/device/events/total";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20210327064755";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
