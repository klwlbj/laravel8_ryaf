<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_software_management
{
    //参数id: 类型long, 参数不可以为空
    //  描述:升级包id
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，在产品概况中可以查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateSoftware($appKey, $appSecret, $id, $MasterKey, $body)
    {
        $path                 = "/aep_software_management/software";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param       = [];
        $param["id"] = $id;

        $version = "20200529232851";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:升级包id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey,在产品概况中可以查询
    public static function DeleteSoftware($appKey, $appSecret, $id, $productId, $MasterKey)
    {
        $path                 = "/aep_software_management/software";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["id"]        = $id;
        $param["productId"] = $productId;

        $version = "20200529232809";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:升级包ID
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，可在产品概况中查看
    public static function QuerySoftware($appKey, $appSecret, $id, $productId, $MasterKey)
    {
        $path                 = "/aep_software_management/software";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["id"]        = $id;
        $param["productId"] = $productId;

        $version = "20200529232806";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数searchValue: 类型String, 参数可以为空
    //  描述:查询条件，可以根据升级包名称模糊查询
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，可以在产品概况中查看
    public static function QuerySoftwareList($appKey, $appSecret, $productId, $MasterKey, $searchValue = "", $pageNow = "", $pageSize = "")
    {
        $path                 = "/aep_software_management/softwares";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param                = [];
        $param["productId"]   = $productId;
        $param["searchValue"] = $searchValue;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;

        $version = "20200529232801";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
