<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_device_control
{
    //参数MasterKey: 类型String, 参数不可以为空
    //  描述:MasterKey在该设备所属产品的概况中可以查看
    //参数productId: 类型long, 参数不可以为空
    //  描述:
    //参数searchValue: 类型String, 参数可以为空
    //  描述:可选填：设备Id
    //参数type: 类型long, 参数可以为空
    //  描述:可选填枚举值：
    //    1：设备重启
    //    2：退出平台
    //    3：重新登录
    //    4：设备自检
    //    6：开始发送数据
    //    7：停止发送数据
    //    8：恢复出厂设置
    //参数status: 类型long, 参数可以为空
    //  描述:可选填：1：指令已保存
    //    2：指令已发送
    //    3：指令已送达
    //    4：指令已完成
    //    999：指令发送失败
    //参数startTime: 类型String, 参数可以为空
    //  描述:精确到毫秒的时间戳
    //参数endTime: 类型String, 参数可以为空
    //  描述:精确到毫秒的时间戳
    //参数pageNow: 类型long, 参数可以为空
    //  描述:当前页数
    //参数pageSize: 类型long, 参数可以为空
    //  描述:每页记录数
    public static function QueryRemoteControlList($appKey, $appSecret, $MasterKey, $productId, $searchValue = "", $type = "", $status = "", $startTime = "", $endTime = "", $pageNow = "", $pageSize = "")
    {
        $path                 = "/aep_device_control/controls";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param                = [];
        $param["productId"]   = $productId;
        $param["searchValue"] = $searchValue;
        $param["type"]        = $type;
        $param["status"]      = $status;
        $param["startTime"]   = $startTime;
        $param["endTime"]     = $endTime;
        $param["pageNow"]     = $pageNow;
        $param["pageSize"]    = $pageSize;

        $version = "20190507012630";

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
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateRemoteControl($appKey, $appSecret, $MasterKey, $body)
    {
        $path                 = "/aep_device_control/control";
        $headers              = [];
        $headers["MasterKey"] = $MasterKey;

        $param   = null;
        $version = "20181031202247";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
