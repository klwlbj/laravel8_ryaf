<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_subscribe_north
{
    //参数subId: 类型long, 参数不可以为空
    //  描述:订阅记录id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id，分组级为-1
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:产品MasterKey
    public static function GetSubscription($appKey, $appSecret, $subId, $productId, $MasterKey)
    {
        $path="/aep_subscribe_north/subscription";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["subId"]=$subId;
        $param["productId"]=$productId;

        $version ="20220624171733";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数不可以为空
    //  描述:产品ID
    //参数pageNow: 类型long, 参数不可以为空
    //  描述:当前页
    //参数pageSize: 类型long, 参数不可以为空
    //  描述:每页条数
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数subType: 类型long, 参数可以为空
    //  描述:订阅类型
    //参数searchValue: 类型String, 参数可以为空
    //  描述:检索deviceId,模糊匹配
    //参数deviceGroupId: 类型String, 参数可以为空
    //  描述:
    public static function GetSubscriptionsList($appKey, $appSecret, $productId, $pageNow, $pageSize, $MasterKey, $subType = "", $searchValue = "", $deviceGroupId = "")
    {
        $path="/aep_subscribe_north/subscribes";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;
        $param["subType"]=$subType;
        $param["searchValue"]=$searchValue;
        $param["deviceGroupId"]=$deviceGroupId;

        $version ="20220624163719";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数subId: 类型String, 参数不可以为空
    //  描述:订阅记录id
    //参数deviceId: 类型String, 参数可以为空
    //  描述:设备id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数subLevel: 类型long, 参数不可以为空
    //  描述:订阅级别
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:产品MasterKey
    public static function DeleteSubscription($appKey, $appSecret, $subId, $productId, $subLevel, $MasterKey, $deviceId = "")
    {
        $path="/aep_subscribe_north/subscription";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["subId"]=$subId;
        $param["deviceId"]=$deviceId;
        $param["productId"]=$productId;
        $param["subLevel"]=$subLevel;

        $version ="20181031202023";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateSubscription($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_subscribe_north/subscription";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20181031202018";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
