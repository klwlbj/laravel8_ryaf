<?php

namespace App\Utils\Apis;

use App\Utils\Apis\Core\AepSdkCore;

class Aep_mq_sub
{
    public static function QueryServiceState($appKey, $appSecret)
    {
        $path    = "/aep_mq_sub/mqStat";
        $headers = null;
        $param   = null;
        $version = "20201218144210";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function OpenMqService($appKey, $appSecret, $body)
    {
        $path    = "/aep_mq_sub/mqStat";
        $headers = null;
        $param   = null;
        $version = "20201217094438";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数topicId: 类型long, 参数不可以为空
    //  描述:
    public static function QueryTopicInfo($appKey, $appSecret, $topicId)
    {
        $path             = "/aep_mq_sub/topic";
        $headers          = null;
        $param            = [];
        $param["topicId"] = $topicId;

        $version = "20201218153403";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数topicId: 类型long, 参数不可以为空
    //  描述:
    public static function QueryTopicCacheInfo($appKey, $appSecret, $topicId)
    {
        $path             = "/aep_mq_sub/topic/cache";
        $headers          = null;
        $param            = [];
        $param["topicId"] = $topicId;

        $version = "20201218150354";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    public static function QueryTopics($appKey, $appSecret)
    {
        $path    = "/aep_mq_sub/topics";
        $headers = null;
        $param   = null;
        $version = "20201218153456";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function QuerySubRules($appKey, $appSecret, $body)
    {
        $path    = "/aep_mq_sub/rule";
        $headers = null;
        $param   = null;
        $version = "20201218160237";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null) {
            return $response;
        }
        return null;
    }

    public static function ClosePushService($appKey, $appSecret)
    {
        $path    = "/aep_mq_sub/mqStat";
        $headers = null;
        $param   = null;
        $version = "20201217141937";

        $application = $appKey;
        $secret      = $appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "DELETE");
        if ($response != null) {
            return $response;
        }
        return null;
    }
}
