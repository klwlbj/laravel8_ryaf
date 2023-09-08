<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_product_management
{
    //参数productId: 类型long, 参数不可以为空
    //  描述:
    public static function QueryProduct($appKey, $appSecret, $productId)
    {
        $path               = "/aep_product_management/product";
        $headers            = null;
        $param              = [];
        $param["productId"] = $productId;

        $version = "20181031202055";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数searchValue: 类型String, 参数可以为空
    //  描述:产品id或者产品名称
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    public static function QueryProductList($appKey, $appSecret, $searchValue = "", $pageNow = "", $pageSize = "")
    {
        $path                 = "/aep_product_management/products";
        $headers              = null;
        $param                = [];
        $param["searchValue"] = $searchValue;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;

        $version = "20190507004824";

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
    public static function DeleteProduct($appKey, $appSecret, $MasterKey, $productId)
    {
        $path                 = "/aep_product_management/product";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["productId"] = $productId;

        $version = "20181031202029";

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
    public static function CreateProduct($appKey, $appSecret, $body)
    {
        $path    = "/aep_product_management/product";
        $headers = null;
        $param   = null;
        $version = "20220924042921";

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
    public static function UpdateProduct($appKey, $appSecret, $body)
    {
        $path    = "/aep_product_management/product";
        $headers = null;
        $param   = null;
        $version = "20220924043504";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
