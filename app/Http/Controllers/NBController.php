<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NBController extends BaseController
{
    public const HK_PARAM_TYPE = [
        '0001' => ['name' => '燃气浓度', 'unit' => '%LEL'],
        '0015' => ['name' => '电量', 'unit' => '%'], //电量
        '0017' => ['name' => '烟雾浓度', 'unit' => '%'], // 烟雾浓度
        '0018' => ['name' => '污染程度', 'unit' => '%'], // 污染程度
        '0004' => ['name' => '温度', 'unit' => '*0.1摄氏度'], // 温度
        '001a' => ['name' => '湿度', 'unit' => '%RH'], // 湿度
        '002e' => ['name' => '水汽浓度', 'unit' => '%'], // 水汽浓度
        '0036' => ['name' => '232', 'unit' => 'asd'], // 燃气浓度
    ];

    // byMessageId
    public const HK_PARAM_MESSAGE_NAME = [
        '01' => '心跳',
        '02' => '报警',
        '03' => '消音',
        '05' => '故障',
        '0f' => '操作响应',
        '11' => '信息上报',
    ];

    public const HK_PARAM_MESSAGE_MSG_TYPE = [
        '无事件',
        '报警',
        '报警复位',
        '故障',
        '故障复位',
        '消音',
        '自检',
        '信号查询',
        '复位',
        '注册',
        '注销',
        '屏蔽',
        '解除屏蔽',
        '偷盗报警复位',
        '本地按键消音',
        '遥控器消音',
        '远程报警消音',
        '远程故障消音',
        '微波检测活跃度',
        '设备调零',
        '指示灯开启',
        '指示灯关闭',
    ];
    public const HK_PARAM_FAULT_TYPE = [
        '寿命超期',
        '传感器短路/断路故障',
        '传感器失联故障',
        '低电压',
        '设备被拆下',
        '迷宫污染',
        '用电传感器',
        '通信故障',
        '传感器故障',
        '欠压故障',
        '过压故障',
        '过流故障',
        '寿命超期',
        '缺相故障',
        '错相故障',
        '传感器短路故障',
        '传感器断路故障',
        '传感器错接故障',
        '主备电故障',
        '休眠故障',
        'GPS故障',
        '蓝牙故障',
        '蜂鸣器故障',
        '故障联动（开关量输出）',
        '微波检测故障',
        '开卡参数异常/故障',
        '断电故障（被检测的abc相断电，支持恢复）',
        '欠压预警',
    ];

    public const HK_PARAM_WARM_TYPE = [
        '烟雾报警',
        '燃气报警',
        '剩余电流报警',
        '温度报警',
        '水压报警',
        '液位报警',
        '波动报警',
        '上限报警',
        '下限报警',
        '电弧报警',
        '联动输入报警',
        '手报报警',
        '声光报警',
        '碰撞报警',
        '倾斜报警',
        '偷盗报警',
        '报警联动（开关量输出）',
        '脱扣联动（开关量输出）',
        '微波检测有人报警（火警或三合一模式下产生）',
        '微波检测无人报警（养老模式下产生）',
        '断电报警（设备掉电）',
        '有功功率过载报警',
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

    public function dhCTWingWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('dhctwingWarm:' . json_encode($jsonData));

        return response('', 200);
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
            // 01心跳 02报警 03消音 05故障
            ['byMessageId', 2, true],
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
            // 0无事件 1报警 2报警复位 3故障 4故障复位 5消音 6自检 7信号查询 8复位 9注册 11注销 12 屏蔽 13 解除屏蔽 14偷盗报警复位 15本地按键消音 16遥控器消音 17远程报警消音 18远程故障消音 19微波检测活跃度 20设备调零 21指示灯开启 22指示灯关闭
            ['MsgType', 2, true],
            ['keepByte1', 4, false],
            ['dataBytes', 4, true], // 数据总字节数
            ['channels', 4, true], // 通道数
            ['definiteChannels', 4, false], // 具体通道数

            ['channelNo1', 4, false],
            ['channel1', 4, false],
            ['EventType1', 4, true],
            ['EventValue1', 8, true],
            ['paramType1', 4, true],
            ['value1', 4, true],

            ['channelNo2', 4, false],
            ['channel2', 4, false],
            ['EventType2', 4, true],
            ['EventValue2', 8, true],
            ['paramType2', 4, true],
            ['value2', 4, true],

            ['channelNo3', 4, false],
            ['channel3', 4, false],
            ['EventType3', 4, true],
            ['EventValue3', 8, true],
            ['paramType3', 4, true],
            ['value3', 4, true],

            ['channelNo4', 4, false],
            ['channel4', 4, false],
            ['EventType4', 4, true],
            ['EventValue4', 8, true],
            ['paramType4', 4, true],
            ['value4', 4, true],

            ['channelNo5', 4, false],
            ['channel5', 4, false],
            ['EventType5', 4, true],
            ['EventValue5', 8, true],
            ['paramType5', 4, true],
            ['value5', 4, true],

            ['channelNo6', 4, false],
            ['channel6', 4, false],
            ['EventType6', 4, true],
            ['EventValue6', 8, true],
            ['paramType6', 4, true],
            ['value6', 4, true],
        ];

        foreach ($substrArray as $value) {
            static $offset = 0;
            if ($value[2] === true) {
                $littleString = substr($string, $offset, $value[1]);
                switch ($value[0]) {
                    case 'byMessageId':
                        $parsedData['byMessageId']   = $littleString;
                        $parsedData['byMessageName'] = self::HK_PARAM_MESSAGE_NAME[$littleString] ?? '';
                        break;
                    case 'MsgType':
                        $parsedData['MsgType']     = $littleString;
                        $parsedData['MsgTypeName'] = self::HK_PARAM_MESSAGE_MSG_TYPE[(int) $littleString] ?? '';
                        break;
                    case 'EventType1':
                    case 'EventType2':
                    case 'EventType3':
                    case 'EventType4':
                    case 'EventType5':
                    case 'EventType6':
                        $lastChar                                          = substr($value[0], -1);
                        $parsedData['channel'][$lastChar - 1]['eventType'] = $littleString;
                        break;
                    case 'EventValue1':
                    case 'EventValue2':
                    case 'EventValue3':
                    case 'EventValue4':
                    case 'EventValue5':
                    case 'EventValue6':
                        $lastChar = substr($value[0], -1);
                        // 16进制转2进制
                        $eventValue                                         = base_convert($littleString, 16, 2);
                        $parsedData['channel'][$lastChar - 1]['eventValue'] = $eventValue;
                        switch ($parsedData['channel'][$lastChar - 1]['eventType']) {
                            case 62:
                                break;
                            case 63:
                                $parsedData['channel'][$lastChar - 1]['fault'] = $this->getBitValue($eventValue, self::HK_PARAM_FAULT_TYPE);
                                break;
                            case 64:
                                $parsedData['channel'][$lastChar - 1]['warm'] = $this->getBitValue($eventValue, self::HK_PARAM_WARM_TYPE);
                                break;
                        }

                        break;
                    case 'paramType1':
                    case 'paramType2':
                    case 'paramType3':
                    case 'paramType4':
                    case 'paramType5':
                    case 'paramType6':
                        $lastChar                                              = substr($value[0], -1);
                        $parsedData['channel'][$lastChar - 1]['paramType']     = $littleString;
                        $parsedData['channel'][$lastChar - 1]['paramTypeName'] = self::HK_PARAM_TYPE[$littleString]['name'] ?? '';
                        $parsedData['channel'][$lastChar - 1]['paramTypeUnit'] = self::HK_PARAM_TYPE[$littleString]['unit'] ?? '';
                        break;
                    case 'value6':
                    case 'value5':
                    case 'value4':
                    case 'value3':
                    case 'value2':
                    case 'value1':
                        $lastChar = substr($value[0], -1);
                        $string   = $littleString;
                        // if($littleString === 'FFFF' || $littleString === 'ffff'){
                        //     // 可变长
                        //     // $offset += $value[1];
                        // }
                        $parsedData['channel'][$lastChar - 1]['paramValue'] = hexdec($string);
                        break;

                    case 'byTime':
                        $parsedData[$value[0]] = date('Y-m-d H:i:s', hexdec($littleString));
                        break;
                    case 'wPCI':
                    case 'bySnr':
                    case 'byEcl':
                    case "channels":
                        $parsedData[$value[0]] = hexdec($littleString);
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
        // dd($parsedData);

        return $parsedData;
    }

    private function getBitValue($string, $typeArray)
    {
        $returnArray = [];
        foreach (str_split($string) as $key => $bit) {
            if ($bit == 1) {
                $returnArray[] = $typeArray[$key];
            }
            return $returnArray;
        }

        return $returnArray;
    }

    /**
     * 解析16进制字符串（测试用）
     * @param string $string
     * @return array
     */
    public function analyze(string $string)
    {
        return $this->parseString(strtolower($string));
    }
}
