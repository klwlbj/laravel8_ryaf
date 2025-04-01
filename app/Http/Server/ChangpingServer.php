<?php

namespace App\Http\Server;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ChangpingServer extends BaseServer
{
    public string $url = 'http://beijing.xfwlw119.com:8980/openapitest/v2/gateway';
    public function sendRequest($method, $data)
    {
        // 第三方平台要求的参数
        $appId = 'b98543e13bfb46db98784eb18eee14ee'; // 替换为实际的appId
        $appKey = 'ZAIT/D5BpdZVwX5AmSkOm9uuIjM='; // 替换为实际的appKey
        $timestamp = Carbon::now()->timestamp; // 获取当前 Unix 时间戳

        // 定义缓存的键名
        $key = 'cp_counter';

        // 使用 cache 的 increment 方法实现自增
        $random = Cache::increment($key);

        // 如果是第一次访问，初始化计数器
        if ($random === 1) {
            Cache::put($key, 1, 60); // 默认缓存 1 分钟
        }

        // 计算 sign
        $sign = md5("appKey={$appKey}&timestamp={$timestamp}&random={$random}");

        // 请求参数
        $params = [
            'appId' => $appId,
            'method' => $method,
            'timestamp' => $timestamp,
            'random' => $random,
            'sign' => $sign,
            'data' => $data,
        ];

        // 发送请求到第三方接口
        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
        ])->post($this->url, $params);

        // 处理响应
        if ($response->successful()) {
            return $response->body();
        } else {
            // 请求失败，处理错误信息
            return 222;
        }
    }
}
