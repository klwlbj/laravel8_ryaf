<?php

namespace App\Utils;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class HuiXiao
{
    public const  CONNETCTION_TYPE = [
        '01' => '网线通道',
        '02' => '无线通道',
        '03' => '预留',
        '04' => '预留',
    ];

    public const SYSTEM_TYPE = [
        '01' => '火灾自动报警系统',
        '02' => '自动喷水灭火系统',
        '03' => '消火栓系统',
        '04' => '电气火灾监控系统',
        '05' => '可燃气体探测系统',
        '06' => '防火门监控系统',
        '07' => '电源监控系统',
        '08' => '智慧电力',
        '09' => '智慧农业',
        '0a' => '防排烟系统',
        '0B' => '应急照明',
        '0C' => '自定义1',
        '0D' => '自定义2',
        '0E' => '自定义3',
        '0F' => '自定义4',
    ];

    public const INFO_TYPE = [
        // 信息类型小于20H时，状态字代表探测器类型，范围0X00-0XFF
        // 信息类型等于20H时，状态字代表消防主机状态，等于21H时，状态字代表用户信息传输装置状态
        '00' => '未定义',
        '01' => '火警',
        '02' => '监管',
        '03' => '启动',
        '04' => '反馈',
        '05' => '隔离（屏蔽）',
        '06' => '取消隔离（取消屏蔽）',
        '07' => '故障',
        '08' => '故障恢复（消除）',
        '09' => '动作',
        '0A' => '停止',
        '0B' => '预警',
        '0C' => '预警恢复',
        '0D' => '报警',
        '0E' => '报警恢复',
        '0F' => '停动',
        '10' => '离线',
        '11' => '离线恢复',
        '12' => '测试',
        '13' => '结束测试',
        '14' => '报脏',
        '15' => '反馈恢复',
        '16' => '设备自检',
        '17' => '监管恢复',
        '18' => '预留',
        '19' => '预留',
        '1A' => '预留',
        '1B' => '预留',
        '1C' => '留',
        '1D' => '预留',
        '1E' => '预留',
        '1F' => '预留',
        '20' => '消防主机状态',
        '21' => '用户信息传输装置状态',
    ];

    public const STATUS = [
        '00' => '未定义',
        '01' => '复位',
        '02' => '主电故障',
        '03' => '主电恢复',
        '04' => '备电故障',
        '05' => '备电恢复',
        '06' => '手动允许',
        '07' => '自动允许',
        '08' => '喷洒允许',
        '09' => '本次开机',
        '0A' => '上次关机',
        '0B' => '自检',
        '0C' => '全关',
        '0D' => '直控禁止',
        '0E' => '直控允许',
        '0F' => '手动禁止',
        '10' => '自动禁止',
        '11' => '喷洒禁止',
        '12' => '总线故障',
        '13' => '延时',
        '14' => '延时状态',
        '15' => '延时结束',
        '16' => '自检结束',
        '17' => '全开',
        '18' => '消音',
        '19' => '全部自动',
        '1A' => '全部确认',
        '1B' => '值班在岗',
        '1C' => '值班漏岗',
        '1D' => '测试',
        '1E' => '预留',
        '1F' => '预留',
        '20' => '部分自动',
        '21' => '模拟联动',
        '22' => '监控状态',
        '23' => '调试状态',
        '24' => '用户登录',
        '25' => '人工火警',
        '26' => '通信故障',
        '27' => '通信恢复',
        '28' => '链路故障',
        '29' => '链路恢复',
        '2A' => '连接故障',
        '2B' => '连接恢复',
        '2C' => '预留',
        '2D' => '预留',
    ];

    /*    public const EQUIPMENT_TYPE = [
            '00'  => '未定义',
            '01'  => '离子感烟',
            '02'  => '点型感温',
            '03'  => '点型感烟',
            '04'  => '报警接口',
            '05'  => '可燃气体',
            '06'  => '红外对射',
            '07'  => '紫外感光',
            '08'  => '缆式感温',
            '09'  => '模拟感温',
            '10'  => '复合探测',
            '11'  => '手动按钮',
            '12'  => '消防广播',
            '13'  => '讯响器',
            '14'  => '消防电话',
            '15'  => '消火栓',
            '16'  => '消火栓泵',
            '17'  => '喷淋泵',
            '18'  => '稳压泵',
            '19'  => '排烟机',
            '20'  => '送风机',
            '21'  => '新风机',
            '22'  => '防火阀',
            '23'  => '排烟阀',
            '24'  => '送风阀',
            '25'  => '电磁阀',
            '26'  => '卷帘门中',
            '27'  => '卷帘门下',
            '28'  => '防火门',
            '29'  => '压力开关',
            '30'  => '水流指示',
            '31'  => '电梯',
            '32'  => '空调机组',
            '33'  => '柴油发电',
            '34'  => '照明配电',
            '35'  => '动力配电',
            '36'  => '水幕电磁',
            '37'  => '气体启动',
            '38'  => '气体停动',
            '39'  => '从机',
            '40'  => '火灾示盘',
            '41'  => '闸阀',
            '42'  => '高位水箱',
            '43'  => '泡沫泵',
            '44'  => '消防电源',
            '45'  => '紧急照明',
            '46'  => '疏导指示',
            '47'  => '喷洒指示',
            '48'  => '防盗模块',
            '49'  => '信号碟阀',
            '50'  => '防排烟阀',
            '51'  => '水幕泵',
            '52'  => '层号灯',
            '53'  => '设备停动',
            '54'  => '泵故障',
            '55'  => '急启按钮',
            '56'  => '急停按钮',
            '57'  => '雨淋泵',
            '58'  => '上位机',
            '59'  => '回路',
            '60'  => '空压机',
            '61'  => '联动电源',
            '62'  => '多线盘锁',
            '63'  => '部分设备',
            '64'  => '雨淋阀',
            '65'  => '感温棒',
            '66'  => '故障输出',
            '67'  => '环路开关',
            '68'  => '外控允许',
            '69'  => '外控禁止',
            '70'  => '备用指示',
            '71'  => '门灯',
            '72'  => '备用工作',
            '73'  => '内部设备',
            '74'  => '紧急求助',
            '75'  => '时钟电源',
            '76'  => '声光警报',
            '77'  => '报警传输',
            '78'  => '环路开关',
            '79'  => '广播支线',
            '80'  => '挡烟垂壁',
            '81'  => '消火栓阀',
            '82'  => '温度传感',
            '83'  => '吸气感烟',
            '84'  => '吸气火警',
            '85'  => '吸气预警',
            '86'  => '末端试水',
            '87'  => '未定义',
            '88'  => '模拟感温',
            '89'  => '漏电报警',
            '90'  => '总线',
            '91'  => '未定义',
            '92'  => '未定义',
            '93'  => '未定义',
            '94'  => '接地故障',
            '95'  => '联动公式',
            '96'  => '未定义',
            '97'  => '交流电源',
            '98'  => '备用电源',
            '99'  => '键盘',
            '100' => '湿式报警阀',
            '101' => '疏散门',
            '102' => '水位液位',
            '103' => '输入模块',
            '104' => '输出模块',
            '105' => '输入/输出模块',
            '106' => '中继模块',
            '107' => '未定义',
        ];*/

    // 心跳包字符串分隔规则
    public const HEART_BEAT_SUBSTR_ARRAY = [
        // ['字段名','字段长度','类型']
        'gateway_id'       => ['name' => '网关ID', 'length' => 5],
        "connetction_type" => ['name' => '通讯类型', 'length' => 1, 'type' => 'enum', "append_list" => self::CONNETCTION_TYPE],
        'datetime'         => ['name' => '时间', 'length' => -6, 'type' => 'datetime'],
    ];

    public const ALARM_BEAT_SUBSTR_ARRAY = [
        'gateway_id'       => ['name' => '网关ID', 'length' => 5],
        "connetction_type" => ['name' => '通讯类型', 'length' => 1, 'type' => 'enum', "append_list" => self::CONNETCTION_TYPE],
        'system_type'      => ['name' => '系统类型', 'length' => 1, 'type' => 'enum', 'append_list' => self::SYSTEM_TYPE],
        'info_type'        => ['name' => '信息类型', 'length' => 1, 'type' => 'enum', 'append_list' => self::INFO_TYPE],
        "status"           => ['name' => '状态字', 'length' => 1, 'type' => 'enum', 'append_list' => self::STATUS],
        'probe_code'       => ['name' => '探测器编码', 'length' => 6, 'type' => 'ascii'], // 0表示可变长度，暂不解析
        'position'         => ['name' => '位置信息', 'length' => 0],
        'datetime'         => ['name' => '时间', 'length' => -6, 'type' => 'datetime'],
    ];

    /**
     * 解析字符串
     * @param string $string
     * @return array|false
     */
    public function parseString(string $string)
    {
        $parsedData = [];

        $string = str_replace(' ', '', $string);// 去除空格
        $i = 0;
        while (!empty($string)) {
            if (str_starts_with($string, '7a7a')) {
                // 心跳包
                $startStr  = '7a7a';
                $strLength = 28;// 截取固定位数
                $parsedData[$i]['type'] = ['value' => 'heart_beat', 'name' => '消息类型'];
                $substrArray        = self::HEART_BEAT_SUBSTR_ARRAY;
            } elseif (str_starts_with($string, '7b7b7b')) {
                // 报警
                $startStr           = '7b7b7b';
                $strLength = 118;// 截取固定位数
                $parsedData[$i]['type'] = ['value' => 'alarm', 'name' => '消息类型'];
                $substrArray        = self::ALARM_BEAT_SUBSTR_ARRAY;
            } else {
                return false;
            }
            $newString = Str::substr($string, 0, $strLength);
            // 获取开头位置和结尾位置
            $startPos = strpos($newString, $startStr);
            $endPos   = -2;
            // 截取子字符串
            $substring = substr($newString, $startPos + strlen($startStr), $endPos);

            // 校验和
            $checkSum = $this->checkSum(substr($newString, 0, -2));

            if (substr($newString, -2) != $checkSum) {
                return false;
            }


            $offset = 0;
            foreach ($substrArray as $key => $value) {
                $length        = $value['length'] * 2;
                $currentString = substr($substring, $offset, $length);
                if (isset($value['type']) && $value['type'] === 'datetime') {
                    $parsedData[$i][$key] = [
                        'value' => $this->hexToDateTime(substr($substring, $length)),
                        'name'  => $value['name'], ];
                } elseif (isset($value['type']) && $value['type'] === 'enum') {
                    $parsedData[$i][$key] = [
                        'original_value' => $currentString,
                        'value'          => $this->getEnum($currentString, $value['append_list']),
                        'name'           => $value['name'],
                    ];
                } elseif (isset($value['type']) && $value['type'] === 'ascii') {
                    $chunks    = str_split($currentString, 2);
                    $character = [];
                    foreach ($chunks as $chunk) {
                        $character[] = chr(hexdec($chunk));
                    }
                    // 查找子设备 todo
                    $userCode = implode($character);

                    $parsedData[$i][$key] = [
                        'original_value' => $currentString,
                        'value'          => $userCode,
                        'name'           => $value['name'],
                    ];
                } else {
                    $parsedData[$i][$key] = ['value' => $currentString, 'name' => $value['name']];
                }
                $offset += $length;
            }
            $i++;
            $string = Str::substr($string, $strLength);
        }


        return $parsedData;
    }

    public function getEnum($string, $enumList)
    {
        return $enumList[strtoupper($string)] ?? '';
    }

    public function hexToDateTime($hexString)
    {
        $decimalValues = str_split($hexString, 2);

        $year   = hexdec($decimalValues[0]);
        $month  = hexdec($decimalValues[1]);
        $day    = hexdec($decimalValues[2]);
        $hour   = hexdec($decimalValues[3]);
        $minute = hexdec($decimalValues[4]);
        $second = hexdec($decimalValues[5]);

        $datetime = Carbon::create(2000 + $year, $month, $day, $hour, $minute, $second);

        return $datetime->format('Y-m-d H:i:s');
    }

    /**
     * 校验和(小写)
     * @param $string
     * @return string
     */
    public function checkSum($string): string
    {
        // 将字符串按两个字符分割成数组元素
        $hexArray = str_split($string, 2);

        // 将每个数组元素从十六进制转换为十进制
        $decArray = array_map('hexdec', $hexArray);

        // 对数组中的所有元素求和
        $sum = array_sum($decArray);

        // 取和的低8位（和对256取模）
        $checksum = $sum % 256;

        return strtolower(str_pad(dechex($checksum), 2, '0', STR_PAD_LEFT));
    }
}
