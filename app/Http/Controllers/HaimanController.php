<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Utils\Apis\Aep_device_command;
use Illuminate\Contracts\Foundation\Application;

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
        $data        = $request->input();
        $msg         = json_decode($data['msg'], true);
        $data['msg'] = $msg;
        $nonce       = $data['nonce'];

        // Log::info('data:' . json_encode($data));
        Log::channel('haiman')->info("海曼移动4G msg:" . json_encode([
            'msg'   => $msg,
            'nonce' => $nonce,
            'time'  => $data['time'],
            'id'    => $data['id'],
        ]));

        $imei = $msg['deviceName'] ?? ($msg['dev_name'] ?? 0); // 设备imei
        if (!empty($imei) /*&& $type == 2*/) { // 心跳包时才下发 todo
            // 区分消息类型
            if (isset($msg['dev_name']) && $msg['type'] == 2) {
                // 在离线状态 不处理
                $infoType = 1;
            }
            if (isset($msg['notifyType']) && $msg['notifyType'] === 'event' && isset($msg['deviceName'])) {
                // 事件上报 不处理
                $infoType = 2;
            }
            if (isset($msg['notifyType']) && $msg['notifyType'] === 'property' && isset($msg['deviceName'], $msg['data']['params']) && count($msg['data']['params']) !== 5) {
                // 设备属性变更
                $infoType = 3;
                // 保存消息
                $this->insertIOT($data, $imei, $infoType);
            }
            // 从命令缓存表中，获取命令，马上下发
            $this->getAndSendDeviceCacheCMD($imei, $data['id'] ?? '', 3);
        }

        return response()->json(['message' => 'Success']);
    }

    public function insertSmokeDetector(string $imei)
    {
        $smde_type       = "烟感";
        $smde_brand_name = "海曼";
        $smde_model_name = "HM-618PH-4G";
        $smde_model_tag  = "";
        $smde_part_id    = 1; // 如约自己的设备
        return DB::connection('mysql2')->table('smoke_detector')->insert([
            "smde_type"       => $smde_type,
            "smde_brand_name" => $smde_brand_name,
            "smde_model_name" => $smde_model_name,
            "smde_imei"       => $imei,
            "smde_model_tag"  => $smde_model_tag,
            "smde_part_id"    => $smde_part_id,
        ]);
    }
}
