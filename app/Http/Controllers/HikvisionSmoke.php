<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HikvisionSmoke extends BaseController
{
    public $client;

    public function __construct()
    {
        $this->client = new \App\Utils\HikvisionSmoke();
    }

    /**
     * 移动海康烟感回调地址
     * @param Request $request
     * @return void
     */
    public function hkOnenetWarm(Request $request)
    {
        $data      = $request->input();
        $msg       = $data['msg'];
        $nonce     = $data['nonce'];
        $signature = $data['signature'];

        if ($this->checkSign($msg, $nonce, $signature)) {
            Log::info('data:' . json_encode($data));

            // 解密处理
            $msg = $this->aesDecrypt(base64_decode($msg));
            Log::info('msg2:' . $msg);

            $msg = json_decode($msg, true);
            Log::info('msg:' . json_encode($msg));

            $type = $msg['type']; // 固定值:数据点数据1 或 生命周期数据2

            switch ($type) {
                case 2:
                    $devName   = $msg['dev_name']; //设备名
                    $pid       = $msg['pid']; // 产品id
                    $status    = $msg['status']; // 设备状态，1:在线；2:离线
                    $eventTime = $msg['at']; // 设备上报的时间戳
                    break;
                case 1:
                    $devName   = $msg['dev_name']; //设备名
                    $eventTime = $msg['at']; // 设备上报的时间戳
                    $imei      = $msg['imei']; // 编号
                    $pid       = $msg['pid']; // 产品id
                    $dsId      = $msg['ds_id'] ?? ''; // 数据 点id
                    $value     = $msg['value']; // 具体数据部分，为设备上传至平台或触发的相关数据
                    $array     = $this->client->parseString(strtolower($value));
                    Log::info('hk_array:' . json_encode($array));

                    break;
            }
        }
    }

    /**
     * 电信海康烟感回调地址
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hkCTWingWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::channel('hikvision_smoke')->info('ctwingWarm:' . json_encode($jsonData));
        $jsonData = json_decode(json_encode($jsonData), true);

        if (isset($jsonData['payload'][0]['serviceData'])) {
            $serviceData = $jsonData['payload'][0]['serviceData'];
        } elseif (isset($jsonData['payload']['serviceData'])) {
            $serviceData = $jsonData['payload']['serviceData'];
        } else {
            $serviceData = [];
        }

        $value = $serviceData['msg'] ?? ''; // 16进制字符串
        $imei  = $serviceData['IMEI'] ?? '';
        if (!empty($value)) {
            $array = $this->client->parseString(strtolower($value));
            // 解析后处理todo
            Log::channel('hikvision_smoke')->info('hk_ctwing_array:' . json_encode($array));
        }
        return response('', 200);
    }

    /**
     * 电信海康烟感4G回调地址
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hkCTWing4GWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('ctwingWarm 4G:' . json_encode($jsonData));
        $jsonData = json_decode(json_encode($jsonData), true);

        $value = $jsonData['payload']['msg'] ?? ''; // 16进制字符串
        $imei  = $serviceData['IMEI'] ?? '';
        if (!empty($value)) {
            $array = $this->client->parseString(strtolower($value));
            // 解析后处理todo
            Log::info('hk_ctwing_array 4G:' . json_encode($array));
        }
        return response('', 200);
    }

    /**
     * 解析16进制字符串（测试用）
     * @param string $string
     * @return array
     */
    public function analyze(string $string)
    {
        return $this->client->parseString(strtolower($string));
    }
}
