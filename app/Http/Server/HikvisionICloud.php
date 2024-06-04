<?php

namespace App\Http\Server;

use Exception;
use GuzzleHttp\Client;

class HikvisionICloud
{
    // public $base_url      = "https://open.hikfirecloud.com";
    // public $base_path     = "/artemis";
    public $base_url = "https://www.hikfirecloud.com/api/ncg";

    public $base_path     = "";
    protected $app_key    = "";
    protected $app_secret = "";
    public $content_type  = "application/json";
    public $accept        = "*/*";

    public function __construct($app_key = '', $app_secret = '')
    {
        $this->app_key    = $app_key;
        $this->app_secret = $app_secret;
    }

    /**
     * 以appSecret为密钥，使用HmacSHA256算法对签名字符串生成消息摘要，对消息摘要使用BASE64算法生成签名（签名过程中的编码方式全为UTF-8）
     */
    public function getSign($url)
    {
        $next     = "\n";
        $sign_str = "POST" . $next .
            $this->accept . $next .
            $this->content_type . $next .
            "x-ca-key:" . $this->app_key . $next
            . $url;

        return base64_encode(hash_hmac('sha256', $sign_str, $this->app_secret, true)); //生成消息摘要
    }

    public function doRequest2($path, $params = [])
    {
        $fullpath = $this->base_path . $path;
        $token    = $this->generateAuthInfo('22689412', 'cs81nNQGSqGeubA7sCL3');

        $client   = new Client(['verify' => false]);
        $response = $client->post($this->base_url . $fullpath, [
            'headers' => [
                "Client-Token" => $token,
                "Content-Type" => $this->content_type,
            ],
            'json'    => (object) $params, // 将关联数组转换为 JSON 对象,PHP空数组转空对象
        ]);

        return json_decode($response->getBody(), true);
    }

    public function doRequest($path, $params = [])
    {
        $fullpath = $this->base_path . $path;
        $sign     = $this->getSign($fullpath);

        $client   = new Client(['verify' => false]);
        $response = $client->post($this->base_url . $fullpath, [
            'headers' => [
                "Accept"                 => $this->accept,
                "Content-Type"           => $this->content_type,
                "X-Ca-Key"               => $this->app_key,
                "X-Ca-Signature"         => $sign,
                "X-Ca-Signature-Headers" => "x-ca-key",
            ],
            'json'    => (object) $params, // 将关联数组转换为 JSON 对象,PHP空数组转空对象
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * 生成认证字符串
     * 生成摘要字符串
     * 生成头信息
     */
    public static function generateAuthInfo($ak, $sk)
    {
        try {
            // 第一步：获取系统的时间戳（自1970年1月1日0时起的毫秒数）
            $time = round(microtime(true) * 1000);

            // 第二步：对(ak + 时间戳)求base64编码的数据
            $base64String = base64_encode($ak . $time);

            // 第三步：使用sk作为密钥，对上面这个base64后的数据求sha256加密后的值，结果要求是16进制的字符串
            $digestStr = self::hmacSHA256Signature($base64String, $sk);

            // 第四步：对(sha256加密后的值 + ":" + ak + ":" + 时间戳)求base64的信息，最终得到认证信息
            return base64_encode($digestStr . ":" . $ak . ":" . $time);
        } catch (Exception $e) {
            echo "FireDigestUtil.generateAuthInfo error";
        }

        return null;
    }

    public static function hmacSHA256Signature($message, $secret)
    {
        // $signature = "";
        $bytes2    = hash_hmac('sha256', $message, $secret, true);
        $signature = bin2hex($bytes2);
        return $signature;
    }
}
