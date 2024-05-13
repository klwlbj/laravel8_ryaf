<?php

namespace App\Utils;

class HttpRequest
{
    /**
     * @param $url
     * @param string $method
     * @param null $data
     * @param null $header
     * @return mixed
     */
    public static function httpRequest($url, $method = "POST", $data = null, $header = null){
        $curl = curl_init();
        if(!empty($header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_HEADER, 0);//返回response头部信息
        }


        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);


        if (!empty($data)) {
            if(strtolower($method) == 'post'){
                curl_setopt($curl, CURLOPT_HTTPGET, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
            }else{
                $url = self::jointUrl($url,$data);
            }
        }
//        print_r($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        self::$curlInfo = curl_getinfo($curl);
        curl_close($curl);
        return $output;
    }

    /**拼接字符串
     * @param $url
     * @param array $data
     * @return bool|string
     */
    private static function jointUrl($url, $data = [])
    {
        if (empty($data)) {
            return $url;
        }
        foreach ($data as $key => $value) {
            $url .= $key . '=' . $value . '&';
        }
        $url = substr($url, 0, strlen($url) - 1);
        return $url;
    }
}
