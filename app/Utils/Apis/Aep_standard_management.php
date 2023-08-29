<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_standard_management
{
    //参数standardVersion: 类型String, 参数可以为空
    //  描述:标准物模型版本号
    //参数thirdType: 类型long, 参数不可以为空
    //  描述:三级分类Id
    public static function QueryStandardModel($appKey, $appSecret, $thirdType, $standardVersion = "")
    {
        $path="/aep_standard_management/standardModel";
        $headers=null;
        $param=array();
        $param["standardVersion"]=$standardVersion;
        $param["thirdType"]=$thirdType;

        $version ="20190713033424";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
