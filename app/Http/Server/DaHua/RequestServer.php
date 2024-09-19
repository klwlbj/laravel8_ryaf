<?php

namespace App\Http\Server\DaHua;

use App\Http\Server\BaseServer;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class RequestServer extends BaseServer
{
    public $basePath = 'https://openapi.wisualarm.com/';
    public $contentType = 'application/json';

    protected $clientId = 'refc09758138d541798a8639c2353584d7';
    protected $clientSecret = '881f6fde189144f75f3b39dcfb880b61';

    public function doRequest($path, $params = [],$method = 'POST')
    {
        $token = $this->getToken();
        if(!$token){
            return false;
        }
        $fullPath = $this->basePath . $path;
        $client   = new Client(['verify' => false]);

//        print_r($params);die;
        if(mb_strtoupper($method) == 'POST'){
            $response = $client->post($fullPath, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    "Content-Type" => $this->contentType,
                ],
                'json'    => (object)$params, // 将关联数组转换为 JSON 对象,PHP空数组转空对象
            ]);
        }else{
//            print_r($this->jointUrl($fullPath,$params));die;
            $response = $client->get($this->jointUrl($fullPath,$params), [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    "Content-Type" => $this->contentType,
                ],
            ]);
        }


        return json_decode($response->getBody(), true);
    }

    /**拼接字符串
     * @param $url
     * @param array $data
     * @return bool|string
     */
    private function jointUrl($url, $data = [])
    {
        if (empty($data)) {
            return $url;
        }
        $url .= '?' . http_build_query($data);

        return $url;
    }

    public function getToken()
    {
        $key = 'dahua_token';
        $token = Cache::get($key);
        if(!empty($token)){
            return $token;
        }

        $fullPath = $this->basePath . 'auth/oauth/token?grant_type=client_credentials&scope=server&client_id='.$this->clientId.'&client_secret='.$this->clientSecret;
//        $fullPath = 'https://openapi.wisualarm.com/auth/oauth/token?grant_type=client_credentials&scope=server&client_id=refc09758138d541798a8639c2353584d7&client_secret=881f6fde189144f75f3b39dcfb880b61';
        $client   = new Client(['verify' => false]);
        $response = $client->post($fullPath, [
            'headers' => [
                "Content-Type" => 'application/x-www-form-urlencoded',
            ],
            'json'    => (object)[

            ], // 将关联数组转换为 JSON 对象,PHP空数组转空对象
        ]);

        $res = json_decode($response->getBody(), true);

        if(!isset($res['access_token'])){
            Response::setMsg('获取token失败');
            return false;
        }

        $token = $res['access_token'];

        #如果剩余时间少于3600 则不存进缓存
        if($res['expires_in'] >= 3600){
            Cache::put($key,$token,$res['expires_in'] - 3600);
        }

        return $token;
    }
}
