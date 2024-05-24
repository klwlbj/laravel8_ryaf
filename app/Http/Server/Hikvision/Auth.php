<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;

class Auth extends BaseServer
{
    public $ak = '22689412';
    public $sk = 'cs81nNQGSqGeubA7sCL3';

    public function getSign()
    {
        $time = round(microtime(true) * 1000);

        // 第二步：对(ak + 时间戳)求base64编码的数据
        $base64String = base64_encode($this->ak . $time);

        // 第三步：使用sk作为密钥，对上面这个base64后的数据求sha256加密后的值，结果要求是16进制的字符串
        $digestStr = $this->hmacSHA256Signature($base64String, $this->sk);

        // 第四步：对(sha256加密后的值 + ":" + ak + ":" + 时间戳)求base64的信息，最终得到认证信息
        return base64_encode($digestStr . ":" . $this->ak . ":" . $time);
    }

    public function hmacSHA256Signature($message,$secret)
    {
        $bytes2    = hash_hmac('sha256', $message, $secret, true);
        $signature = bin2hex($bytes2);
        return $signature;
    }
}
