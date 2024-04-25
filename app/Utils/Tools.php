<?php

namespace App\Utils;

class Tools
{
    /**解析json字符串
     * @param $data
     * @return mixed
     */
    public static function jsonDecode($data){
        if(is_array($data)){
            return $data;
        }

        $newData = json_decode($data,true);
        if(is_array($newData)){
            return $newData;
        }

        return $data;
    }

    /**
     * @param $data
     * @param int $option
     * @return string
     */
    public static function jsonEncode($data, $option = 256){
        if(!is_array($data)){
            return $data;
        }

        return json_encode($data,$option);
    }
}
