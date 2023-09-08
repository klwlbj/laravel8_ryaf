<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_public_product_management
{
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function QueryPublicByPublicProductId($appKey, $appSecret, $body)
    {
        $path    = "/aep_public_product_management/publicProducts";
        $headers = null;
        $param   = null;
        $version = "20190507003930";

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
    public static function QueryPublicByProductId($appKey, $appSecret, $body)
    {
        $path    = "/aep_public_product_management/publicProductList";
        $headers = null;
        $param   = null;
        $version = "20190507004139";

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
    public static function InstantiateProduct($appKey, $appSecret, $body)
    {
        $path    = "/aep_public_product_management/instantiateProduct";
        $headers = null;
        $param   = null;
        $version = "20200801233037";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数searchValue: 类型String, 参数可以为空
    //  描述:设备类型、厂商ID、厂商名称
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    public static function QueryAllPublicProductList($appKey, $appSecret, $searchValue = "", $pageNow = "", $pageSize = "")
    {
        $path                 = "/aep_public_product_management/allPublicProductList";
        $headers              = null;
        $param                = [];
        $param["searchValue"] = $searchValue;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;

        $version = "20200829005548";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数searchValue: 类型String, 参数可以为空
    //  描述:产品名称
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    //参数productIds: 类型String, 参数可以为空
    //  描述:私有产品idList
    public static function QueryMyPublicProductList($appKey, $appSecret, $searchValue = "", $pageNow = "", $pageSize = "", $productIds = "")
    {
        $path                 = "/aep_public_product_management/myPublicProductList";
        $headers              = null;
        $param                = [];
        $param["searchValue"] = $searchValue;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;
        $param["productIds"]  = $productIds;

        $version = "20200829005359";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
