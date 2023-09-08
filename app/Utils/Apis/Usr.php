<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Usr
{
    //参数sdk_type: 类型String, 参数可以为空
    //  描述:SDK语言类型，默认为Java(字典项: sdk_type)
    //参数file_name: 类型String, 参数不可以为空
    //  描述:SDK描述，用以标识其中的biz包
    //参数application_id: 类型String, 参数不可以为空
    //  描述:应用编码，下载的SDK会根据该编码收集所有有权限的API打包
    //参数api_version: 类型String, 参数可以为空
    //  描述:API版本信息 TODO
    public static function SdkDownload($appKey, $appSecret, $file_name, $application_id, $sdk_type = "", $api_version = "")
    {
        $path                    = "/usr/sdk/download";
        $headers                 = null;
        $param                   = [];
        $param["sdk_type"]       = $sdk_type;
        $param["file_name"]      = $file_name;
        $param["application_id"] = $application_id;
        $param["api_version"]    = $api_version;

        $version = "20180000000000";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
