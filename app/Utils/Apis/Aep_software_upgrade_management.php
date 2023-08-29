<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_software_upgrade_management
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，在产品概况中查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function OperationalSoftwareUpgradeTask($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_software_upgrade_management/operational";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20200529233236";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数taskStatus: 类型long, 参数可以为空
    //  描述:子任务状态：
    //    0:未启动,1:等待升级,2:升级执行中,3:升级成功,4:升级失败,5:取消升级
    //参数searchValue: 类型String, 参数可以为空
    //  描述:查询值，设备ID或设备编号(IMEI)或设备名称模糊查询
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页码
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页显示数
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，可在产品概况中查看
    public static function QuerySoftwareUpgradeSubtasks($appKey, $appSecret, $id, $productId, $MasterKey, $taskStatus = "", $searchValue = "", $pageNow = "", $pageSize = "")
    {
        $path="/aep_software_upgrade_management/details";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;
        $param["taskStatus"]=$taskStatus;
        $param["searchValue"]=$searchValue;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20200529233212";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey,产品概况中查看
    public static function QuerySoftwareUpgradeTask($appKey, $appSecret, $id, $productId, $MasterKey)
    {
        $path="/aep_software_upgrade_management/task";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;

        $version ="20200529233136";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，产品概况可以查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateSoftwareUpgradeTask($appKey, $appSecret, $MasterKey, $body)
    {
        $path="/aep_software_upgrade_management/task";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=null;
        $version ="20200529233123";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，在产品概况中查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function ModifySoftwareUpgradeTask($appKey, $appSecret, $id, $MasterKey, $body)
    {
        $path="/aep_software_upgrade_management/task";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;

        $version ="20200529233103";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，在产品概况中查看
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function ControlSoftwareUpgradeTask($appKey, $appSecret, $id, $MasterKey, $body)
    {
        $path="/aep_software_upgrade_management/control";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;

        $version ="20200529233046";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数updateBy: 类型String, 参数可以为空
    //  描述:修改人
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，在产品概况中查看
    public static function DeleteSoftwareUpgradeTask($appKey, $appSecret, $id, $productId, $MasterKey, $updateBy = "")
    {
        $path="/aep_software_upgrade_management/task";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;
        $param["updateBy"]=$updateBy;

        $version ="20200529233037";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数可以为空
    //  描述:主任务id,isSelectDevice为1时必填，为2不必填
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数isSelectDevice: 类型String, 参数不可以为空
    //  描述:查询类型（1.查询加入升级设备，2.查询可加入升级设备）
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页，默认1
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页显示数，默认20
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，产品概况中查看
    //参数deviceIdSearch: 类型String, 参数可以为空
    //  描述:根据设备id精确查询
    //参数deviceNameSearch: 类型String, 参数可以为空
    //  描述:根据设备名称精确查询
    //参数imeiSearch: 类型String, 参数可以为空
    //  描述:根据imei号精确查询，仅支持LWM2M协议
    //参数deviceGroupIdSearch: 类型String, 参数可以为空
    //  描述:根据群组id精确查询
    public static function QuerySoftwareUpradeDeviceList($appKey, $appSecret, $productId, $isSelectDevice, $MasterKey, $id = "", $pageNow = "", $pageSize = "", $deviceIdSearch = "", $deviceNameSearch = "", $imeiSearch = "", $deviceGroupIdSearch = "")
    {
        $path="/aep_software_upgrade_management/devices";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;
        $param["isSelectDevice"]=$isSelectDevice;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;
        $param["deviceIdSearch"]=$deviceIdSearch;
        $param["deviceNameSearch"]=$deviceNameSearch;
        $param["imeiSearch"]=$imeiSearch;
        $param["deviceGroupIdSearch"]=$deviceGroupIdSearch;

        $version ="20200529233027";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:
    //参数productId: 类型long, 参数不可以为空
    //  描述:
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:
    public static function QuerySoftwareUpgradeDetail($appKey, $appSecret, $id, $productId, $MasterKey)
    {
        $path="/aep_software_upgrade_management/detail";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["id"]=$id;
        $param["productId"]=$productId;

        $version ="20200529233010";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数，默认1
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页显示数，默认20
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey，产品概况中查看
    //参数searchValue: 类型String, 参数可以为空
    //  描述:查询条件，支持主任务名称模糊查询
    public static function QuerySoftwareUpgradeTaskList($appKey, $appSecret, $productId, $MasterKey, $pageNow = "", $pageSize = "", $searchValue = "")
    {
        $path="/aep_software_upgrade_management/tasks";
        $headers=array();
        $headers["MasterKey"]=$MasterKey;

        $param=array();
        $param["productId"]=$productId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;
        $param["searchValue"]=$searchValue;

        $version ="20200529232940";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
