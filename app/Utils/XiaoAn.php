<?php

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class XiaoAn
{
    protected static $api = 'https://guard-open-api.eye4.cn/mod_open/v1/open/device/sendSommand';

    protected static $secretKey = 'Y8i9k2s326e2h89q2F5';
    public function getSign($data)
    {
        $imei = $data['IMEI'] ?? '';
        $timestamp = $data['timestamp'] ?? '';
        $data = Tools::jsonEncode($data['data'] ?? '');

        // 拼接字符串
        $str = $imei . $timestamp . $data . self::$secretKey;


        return md5($str);
    }

    public function checkSign($params,$sign = '')
    {
        $paramsSign = $this->getSign($params);
        print_r($paramsSign);die;
        return ($paramsSign == $sign);
    }

    public function analysisReport($params)
    {
        $data = Tools::jsonDecode($params['data']);

        if(empty($data)){
            return ['code' => -1,'message' => 'data数据为空'];
        }
    }

    public static function sendRequest($data,$method = "GET",$header = [])
    {
        $client = new Client([
            'verify' => false, // 关闭 SSL 验证
        ]);

        try {
            $response = $client->request($method, self::$api, [
                'query'   => $data,
//            'json'    => $data,
                'headers' => $header,
            ]);
        } catch (RequestException $e) {
            return false;
        }


        if ($response->getStatusCode() === 200) {
            return Tools::jsonDecode($response->getBody()->getContents());
        }else{
            return false;
        }
    }

    public function sendCommand($imei,$data)
    {
        $req = [
            'IMEI' => $imei,
            'timestamp' => time(),
            'data' => Tools::jsonEncode($data),
        ];

        $sign = $this->getSign($req);

        $res = $this->sendRequest($req,'POST');
        print_r($res);die;
    }


}
