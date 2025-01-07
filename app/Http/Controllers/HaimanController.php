<?php

namespace App\Http\Controllers;

use App\Utils\Haiman;
use App\Utils\Tools;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceCacheCommands;
use App\Utils\Apis\Aep_device_command;
use Illuminate\Support\Facades\Log;

class HaimanController extends BaseController
{
    public function mufflingByOneNet($imei)
    {
        // 拼接json数据
        $json = json_encode([
            "device_name" => $imei,
            "product_id"  => 'E2dMYR85jh', // 写死
            'identifier'  => 'set_mute',
            'params'      => [
                'mute' => 1,
            ],
        ]);

        return $this->insertDeviceCacheCMD($imei, $json);
    }

    public function mufflingByCTWing($productId, $deviceId, $masterKey, $cmd = '1f560002157c')
    {
        #获取结果日志
        return Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'dataType' => 1,
                    "payload"  => $cmd,
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
    }

    /**
     * 移动海曼烟感4G回调地址
     * @param Request $request
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hmOneNet4GWarm(Request $request)
    {
        $data  = $request->input();
        $msg   = $data['msg'];
        $nonce = $data['nonce'];
        // $signature = $data['signature'];

        // if ($this->checkSign($msg, $nonce, $signature)) { // todo验签
        Log::info('data:' . json_encode($data));

        // 解密处理
        // $msg = $this->aesDecrypt(base64_decode($msg));
        Log::info('msg2:' . $msg);

        $msg = json_decode($msg, true);
        Log::channel('haiman')->info("海曼移动4G msg:" . json_encode([
                'msg'   => $msg,
                'nonce' => $nonce,
                'time'  => $data['time'],
                'id'    => $data['id'],
            ]));

        $imei = $msg['deviceName'] ?? ($msg['dev_name'] ?? 0); // 设备imei
        // $type = $msg['type'] ?? 0;
        // $status = $msg['status'] ?? 0;
        if (!empty($imei) /*&& $type == 2*/) { // 心跳包时才下发 todo
            // 从命令缓存表中，获取命令，马上下发
            $this->getAndSendDeviceCacheCMD($imei, $data['id'] ?? '');
        }

        return response()->json(['message' => 'Success']);
    }
}
