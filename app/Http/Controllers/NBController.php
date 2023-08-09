<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NBController extends BaseController
{
    public function NbWarm(Request $request)
    {
        $params = $request->query();

        // 用于验证签名
        $msg       = $request->query('msg');
        $nonce     = $request->query('nonce');
        $signature = $request->query('signature');

        if ($this->checkSign($msg, $nonce, $signature)) {
            Log::info('success:' . json_encode($params));
            echo $msg;
        }
        Log::info('failed:' . json_encode($params));
    }

    public function received(Request $request)
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
                    $value = $msg['value']; // 具体数据部分，为设备上传至平台或触发的相关数据
                    $this->handleValue($value);
                    break;
            }



            return response()->json(['message' => 'Success']);
        }
        Log::info('failed:' . json_encode($data));
    }

    private function handleValue($value){
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

    /**
     * 验签
     * @param string $msg
     * @param string $nonce
     * @param string $signature
     * @return bool
     */
    private function checkSign(string $msg, string $nonce, string $signature): bool
    {
        $token = env('NB_TOKEN');

        $sign = base64_encode(md5($token . $nonce . $msg, true));

        Log::info('sign:' . $sign);
        Log::info('signature:' . $signature);

        // 验证token
        if ($signature === $sign) {
            return true;
        }
        return false;
    }

    private function aesDecrypt($encryptedData)
    {
        return openssl_decrypt($encryptedData, 'AES-128-CBC', env('NB_KEY'), OPENSSL_RAW_DATA, env('NB_KEY'));
    }
}
