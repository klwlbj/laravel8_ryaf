<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_device_group_management
{
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateDeviceGroup($appKey, $appSecret, $body, $MasterKey = "")
    {
        $path="/aep_device_group_management/deviceGroup";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20190615001622";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateDeviceGroup($appKey, $appSecret, $body, $MasterKey = "")
    {
        $path="/aep_device_group_management/deviceGroup";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20190615001615";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数可以为空
    //  描述:产品Id，单产品分组必填
    //参数deviceGroupId: 类型long, 参数不可以为空
    //  描述:分组Id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    public static function DeleteDeviceGroup($appKey, $appSecret, $deviceGroupId, $productId = "", $MasterKey = "")
    {
        $path="/aep_device_group_management/deviceGroup";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["deviceGroupId"]=$deviceGroupId;

        $version ="20190615001601";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数pageNow: 类型long, 参数不可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数不可以为空
    //  描述:每页记录数
    //参数productId: 类型long, 参数可以为空
    //  描述:支持通过产品id查询单产品分组列表
    //参数deviceGroupId: 类型long, 参数可以为空
    //  描述:支持通过分组ID查询
    //参数deviceGroupName: 类型String, 参数可以为空
    //  描述:支持通过分组名称查询
    //参数groupLevel: 类型long, 参数可以为空
    //  描述:支持通过分组类别查询，0为单产品分组，1为多产品分组
    public static function QueryDeviceGroupList($appKey, $appSecret, $pageNow, $pageSize, $productId = "", $deviceGroupId = "", $deviceGroupName = "", $groupLevel = "")
    {
        $path="/aep_device_group_management/deviceGroups";
        $headers=null;
        $param=array();
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;
        $param["productId"]=$productId;
        $param["deviceGroupId"]=$deviceGroupId;
        $param["deviceGroupName"]=$deviceGroupName;
        $param["groupLevel"]=$groupLevel;

        $version ="20230218035819";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    //参数productId: 类型long, 参数可以为空
    //  描述:产品ID，查询单产品分组下已关联的设备列表或产品下未关联的设备列表时必填
    //参数searchValue: 类型String, 参数可以为空
    //  描述:可查询：设备ID，设备名称，设备编号或者IMEI号；仅支持单产品分组查询
    //参数pageNow: 类型long, 参数不可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数不可以为空
    //  描述:每页条数
    //参数deviceGroupId: 类型long, 参数可以为空
    //  描述:群组ID：1.有值则查询该群组已关联的设备信息列表。2.为空则查询该产品下未关联的设备信息列表
    public static function QueryGroupDeviceList($appKey, $appSecret, $pageNow, $pageSize, $MasterKey = "", $productId = "", $searchValue = "", $deviceGroupId = "")
    {
        $path="/aep_device_group_management/groupDeviceList";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["searchValue"]=$searchValue;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;
        $param["deviceGroupId"]=$deviceGroupId;

        $version ="20190615001540";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateDeviceGroupRelation($appKey, $appSecret, $body, $MasterKey = "")
    {
        $path="/aep_device_group_management/deviceGroupRelation";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20190615001526";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数不可以为空
    //  描述:
    //参数deviceId: 类型String, 参数不可以为空
    //  描述:
    public static function getGroupDetailByDeviceId($appKey, $appSecret, $productId, $deviceId)
    {
        $path="/aep_device_group_management/groupOfDeviceId";
        $headers=null;
        $param=array();
        $param["productId"]=$productId;
        $param["deviceId"]=$deviceId;

        $version ="20211014095939";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
