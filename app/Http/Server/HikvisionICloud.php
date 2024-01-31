<?php

namespace App\Http\Server;

use GuzzleHttp\Client;

class HikvisionICloud
{
    public $base_url      = "https://open.hikfirecloud.com";
    public $base_path     = "/artemis";
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
            'json'    => $params, // 将关联数组转换为 JSON 对象
        ]);

        return json_decode($response->getBody(), true);
    }
}
