<?php

namespace App\Utils;

use DateTimeZone;
use Illuminate\Support\Str;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

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

    /**打印日志
     * @param $msg
     * @param null $path
     * @param array $data
     */
    public static function writeLog($msg, $path = null, $data = []){
        if(empty($path)){
            $actions=explode('\\', \Route::current()->getActionName());
            $func=explode('@', $actions[count($actions)-1]);
            $path=$func[0].'-'.$func[1];
        }
        $path = 'logs/'.$path.'/'.$path.'.log';

        if(!is_array($data) && !empty($data)){
            $data = ['data' => $data];
        }

        if(empty($data)){
            $data = array_merge($_GET,$_POST);
        }


        if(!is_array($data)){
            $data = self::jsonDecode($data);
        }

        (new Logger('daily',[],[],new DateTimeZone('Asia/Shanghai')))
            ->pushHandler(new RotatingFileHandler(storage_path($path),14))
            ->debug($msg,$data);
    }

    public static function snake($data): array
    {
        return collect($data)->mapWithKeys(function ($value, $key) {
            return [Str::snake($key) => $value];
        })->toArray();
    }

    public static function camel($data): array
    {
        return collect($data)->mapWithKeys(function ($v, $k) {
            return [Str::camel($k) => $v];
        })->toArray();
    }
}
