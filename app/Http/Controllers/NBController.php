<?php

namespace App\Http\Controllers;

use App\Utils\Haiman;
use Illuminate\Http\Request;
use App\Models\DeviceCacheCommands;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Foundation\Application;

/**
 * NB手报专用
 */
class NBController extends BaseController
{
    /**
     * 移动NB手报测试回调地址
     * @param Request $request
     * @return void
     */
    public function nbWarm(Request $request)
    {
        $params = $request->query();

        // 用于验证签名
        $msg = $request->query('msg');
        // $nonce     = $request->query('nonce');
        // $signature = $request->query('signature');

        // if ($this->checkSign($msg, $nonce, $signature)) {
        Log::info('success:' . json_encode($params));
        echo $msg;
        // }
    }

    /**
     * 电信海曼烟感4G回调地址
     * @param Request $request
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hmCTWing4GWarm(Request $request, string $url)
    {
        $jsonData = $request->all();
        Log::channel('haiman')->info("海曼电信4G {$url}:" . json_encode($jsonData));
        return response('', 200);
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

        // if ($this->checkSign($msg, $nonce, $signature)) {
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

            // Log::info('msg:' . json_encode($msg));
        $imei = $msg['deviceName'] ?? ($msg['dev_name'] ?? 0); // 设备imei
        $type = $msg['type'] ?? 0;
        if (!empty($imei) && $type == 2) { // 心跳包时才下发
            // 从命令缓存表中，获取命令，马上下发
            DeviceCacheCommands::where('imei', $imei)->where('is_success', 0)->get()->each(function ($item) use ($data) {
                // 下发命令
                $res = (new Haiman())->mufflingByOneNet($item->json);
                if ($res['code'] == 0) {
                    $item->is_success = 1;
                    $item->msg_id     = $data['id'] ?? '';
                    $item->save();
                }
            });
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * 移动NB手报真实回调地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function nbReceived(Request $request)
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

            $type = $msg['type'] ?? 1; // 固定值:数据点数据1 或 生命周期数据2

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
                    $this->handleNbValue($value);
                    break;
            }

            return response()->json(['message' => 'Success']);
        }
        Log::info('failed:' . json_encode($data));
    }

    private function handleNbValue($value)
    {
        $value = $this->parseStringToArray($value);
        Log::info('value:' . json_encode($value));

        $msgType = $value['msg_type'];// event或heartbeat
        switch ($msgType) {
            case 'event':
                /*
                    事件类型:
                    55AA00015A --报警
                    55AA00025A --低电量报警
                    55AA00035A --心跳/恢复正常
                    55AA00045A --拆除
                    55AA00055A --测试
                    55AA00075A --故障
                    55AA00085A --温度报警
                    55AA000A5A --心跳
                    55AA000D5A --防拆恢复
                */
                $payload     = $value['payload'];
                $temperature = $value['temperature']; // 设备温度
                $voltage     = $value['voltage']; // 设备电压
                $csq         = $value['csq']; //信号强度
                break;
            case "heartbeat":
                $sw          = $value['sw']; // 软件版本号
                $hw          = $value['hw']; // 硬件版本号
                $imei        = $value['imei']; // 设备imei
                $imsi        = $value['imsi']; // 设备imsi
                $iccid       = $value['iccid']; // 物联网卡的iccid
                $csq         = $value['csq']; // 信号强度
                $temperature = $value['temperature']; // 设备温度
                $voltage     = $value['voltage']; // 设备电压
                $txPower     = $value['tx_power']; // 终端发射功率
                $earfcn      = $value['earfcn']; // 绝对频点号
                $band        = $value['band']; // 频带
                $PCI         = $value['PCI']; // 物理小区标识
                $ECL         = $value['ECL']; // 小区唯一标识
                $SNR         = $value['SNR']; // 信噪比
                $rsrq        = $value['rsrq']; // 参考信号接收质量测量值
                $cellID      = $value['cellID']; // 基站小区标识
                break;
        }

        // 对各value进行处理 todo
        return;
    }

    /**
     * 字符串转数组
     * @param string $inputString
     * @return array
     */
    private function parseStringToArray(string $inputString): array
    {
        $result = [];
        $pairs  = explode(",", $inputString); // 按逗号分割字符串得到键值对数组

        foreach ($pairs as $pair) {
            $split        = explode(":", $pair); // 按冒号分割每个键值对
            $key          = trim($split[0]); // 去除键的前后空格
            $value        = trim($split[1]); // 去除值的前后空格
            $result[$key] = $value; // 添加到结果数组中
        }

        return $result;
    }
}
