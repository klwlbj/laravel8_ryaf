<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_upgrade_management
{
    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    public static function QueryRemoteUpgradeDetail($appKey, $appSecret, $id, $productId, $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/detail";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["id"]        = $id;
        $param["productId"] = $productId;

        $version = "20190615001517";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    public static function QueryRemoteUpgradeTask($appKey, $appSecret, $id, $productId, $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/task";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["id"]        = $id;
        $param["productId"] = $productId;

        $version = "20190615001509";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function ControlRemoteUpgradeTask($appKey, $appSecret, $id, $body, $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/control";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param       = [];
        $param["id"] = $id;

        $version = "20190615001456";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型String, 参数可以为空
    //  描述:主任务id,isSelectDevice为1时必填，为2不必填
    //参数productId: 类型String, 参数不可以为空
    //  描述:产品id
    //参数isSelectDevice: 类型String, 参数不可以为空
    //  描述:查询类型（1.查询加入升级设备，2.查询可加入升级设备）
    //参数pageNow: 类型String, 参数可以为空
    //  描述:当前页，默认1
    //参数pageSize: 类型String, 参数可以为空
    //  描述:每页显示数，默认20
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    //参数deviceIdSearch: 类型String, 参数可以为空
    //  描述:根据设备id精确查询
    //参数deviceNameSearch: 类型String, 参数可以为空
    //  描述:根据设备名称精确查询
    //参数imeiSearch: 类型String, 参数可以为空
    //  描述:根据imei号精确查询，仅支持LWM2M协议
    //参数deviceNoSearch: 类型String, 参数可以为空
    //  描述:根据设备编号精确查询，仅支持T_Link协议
    //参数deviceGroupIdSearch: 类型String, 参数可以为空
    //  描述:根据群组id精确查询
    public static function QueryRemoteUpradeDeviceList($appKey, $appSecret, $productId, $isSelectDevice, $id = "", $pageNow = "", $pageSize = "", $MasterKey = "", $deviceIdSearch = "", $deviceNameSearch = "", $imeiSearch = "", $deviceNoSearch = "", $deviceGroupIdSearch = "")
    {
        $path                 = "/aep_upgrade_management/devices";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param                        = [];
        $param["id"]                  = $id;
        $param["productId"]           = $productId;
        $param["isSelectDevice"]      = $isSelectDevice;
        $param["pageNow"]             = $pageNow;
        $param["pageSize"]            = $pageSize;
        $param["deviceIdSearch"]      = $deviceIdSearch;
        $param["deviceNameSearch"]    = $deviceNameSearch;
        $param["imeiSearch"]          = $imeiSearch;
        $param["deviceNoSearch"]      = $deviceNoSearch;
        $param["deviceGroupIdSearch"] = $deviceGroupIdSearch;

        $version = "20190615001451";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
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
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    public static function DeleteRemoteUpgradeTask($appKey, $appSecret, $id, $productId, $updateBy = "", $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/task";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param              = [];
        $param["id"]        = $id;
        $param["productId"] = $productId;
        $param["updateBy"]  = $updateBy;

        $version = "20190615001444";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null) {
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
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    //参数searchValue: 类型String, 参数可以为空
    //  描述:查询条件，支持主任务名称模糊查询
    public static function QueryRemoteUpgradeTaskList($appKey, $appSecret, $productId, $pageNow = "", $pageSize = "", $MasterKey = "", $searchValue = "")
    {
        $path                 = "/aep_upgrade_management/tasks";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param                = [];
        $param["productId"]   = $productId;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;
        $param["searchValue"] = $searchValue;

        $version = "20190615001440";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function ModifyRemoteUpgradeTask($appKey, $appSecret, $id, $body, $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/task";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param       = [];
        $param["id"] = $id;

        $version = "20190615001433";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "PUT");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateRemoteUpgradeTask($appKey, $appSecret, $body, $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/task";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20190615001416";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function OperationalRemoteUpgradeTask($appKey, $appSecret, $body, $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/operational";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20190615001412";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数id: 类型long, 参数不可以为空
    //  描述:主任务id
    //参数productId: 类型long, 参数不可以为空
    //  描述:产品id
    //参数taskStatus: 类型long, 参数可以为空
    //  描述:子任务状态
    //    T-Link协议必填（1.待升级，2.升级中，3.升级成功，4.升级失败）
    //    LWM2M协议选填（0:未启动,1:等待升级,2:升级执行中,3:升级成功,4:升级失败,5:取消升级）
    //参数searchValue: 类型String, 参数可以为空
    //  描述:查询值，设备ID或设备编号(IMEI)或设备名称模糊查询
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页码
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页显示数
    //参数MasterKey: 类型String, 参数可以为空
    //  描述:MasterKey
    public static function QueryRemoteUpgradeSubtasks($appKey, $appSecret, $id, $productId, $taskStatus = "", $searchValue = "", $pageNow = "", $pageSize = "", $MasterKey = "")
    {
        $path                 = "/aep_upgrade_management/details";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param                = [];
        $param["id"]          = $id;
        $param["productId"]   = $productId;
        $param["taskStatus"]  = $taskStatus;
        $param["searchValue"] = $searchValue;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;

        $version = "20190615001406";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
