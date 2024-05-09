<?php

namespace App\Http\Server\Platform;

use App\Http\Server\BaseServer;
use Illuminate\Support\Facades\Redis;

class Auth extends BaseServer
{
    public static $secret = 'abcdefg';
    public static $operatorId = null;
    public static $token = '';


    public function checkSign($datetime,$operatorId,$signature): bool
    {
        if(!empty(self::$token)){
            $str = "datetime: " . $datetime . "\noperatorid: " . $operatorId . "\ntoken: " . self::$token;
        }else{
            $str = "datetime: " . $datetime . "\noperatorid: " . $operatorId;
        }

        $sign = $this->getSign($str);
        // print_r($sign);die;
        if($sign != $signature){
            return false;
        }

        return true;
    }


    public function getSign($str): string
    {
        $sign = hash_hmac('sha256', $str, self::$secret);

        return base64_encode($sign);
    }

    public function getToken(){
        $token = md5(time() . rand(10,99) . self::$operatorId);

        Redis::set('platform_token:' . $token,self::$operatorId);

        return $token;
    }
}
