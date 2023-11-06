<?php

namespace App\Utils;

use ReflectionClass;
use Illuminate\Support\Facades\Log;

class DaHua
{
    /**
     * 命令字节
     */
    public const COMMAND_BYTE = [
        0 => '预留',
        1 => '控制命令',
        2 => '发送数据',
        3 => '确认',
        4 => '请求',
        5 => '应答',
        6 => '否认',
    ];

    public const SYSTEM_TYPE_FLAG = [
        0  => '通用',
        1  => '火灾报警系统',
        10 => '消防联动控制器',
        11 => '消防栓系统',
        12 => '自动喷水灭火系统',
        13 => '气体灭火系统',
        14 => '水喷雾灭火系统（泵启动方式）',
        15 => '水喷雾灭火系统（压力容器启动方式）',
        16 => '泡沫灭火系统',
        17 => '干粉灭火系统',
        18 => '防烟排烟系统',
        19 => '防火门及卷帘系统',
        20 => '消防电梯',
        21 => '消防应急广播',
        22 => '消防应急照明和疏散指示系统',
        23 => '消防电源',
        24 => '消防电话',
    ];

    public const UNIT_TYPE_FLAG = [
        0   => '通用',
        1   => '火灾报警控制器',
        10  => '可燃气体探铡器',
        11  => '点型可燃气体探测器',
        12  => '独立式可燃气体探测器',
        13  => '线型可燃气体探测器',
        16  => '电气火灾监控报警器',
        17  => '剩余电流式电气火灾监控探测器',
        18  => '测温式电气火灾监控探测器',
        21  => '探测回路',
        22  => '火灾显示盘',
        23  => '手动火灾报警按钮',
        24  => '消火栓按钮',
        25  => '火灾探测器',
        30  => '感温火灾探测器',
        31  => '点型感温火灾探测器',
        32  => '点型感温火灾探测器(s型)',
        33  => '点型感温火灾探测器(R型)',
        34  => '线型感温火灾探测器',
        35  => '线型感温火灾探测器(S型)',
        36  => '线型感温火灾探测器(R型)',
        37  => '光纤感温火灾探测器',
        40  => '感烟火灾探测器',
        41  => '点型离子感烟火灾探测器',
        42  => '点型光电感烟火灾探测器',
        43  => '线型光束感烟火灾探测器',
        44  => '吸气式感烟火灾探测器',
        50  => '复合式火灾探测器',
        51  => '复合式感烟感温火灾探测器',
        52  => '复合式感光感温火灾探浏器',
        53  => '复合式感光感烟火灾探测器',
        61  => '紫外火焰探测器',
        62  => '红外火焰探测器',
        69  => '感光火灾探测器',
        74  => '气体探测器',
        78  => '图像摄像方式火灾探测器',
        79  => '感声火灾探测器',
        81  => '气体灭火控制器',
        82  => '消防电气控制装置',
        83  => '消防控制室图形显示装置',
        84  => '模块',
        85  => '输入模块',
        86  => '输出模块',
        87  => '输入／输出模块',
        88  => '中继模块',
        91  => '消防水泵',
        92  => '消防水箱',
        95  => '喷淋泵',
        96  => '水流指示器',
        97  => '信号阀',
        98  => '报警阀',
        99  => '压力开关',
        101 => '阀驱动装置',
        102 => '防火门',
        103 => '防火阀',
        104 => '通风空调',
        105 => '泡沫液泵',
        106 => '管网电磁阀',
        111 => '防烟排烟风机',
        113 => '排烟防火阀',
        114 => '常闭送风口',
        115 => '排烟口',
        116 => '电控挡烟垂壁',
        117 => '防火卷帘控制器',
        118 => '防火门监控器',
        121 => '警报装置',
    ];

    public const TYPE_FLAG = [
        0  => '预留',
        1  => [
            'name'      => '上传建筑消防设施系统状态',
            'structure' => [
                // 名称=>字节数,0为可变长度
                'system_type_flag'  => 1,
                'system_address'    => 1,
                'bfpf_system_state' => 2,
                'time'              => 6,
            ],
        ],
        2  => [
            'name'      => '上传建筑消防设施部件运行状态',
            'structure' => [
                // 名称=>字节数
                'system_type_flag' => 1,
                'system_address'   => 1,
                'unit_type_flag'   => 1,
                'unit_address'     => 4,
                'bfpf_unit_state'  => 2,
                'unit_desc'        => 31,
                'time'             => 6,
            ],
        ],
        3  => [
            'name'      => '上传建筑消防设施部件模拟量',
            'structure' => [
                'system_type_flag' => 1,
                'system_address'   => 1,
                'unit_type_flag'   => 1,
                'unit_address'     => 4,
                'analog_type'      => 1,
                'analog_value'     => 2,
                'time'             => 2,
            ],
        ],
        4  => [
            'name'      => '上传建筑消防设施操作信息',
            'structure' => [
                'system_type_flag' => 1,
                'system_address'   => 1,
                'operate_info'     => 1,
                'operator'         => 1,
                'time'             => 6,
            ],
        ],
        5  => [
            'name'      => '上传建筑消防设施软件版本',
            'structure' => [
                'system_type_flag'  => 1,
                'system_address'    => 1,
                'main_version'      => 1,
                'secondary_version' => 1,
            ],
        ],
        6  => [
            'name'      => '上传建筑消防设施系统配置情况',
            'structure' => [
                'system_type_flag'   => 1,
                'system_address'     => 1,
                'system_desc_length' => 1,
                'system_desc'        => 0,
            ],
        ],
        7  => [
            'name'      => '上传建筑消防设施部件配置情况',
            'structure' => [
                'system_type_flag' => 1,
                'system_address'   => 1,
                'unit_type_flag'   => 1,
                'unit_address'     => 4,
                'unit_desc'        => 31,
            ],
        ],
        8  => [
            'name'      => '上传建筑消防设施系统时间',
            'structure' => [
                'system_type_flag' => 1,
                'system_address'   => 1,
                'time'             => 6,
            ],
        ],
        21 => [
            'name'      => '上传用户信息传输装置运行状态',
            'structure' => [
                'uit_running_state' => 1,
                'time'              => 6,
            ],
        ],
        24 => [
            'name'      => '上传用户信息传输装置操作信息',
            'structure' => [
                'operate_info' => 1,
                'operator'     => 1,
                'time'         => 6,
            ],
        ],
        25 => [
            'name'      => '上传用户信息传输装置软件版本',
            'structure' => [
                'main_version'      => 1,
                'secondary_version' => 1,
            ],
        ],
        26 => [
            'name'      => '上传用户信息传输装置配置',
            'structure' => [
                'setting_length' => 1,
                'setting_desc'   => 0,
            ],
        ],
        28 => [
            'name'      => '上传用户信息传输装置系统时间',
            'structure' => [
                '',
                'time' => 6,
            ],
        ],

        61 => '读建筑消防设施系统状态',
        62 => '读建筑消防设施部件运行状态',
        63 => '读建筑消防设施部件模拟量',
        64 => '读建筑消防设施操作信息',
        65 => '读建筑消防设施软件版本',
        66 => '读建筑消防设施系统配置',
        67 => '读建筑消防设施部件配置',
        68 => '读建筑消防设施系统时间',
        81 => '读用户信息传输装置运行状态',
        82 => '预留',
        83 => '预留',
        84 => '读用户信息传输装置操作信息',
        85 => '读用户信息传输装置软件版本',
        86 => '读用户信息传输装置配置',
        87 => '预留',
        88 => '上传用户信息传输装置系统时间',
        89 => '初始化用户信息传输装置',
        90 => '同步用户信息传输装置时钟',
        91 => '查岗命令',
    ];

    /**
     * 8.2.1.8
     * 用户信息传输装置运行状态
     */
    public const UIT_RUNNING_STATE = [
        [
            '测试状态',
            "正常监视",
        ],
        [
            '无火警',
            "火警",
        ],
        [
            '无故障',
            '故障',
        ],
        [
            '主电正常',
            '主电故障',
        ],
        [
            '备电正常',
            '备电故障',
        ],
        [
            '同信信道正常',
            '与监控中心通信信道故障',
        ],
        [
            '监测连接线路正常',
            '监测连接线路故障',
        ],
        ['预留'],
    ];

    public const BFPF_SYSTEM_STATE = [
        ["测试状态", "正常运行状态"],
        ["无火警", "火警"],
        ["无故障", "故障"],
        ["无屏蔽", "屏蔽"],
        ["无监管", "监管"],
        ["停止（关闭）", "启动（开启）"],
        ["无反馈", "反馈"],
        ["未延时", "延时状态"],
        ["主电正常", "主电故障"],
        ["备电正常", "备电故障"],
        ["总线正常", "总线故障"],
        ["自动状态", "手动状态"],
        ["无配置改变", "配置改变"],
    ];

    /**
     * 8.2.1.2
     * 建筑消防设施部件状态
     */
    public const BFPF_UNIT_STATE = [
        [
            "测试状态",
            "正常运行状态",
        ],
        [
            "无火警",
            "火警",
        ],
        [
            "无故障",
            "故障",
        ],
        [
            "无屏蔽",
            "屏蔽",
        ],
        [
            "无监管",
            "监管",
        ],
        [
            "停止（关闭）",
            "启动（开启）",
        ],
        [
            "无反馈",
            "反馈",
        ],
        [
            "未延时",
            "延时状态",
        ],
        [
            "电源正常",
            "电源故障",
        ],
    ];

    /**
     * 8.2.1.3
     * 建筑消防设施部件模拟量值
     */
    public const ANALOG_TYPE = [
        ["name" => "未用"],
        [
            "name"     => "事件计数",
            "unit"     => "件",
            "radius"   => "0~32000",
            "min_rage" => "1件",
        ],
        [
            "name"     => "高度",
            "unit"     => "m",
            "radius"   => "0~320",
            "min_rage" => "0.01",
        ],
        [
            "name"     => "温度",
            "unit"     => "℃",
            "radius"   => "-273~3200",
            "min_rage" => "0.1℃",
        ],
        [
            "name"     => "压力",
            "unit"     => "Mpa（兆帕）",
            "radius"   => "0~3200",
            "min_rage" => "0.1Mpa",
        ],
        [
            "name"     => "压力",
            "unit"     => "kPa（兆帕）",
            "radius"   => "0~3200",
            "min_rage" => "0.1kpa",
        ],
        [
            "name"     => "气体浓度",
            "unit"     => "％LEL",
            "radius"   => "0~100",
            "min_rage" => "0.1％LEL",
        ],
        [
            "name"     => "时间",
            "unit"     => "s",
            "radius"   => "0~32000",
            "min_rage" => "1s",
        ],
        [
            "name"     => "电压",
            "unit"     => "V",
            "radius"   => "0~3200",
            "min_rage" => "0.1V",
        ],
        [
            "name"     => "电流",
            "unit"     => "A",
            "radius"   => "0~3200",
            "min_rage" => "0.1V",
        ],
        [
            "name"     => "流量",
            "unit"     => "L/s",
            "radius"   => "0~3200",
            "min_rage" => "0.1L/s",
        ],
        [
            "name"     => "风量",
            "unit"     => "M3/min",
            "radius"   => "0~3200",
            "min_rage" => "M3/min",
        ],
        [
            "name"     => "风速",
            "unit"     => "M/s",
            "radius"   => "0~20",
            "min_rage" => "1m/s",
        ],
    ];

    /**
     * 8.2.1.9
     * 用户信息传输装置操作信息
     */
    public const OPERATE_INFO = [
        [
            "无操作",
            "复位",
        ],
        [
            "无操作",
            "消音",
        ],

        [
            "无操作",
            "手动报警",
        ],
        [
            "无操作",
            "警情消除",
        ],
        [
            "无操作",
            "自检",
        ],
        [
            "无操作",
            "确认",
        ],
        [
            "无操作",
            "测试",
        ],
        ["预留"],
    ];

    public const SUBSTR_ARRAY = [
        // ['字段名','字段长度','字段是否需要转10进制']
        ['业务流水号', 2, false],

        ['协议版本号', 2, false],
        // 时间标签
        ['时间标签', 6, true],

        ['源地址', 6, true],
        ['目的地址', 6, true],
        ['应用数据单元长度', 2, true],
        ['命令字节', 1, true],
        // 应用数据单元 不定长
        ['类型标志', 1, true],
        ['信息对象数目', 1, true],
        ['自定义字段', 0, true],
        // ...
    ];

    public function createCmd(string $no)
    {
        $year   = date('y');
        $month  = date('m');
        $day    = date('d');
        $hour   = date('H');
        $minute = date('i');
        $second = date('s');

        $string = $no . '0102' . sprintf("%02s", dechex($second)) . sprintf("%02s", dechex($minute)) . sprintf("%02s", dechex($hour)) . sprintf("%02s", dechex($day)) . sprintf("%02s", dechex($month)) . sprintf("%02s", dechex($year)) . '64000000000028249c330000000003';

        // 处理请求
        return '4040' . $string . $this->checkSum($string) . '2323';
    }

    public function parseString(string $string)
    {
        $parsedData = [];
        $startStr   = '4040';
        $endStr     = '2323';

        $string = str_replace(' ', '', $string);// 去除空格

        // 获取开头位置和结尾位置
        $startPos = strpos($string, $startStr);
        $endPos   = strpos($string, $endStr);

        // 截取子字符串
        $substring = substr($string, $startPos + strlen($startStr), $endPos - ($startPos + strlen($startStr)));

        // 校验和
        $checkSum = $this->checkSum(substr($string, 4, -6));

        if (substr($substring, -2) != $checkSum) {
            return false;
        }

        $substring = substr($substring, 0, -2);

        $customString = '';
        $infoOjbNum   = 0;

        $offset = 0;
        foreach (self::SUBSTR_ARRAY as $value) {
            if ($value[2] === true) {
                $littleString = substr($substring, $offset, $value[1] * 2);
                switch ($value[0]) {
                    case '源地址':
                    case '目的地址':
                        $parsedData[$value[0]] = $littleString;
                        break;
                    case '应用数据单元长度':
                        $len                   = strlen($littleString); // 字符串长度
                        $reversedStr           = substr($littleString, $len - 2) . substr($littleString, 0, $len - 2); // 构造反转后的字符串
                        $parsedData[$value[0]] = $parsedData['app_data_unit_length'] = hexdec($reversedStr);
                        break;
                    case '命令字节':
                        $parsedData[$value[0]] = self::COMMAND_BYTE[hexdec($littleString)] ?? '';
                        break;
                    case '时间标签':
                        $parsedData[$value[0]] = $this->strToTime($littleString);
                        break;
                    case '类型标志':
                        $typeFlag              = self::TYPE_FLAG[hexdec($littleString)] ?? '';
                        $typeFlagStructures    = $typeFlag['structure'] ?? [];
                        $parsedData[$value[0]] = $typeFlag['name'] ?? '预留';
                        break;
                    case '信息对象数目':
                        $parsedData[$value[0]] = $infoOjbNum = hexdec($littleString);
                        break;
                    case '自定义字段':
                        $customString = substr($substring, $offset);
                        // echo $customString;die;
                        break 2;// 跳出foreach
                    default:
                        $parsedData[$value[0]] = hexdec($littleString);
                        break;
                }
            } else {
                $parsedData[$value[0]] = substr($substring, $offset, $value[1] * 2);
            }
            $offset += $value[1] * 2;
        }
        unset($offset);
        if (isset($typeFlagStructures)) {
            for ($j = 1;$j <= $infoOjbNum;$j++) {
                $offset = 0;
                foreach ($typeFlagStructures as $name => $structure) {
                    $littleString = substr($customString, $offset, $structure * 2);

                    switch ($name) {
                        case 'system_type_flag':
                        case 'unit_type_flag':
                        case 'bfpf_system_state':
                            $constantName           = strtoupper($name);
                            $reflectionClass        = new ReflectionClass(self::class);
                            $constantValue          = $reflectionClass->getConstant($constantName);
                            $parsedData[$name . $j] = $constantValue[hexdec($littleString)];
                            break;
                        case 'bfpf_unit_state':
                        case 'uit_running_state':
                        case 'operate_info':
                            $constantName    = strtoupper($name);
                            $reflectionClass = new ReflectionClass(self::class);
                            $constantValue   = $reflectionClass->getConstant($constantName);
                            $unitStates      = strrev(base_convert($this->strReverse($littleString), 16, 2));
                            // 16进制转二进制，记录状态
                            for ($i = 0; $i < strlen($unitStates); $i++) {
                                if ($unitStates[$i] == 1) {
                                    $parsedData[$name . $j][] = $constantValue[$i][1];
                                }
                            }
                            break;
                        case 'unit_address':
                            $parsedData[$name . $j] = $this->strReverse($littleString);
                            break;
                        case 'time' :
                            $parsedData[$name . $j] = $this->strToTime($littleString);
                            break;
                        case 'analog_type':
                            $keyName                = 'analog' . $j;
                            ${$keyName}             = self::ANALOG_TYPE[hexdec($littleString)];
                            $parsedData[$name . $j] = ${$keyName}['name'] ?? '';
                            break;
                        case 'analog_value':
                            // todo test
                            $parsedData[$name . $j]             = hexdec($littleString);
                            $parsedData['analog_unit' . $j]     = ${$keyName}['unit'] ?? '';
                            $parsedData['analog_min_rage' . $j] = ${$keyName}['min_rage'] ?? '';
                            $parsedData['analog_radius' . $j]   = ${$keyName}['radius'] ?? '';
                            break;
                        default:
                            $parsedData[$name . $j] = $littleString;
                            break;
                    }

                    $offset += $structure * 2;
                }
            }
        }
        return $parsedData;
    }

    /**
     * @param string $string
     * @return string
     */
    private function strReverse(string $string)
    {
        // 将字符串拆分成每两个字符一组的数组
        $chunks = str_split($string, 2);

        // 反转数组中的元素
        $reversedChunks = array_reverse($chunks);

        // 拼接反转后的数组元素为一个新的字符串
        $newStr = implode('', $reversedChunks);

        // 将新的字符串每两个字符一组，用空格分隔
        return implode('', str_split($newStr, 2));
    }

    /**
     * 时间转换
     * @param string $string
     * @return false|string
     */
    private function strToTime(string $string)
    {
        $array = str_split($string, 2);

        $second = '00';
        $minute = '00';
        $hour   = '00';
        $day    = '00';
        $month  = '00';
        $year   = '00';
        foreach ($array as $key => $value) {
            switch ($key) {
                case 0:
                    $second = hexdec($value);
                    break;
                case 1:
                    $minute = hexdec($value);
                    break;
                case 2:
                    $hour = hexdec($value);
                    break;
                case 3:
                    $day = hexdec($value);
                    break;
                case 4:
                    $month = hexdec($value);
                    break;
                case 5:
                    $year = '20' . hexdec($value);
                    break;
            }
        }
        return date("Y/m/d H:i:s", mktime($hour, $minute, $second, $month, $day, $year));
    }

    /**
     * 和校验
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
