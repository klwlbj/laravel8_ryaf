<?php

namespace App\Http\Server;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChangpingServer extends BaseServer
{
    // public string $url = 'http://beijing.xfwlw119.com:8980/openapitest/v2/gateway';
    public string $url = 'http://beijing.xfwlw119.com:8980/openapi/v2/gateway';

    public function sendRequest($method, $data)
    {
        // 第三方平台要求的参数
        $appId     = config('services.changping.account');
        $appKey    = config('services.changping.password');
        $timestamp = Carbon::now()->timestamp; // 获取当前 Unix 时间戳

        // 定义缓存的键名
        $key = 'cp_counter';

        // 使用 cache 的 increment 方法实现自增
        $random = Cache::increment($key);

        // 如果是第一次访问，初始化计数器
        if ($random === 1) {
            Cache::put($key, 1); // 默认缓存 1 分钟
        }

        // 计算 sign
        $sign = md5("appKey={$appKey}&timestamp={$timestamp}&random={$random}");

        // 请求参数
        $params = [
            'appId'     => $appId,
            'method'    => $method,
            'timestamp' => $timestamp,
            'random'    => $random,
            'sign'      => $sign,
            'data'      => $data,
        ];

        // dd(json_encode($params));

        // 发送请求到第三方接口
        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
        ])->post($this->url, $params);

        // 处理响应
        if ($response->successful()) {
            return $response->body();
        }
        // 请求失败，处理错误信息
        dd($response->status(), $response->body());
        // return $response->body();
    }
}
