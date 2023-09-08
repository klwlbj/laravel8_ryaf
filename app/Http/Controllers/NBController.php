<?php

namespace App\Http\Controllers;

use App\Utils\CTWing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NBController extends BaseController
{
    public const HK_PARAM_TYPE = [
        '0015' => ['name' => '电量', 'unit' => '%'], //电量
        '0017' => ['name' => '烟雾浓度', 'unit' => '%'], // 烟雾浓度
        '0018' => ['name' => '污染程度', 'unit' => '%'], // 污染程度
        '0004' => ['name' => '温度', 'unit' => '*0.1摄氏度'], // 温度
        '001a' => ['name' => '湿度', 'unit' => '%RH'], // 湿度
        '002e' => ['name' => '水汽浓度', 'unit' => '%'], // 水汽浓度
    ];

    /**
     * 移动NB手报测试回调地址
     * @param Request $request
     * @return void
     */
    public function nbWarm(Request $request)
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

    /**
     * 电信海康烟感回调地址
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hkCTWingWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('ctwingWarm:' . json_encode($jsonData));
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
            $array = $this->parseString(strtolower($value));
            // 解析后处理todo
            Log::info('hk_ctwing_array:' . json_encode($array));
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
            $array = $this->parseString(strtolower($value));
            // 解析后处理todo
            Log::info('hk_ctwing_array 4G:' . json_encode($array));
        }
        return response('', 200);
    }

    /**
     * 移动海康烟感回调地址
     * @param Request $request
     * @return void
     */
    public function hkReceived(Request $request)
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
                    $array     = $this->parseString(strtolower($value));
                    Log::info('hk_array:' . json_encode($array));

                    break;
            }
        }
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

    private function hexToStr($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return trim($string);
    }

    private function parseString(string $string)
    {
        $parsedData = [];

        $substrArray = [
            // ['字段名','字段长度','字段是否需要解析']
            // ['start', 2, false],
            ['byMessageId', 2, false],
            ['byFixedSign', 2, false],
            ['byDevType', 2, false],
            ['byMac', 12, false],
            ['byTime', 8, true],
            ['byDevTypeEx', 2, false],
            ['wPCI', 4, true],
            ['bySnr', 2, true],
            ['byEcl', 2, true],
            ['wRsrp', 4, true],
            ['dwUpHeaderLen', 8, true],
            ['dwPackageNo', 8, false],
            ['byQCCID', 40, true],
            ['byIMEI', 40, true],
            ['byIMSI', 40, true],
            ['byNBModuleVersion', 48, false],
            ['dwCID', 8, false],
            ['dwLAC', 8, false],
            ['bySoftwareVersion', 40, true],
            ['byHardwareVersion', 40, true],
            ['byDeviceModel', 40, true],
            ['byProtocolVersion', 20, true],
            ['keepByte1', 2, false],
            ['MsgType', 2, false],
            ['keepByte1', 4, false],
            ['dataBytes', 4, true], // 数据总字节数
            ['channels', 4, true], // 通道数
            ['definiteChannels', 4, false], // 具体通道数

            ['channelNo1', 4, false],
            ['channel1', 4, false],
            ['EventType1', 4, false],
            ['EventValue1', 8, false],
            ['paramType1', 4, true],
            ['value1', 4, true],

            ['channelNo2', 4, false],
            ['channel2', 4, false],
            ['EventType2', 4, false],
            ['EventValue2', 8, false],
            ['paramType2', 4, true],
            ['value2', 4, true],

            ['channelNo3', 4, false],
            ['channel3', 4, false],
            ['EventType3', 4, false],
            ['EventValue3', 8, false],
            ['paramType3', 4, true],
            ['value3', 4, true],

            ['channelNo4', 4, false],
            ['channel4', 4, false],
            ['EventType4', 4, false],
            ['EventValue4', 8, false],
            ['paramType4', 4, true],
            ['value4', 4, true],

            ['channelNo5', 4, false],
            ['channel5', 4, false],
            ['EventType5', 4, false],
            ['EventValue5', 8, false],
            ['paramType5', 4, true],
            ['value5', 4, true],

            ['channelNo6', 4, false],
            ['channel6', 4, false],
            ['EventType6', 4, false],
            ['EventValue6', 8, false],
            ['paramType6', 4, true],
            ['value6', 4, true],
        ];

        foreach ($substrArray as $value) {
            static $offset = 0;
            if ($value[2] === true) {
                $littleString = substr($string, $offset, $value[1]);
                switch ($value[0]) {
                    case 'byTime':
                        $parsedData[$value[0]] = date('Y-m-d H:i:s', hexdec($littleString));
                        break;
                    case 'wPCI':
                    case 'bySnr':
                    case 'byEcl':
                    case 'value6':
                    case 'value5':
                    case 'value4':
                    case 'value3':
                    case 'value2':
                    case 'value1':
                        $parsedData[$value[0]] = hexdec($littleString);
                        break;
                    case 'paramType1':
                    case 'paramType2':
                    case 'paramType3':
                    case 'paramType4':
                    case 'paramType5':
                    case 'paramType6':
                        $parsedData[$value[0]] = self::HK_PARAM_TYPE[$littleString]['name'] ?? '';
                        break;
                    case 'wRsrp': // 信号强度
                        $parsedData[$value[0]] = hexdec($littleString) - 65536;
                        break;
                    case 'dwUpHeaderLen':
                        $parsedData[$value[0]] = $littleString;
                        break;
                    default:
                        $parsedData[$value[0]] = $this->hexToStr($littleString);
                        break;
                }
            } else {
                $parsedData[$value[0]] = substr($string, $offset, $value[1]);
            }
            $offset += $value[1];
        }

        return $parsedData;
    }

    public function analyze()
    {
        // return (new CTWing())->testSum();
        $string = '030182E0CA3C0156BE64EDB1210000051100FFB2000000BE00000000383938363131323232323430323233393934383738363135363130353630363031383800000000003436303131333232343133393632330000000000424332363059434E4141523032413031000000000000000007543D50000076506275696C6432333032313400000000000000000056312E30000000000000000000000000000000004E502D46593331302D4E00000000000000000000312E302E300000000000001102000013006000010061000100620000000000000000AD';

        // echo strtolower($string);die;
        return $this->parseString(strtolower($string));
    }
}
