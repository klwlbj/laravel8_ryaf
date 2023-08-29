<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_device_model
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数productId: 类型long, 参数不可以为空
    //  描述:
    //参数searchValue: 类型String, 参数可以为空
    //  描述:可填值：属性名称，属性标识符
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    public static function QueryPropertyList($appKey, $appSecret, $MasterKey, $productId, $searchValue = "", $pageNow = "", $pageSize = "")
    {
        $path="/aep_device_model/dm/app/model/properties";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["searchValue"]=$searchValue;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20190712223330";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数productId: 类型long, 参数不可以为空
    //  描述:
    //参数searchValue: 类型String, 参数可以为空
    //  描述:可填： 服务Id, 服务名称,服务标识符
    //参数serviceType: 类型long, 参数可以为空
    //  描述:服务类型
    //    1. 数据上报
    //    2. 事件上报
    //    3.数据获取
    //    4.参数查询
    //    5.参数配置
    //    6.指令下发
    //    7.指令下发响应
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    public static function QueryServiceList($appKey, $appSecret, $MasterKey, $productId, $searchValue = "", $serviceType = "", $pageNow = "", $pageSize = "")
    {
        $path="/aep_device_model/dm/app/model/services";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["searchValue"]=$searchValue;
        $param["serviceType"]=$serviceType;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20190712224233";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
