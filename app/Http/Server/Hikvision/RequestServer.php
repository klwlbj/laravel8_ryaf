<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;
use App\Http\Server\HikvisionICloud;
use GuzzleHttp\Client;

class RequestServer extends BaseServer
{
    public $basePath = 'https://www.hikfirecloud.com/api/ncg/';
    public $contentType = 'application/json';
    public function doRequest($path, $params = [])
    {

        $fullPath = $this->basePath . $path;
        // $sign     = Auth::getInstance()->getSign();
        $sign    = (new HikvisionICloud())->generateAuthInfo('22689412', 'cs81nNQGSqGeubA7sCL3');
        $client   = new Client(['verify' => false]);
        $response = $client->post($fullPath, [
            'headers' => [
                "Client-Token" =>  $sign,
                "Content-Type" => $this->contentType,
            ],
            'json'    => (object)$params, // 将关联数组转换为 JSON 对象,PHP空数组转空对象
        ]);

        return json_decode($response->getBody(), true);
    }
}
