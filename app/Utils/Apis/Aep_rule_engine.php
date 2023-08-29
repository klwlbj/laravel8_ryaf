<?php
namespace App\Utils\Apis;
use App\Utils\Apis\Core\AepSdkCore;



class Aep_rule_engine
{
    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function saasCreateRule($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/api/v2/rule/sass/createRule";
        $headers=null;
        $param=null;
        $version ="20200111000503";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数ruleId: 类型String, 参数可以为空
    //  描述:
    //参数productId: 类型String, 参数不可以为空
    //  描述:
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function saasQueryRule($appKey, $appSecret, $productId, $ruleId = "", $pageNow = "", $pageSize = "")
    {
        $path="/aep_rule_engine/api/v2/rule/sass/queryRule";
        $headers=null;
        $param=array();
        $param["ruleId"]=$ruleId;
        $param["productId"]=$productId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20200111000633";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function saasUpdateRule($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/api/v2/rule/sass/updateRule";
        $headers=null;
        $param=null;
        $version ="20200111000540";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function saasDeleteRuleEngine($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/api/v2/rule/sass/deleteRule";
        $headers=null;
        $param=null;
        $version ="20200111000611";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateRule($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/createRule";
        $headers=null;
        $param=null;
        $version ="20210327062633";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateRule($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/updateRule";
        $headers=null;
        $param=null;
        $version ="20210327062642";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function DeleteRule($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/deleteRule";
        $headers=null;
        $param=null;
        $version ="20210327062626";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数ruleId: 类型String, 参数不可以为空
    //  描述:
    //参数productId: 类型String, 参数可以为空
    //  描述:
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function GetRules($appKey, $appSecret, $ruleId, $productId = "", $pageNow = "", $pageSize = "")
    {
        $path="/aep_rule_engine/v3/rule/getRules";
        $headers=null;
        $param=array();
        $param["ruleId"]=$ruleId;
        $param["productId"]=$productId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20210327062616";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function GetRuleRunStatus($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/getRuleRunningStatus";
        $headers=null;
        $param=null;
        $version ="20210327062610";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateRuleRunStatus($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/modifyRuleRunningStatus";
        $headers=null;
        $param=null;
        $version ="20210327062603";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateForward($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/addForward";
        $headers=null;
        $param=null;
        $version ="20210327062556";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateForward($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/updateForward";
        $headers=null;
        $param=null;
        $version ="20210327062549";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function DeleteForward($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/deleteForward";
        $headers=null;
        $param=null;
        $version ="20210327062539";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数ruleId: 类型String, 参数不可以为空
    //  描述:
    //参数productId: 类型String, 参数可以为空
    //  描述:
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function GetForwards($appKey, $appSecret, $ruleId, $productId = "", $pageNow = "", $pageSize = "")
    {
        $path="/aep_rule_engine/v3/rule/getForwards";
        $headers=null;
        $param=array();
        $param["ruleId"]=$ruleId;
        $param["productId"]=$productId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20210327062531";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数ruleId: 类型String, 参数不可以为空
    //  描述:
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function GetWarns($appKey, $appSecret, $ruleId, $pageNow = "", $pageSize = "")
    {
        $path="/aep_rule_engine/v3/rule/getWarns";
        $headers=null;
        $param=array();
        $param["ruleId"]=$ruleId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20210423162903";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function DeleteWarn($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/deleteWarn";
        $headers=null;
        $param=null;
        $version ="20210423162859";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateWarn($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/updateWarn";
        $headers=null;
        $param=null;
        $version ="20210423162906";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateWarn($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/addWarn";
        $headers=null;
        $param=null;
        $version ="20210423162909";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function CreateAction($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/addAction";
        $headers=null;
        $param=null;
        $version ="20210423162837";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function UpdateAction($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/updateAction";
        $headers=null;
        $param=null;
        $version ="20210423162842";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数body: 类型json, 参数不可以为空
    //  描述:body,具体参考平台api说明
    public static function DeleteAction($appKey, $appSecret, $body)
    {
        $path="/aep_rule_engine/v3/rule/deleteAct";
        $headers=null;
        $param=null;
        $version ="20210423162848";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, $body, $version, $application, $secret, "POST");
        if ($response != null){
            return $response;
        }
        return null;
    }

    //参数ruleId: 类型String, 参数不可以为空
    //  描述:
    //参数pageNow: 类型long, 参数可以为空
    //  描述:
    //参数pageSize: 类型long, 参数可以为空
    //  描述:
    public static function GetActions($appKey, $appSecret, $ruleId, $pageNow = "", $pageSize = "")
    {
        $path="/aep_rule_engine/v3/rule/getActions";
        $headers=null;
        $param=array();
        $param["ruleId"]=$ruleId;
        $param["pageNow"]=$pageNow;
        $param["pageSize"]=$pageSize;

        $version ="20211028100156";

        $application=$appKey;
        $secret=$appSecret;

        $response = AepSdkCore::sendSDkRequest($path, $headers, $param, null, $version, $application, $secret, "GET");
        if ($response != null){
            return $response;
        }
        return null;
    }


}
