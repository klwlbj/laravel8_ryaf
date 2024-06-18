<?php

namespace App\Utils;

class HikvisionSmoke
{
    public const HK_PARAM_TYPE = [
        '0001' => ['name' => '燃气浓度', 'unit' => '%LEL'],
        '0015' => ['name' => '电量', 'unit' => '%'], //电量
        '0017' => ['name' => '烟雾浓度', 'unit' => 'db/m'], // 烟雾浓度
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

    public function parseString(string $string)
    {
        $parsedData = [];

        $substrArray = [
            // ['字段名','字段长度','字段是否需要解析']
            // ['start', 2, false],
            // 01心跳 02报警 03消音 05故障
            ['byMessageId', 2, true], // 业务类型
            ['byFixedSign', 2, false], // 扩展位

            ['byDevType', 2, false], // 设备类型低8位
            ['byMac', 12, false], // Mac地址
            ['byTime', 8, true], // 时间戳
            ['byDevTypeEx', 2, false], // 设备类型高8位
            ['wPCI', 4, true], // 小区编号
            ['bySnr', 2, true], // 网络质量参数
            ['byEcl', 2, true], // 网络质量参数
            ['wRsrp', 4, true], // 网络质量参数
            ['dwUpHeaderLen', 8, true], // 上报协议
            ['dwPackageNo', 8, false], // 包序号
            ['byQCCID', 40, true], // QCCID号
            ['byIMEI', 40, true], // IMEI号
            ['byIMSI', 40, true], // IMSI号
            ['byNBModuleVersion', 48, false], // NB模块版本
            ['dwCID', 8, false], // 基站码
            ['dwLAC', 8, false], // 地区区域码
            ['bySoftwareVersion', 40, true], // 软件版本
            ['byHardwareVersion', 40, true], // 硬件版本
            ['byDeviceModel', 40, true], // 设备型号
            ['byProtocolVersion', 20, true], // 协议版本
            ['keepByte1', 2, false], //
            // 0无事件 1报警 2报警复位 3故障 4故障复位 5消音 6自检 7信号查询 8复位 9注册 11注销 12 屏蔽 13 解除屏蔽 14偷盗报警复位 15本地按键消音 16遥控器消音 17远程报警消音 18远程故障消音 19微波检测活跃度 20设备调零 21指示灯开启 22指示灯关闭
            ['MsgType', 2, true],
            ['keepByte2', 4, false],
            ['dataBytes', 4, true], // 数据总字节数
            ['channels', 4, true], // 通道数
            ['definiteChannels', 4, false], // 具体通道数

            // 固定6通道，针对fy300
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

    /**
     * 根据二进制位，判断值
     * @param string $string
     * @param array $typeArray
     * @return array
     */
    private function getBitValue(string $string, array $typeArray): array
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

    private function hexToStr($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return trim($string);
    }
}
