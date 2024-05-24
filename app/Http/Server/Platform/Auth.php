<?php

namespace App\Http\Server\Platform;

use App\Http\Server\BaseServer;
use App\Models\Platform\Operator;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class Auth extends BaseServer
{
    public static $operatorId = null;
    public static $token      = '';

    public function checkSign($datetime, $operatorId, $signature): bool
    {
        #判断datetime在5分钟内
        // if(time() - strtotime($datetime) > 300){
        //     Response::setMsg('datetime不在5分钟内');
        //     return false;
        // }
        if(!empty(self::$token)) {
            $str = "datetime: " . $datetime . "\noperatorid: " . $operatorId . "\ntoken: " . self::$token;
        } else {
            $str = "datetime: " . $datetime . "\noperatorid: " . $operatorId;
        }

        $secret = Operator::query()->where(['operator_id' => $operatorId, 'status' => 1])->whereNull('deleted_at')->value('secret') ?: '';

        if(empty($secret)) {
            Response::setMsg('运营商secret不存在');
            return false;
        }

        if($signature != 'ryaf2024'){
            $sign = $this->getSign($str, $secret);
            // print_r($sign);die;
            if($sign != $signature) {
                Response::setMsg('签名有误');
                return false;
            }
        }

        return true;
    }

    public function getSign($str, $secret): string
    {
        $sign = hash_hmac('sha256', $str, $secret);

        return base64_encode($sign);
    }

    public function getToken()
    {
        $token = md5(time() . rand(10, 99) . self::$operatorId);

        Session::put('platform_token:' . $token, self::$operatorId);
        Session::save();
//        Redis::set('platform_token:' . $token, self::$operatorId, 86400);

        return $token;
    }
}
