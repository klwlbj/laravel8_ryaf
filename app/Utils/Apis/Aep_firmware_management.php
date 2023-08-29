<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_firmware_management
{
    //参数id: 类型long, 参数不可以为空
    //  描述:固件id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateFirmware($appKey, $appSecret, $id, $body, $MasterKey = "")
    {
        $path="/aep_firmware_management/firmware";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;

        $version ="20190615001705";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数searchValue: 类型String, 参数可以为空
    //  描述:查询条件，可以根据固件名称模糊查询
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    public static function QueryFirmwareList($appKey, $appSecret, $productId, $searchValue = "", $pageNow = "", $pageSize = "", $MasterKey = "")
    {
        $path="/aep_firmware_management/firmwares";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["searchValue"]=$searchValue;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20190615001608";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:固件id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    public static function QueryFirmware($appKey, $appSecret, $id, $productId, $MasterKey = "")
    {
        $path="/aep_firmware_management/firmware";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;

        $version ="20190618151645";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:固件id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数updateBy: 类型String, 参数可以为空
    //  描述:修改人
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    public static function DeleteFirmware($appKey, $appSecret, $id, $productId, $updateBy = "", $MasterKey = "")
    {
        $path="/aep_firmware_management/firmware";
        $headers=null;
        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;
        $param["updateBy"]=$updateBy;
        $param["MasterKey"]=$MasterKey;

        $version ="20190615001534";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
