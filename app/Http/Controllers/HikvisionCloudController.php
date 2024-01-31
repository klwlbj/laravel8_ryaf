<?php

namespace App\Http\Controllers;

use App\Http\Server\HikvisionICloud;

class HikvisionCloudController
{
    public function index()
    {
        $h      = new HikvisionICloud(env('HIK_KEY'), env('HIK_SECRET'));
        $params = ['msgType' => ''];
        // $params = [];
        # 取token  用于大批量操作，但是一定要确保token没有过期，自行处理token过期与重新获取
        // var_dump($h->getToken());
        # 取人员列表，第三个参数可不传 使用签名
        var_dump($h->doRequest('/api/subscription/v2/list', $params));
    }
}
