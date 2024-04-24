<?php

namespace App\Utils;

class LiuRui
{
    public const HEAD        = 'head';
    public const DEVICE_TYPE = 'device_type';
    public const INDEX       = 'index';
    public const CMD         = 'cmd';
    public const DATA_LENGTH = 'data_length';
    public const DATA        = 'data';
    // CONST check_sum = 'check_sum';

    public const SUBSTR_ARRAY = [
        // ['字段名','字段长度','字段是否需要转10进制']
        [self::HEAD, 2, false],
        [self::DEVICE_TYPE, 2, false],
        [self::INDEX, 2, true],
        [self::CMD, 2, true],
        [self::DATA_LENGTH, 2, true],
        // 应用数据 不定长
        [self::DATA, 0, true],
        // ['check_sum', 2, true],
    ];

    public const DEVICE_TYPE_ARRAY = [
        '01' => '门锁',
        '02' => '烟感',
    ];

    public const CMD_ARRAY = [
        // length 为0时，长度不固定，为-1时长度待定，为-2时无长度
        // 上行
        'a0' => [self::CMD => 'CMD_ACK', 'length' => 0, 'name' => '应答'],
        '50' => [self::CMD => 'CMD_SOFTVER', 'length' => 3, 'name' => '软件版本',
            'data_config'  => [
                'major_version'     => [
                    'is_binary' => false,
                    'name'      => '主版本号',
                ],

                'secondary_version' => [
                    'is_binary' => false,
                    'name'      => '次版本号',
                ],
                'beta_version'      => [
                    'is_binary' => false,
                    'name'      => 'beta版本号',
                ],
            ],
        ],
        '51' => [self::CMD => 'CMD_DEV_TYPE', 'length' => -1, 'name' => '设备编号',
            'data_config'  => [
                'model_number' => [
                    'is_binary' => false,
                    'name'      => '型号生产识别码',
                ],
            ],
        ],
        '52' => [self::CMD => 'CMD_PID', 'length' => -1, 'name' => '厂家ID',
            'data_config'  => [
                'manufacturer' => [
                    'is_binary' => false,
                    'name'      => '厂家ID',
                ],
            ],
        ],
        '60' => [self::CMD => 'CMD_MODELVER', 'length' => 0, 'name' => '模块版本',
            'data_config'  => [
                'module_version' => [
                    'is_binary' => false,
                    'name'      => '模块版本',
                ],
            ],
        ],
        '62' => [self::CMD => 'CMD_SIM', 'length' => 0, 'name' => 'SIM类型',
            'data_config'  => [
                'card_type' => [
                    'is_binary'     => true,
                    'name'          => '卡类型',
                    'default_value' => 'USIM', // 当值为0时
                    'config'        => [
                        '贴片SIM', 'ESIM', '软SIM',
                    ],
                ],
                'imsi'      => [
                    'is_binary' => false,
                    'name'      => 'IMSI',
                    'to_last'   => true,
                ],
            ],
        ],
        '63' => [self::CMD => 'CMD_IMEI', 'length' => 0, 'name' => 'IMEI',
            'data_config'  => [
                'imsi' => [
                    'is_binary' => false,
                    'name'      => 'IMSI',
                    'to_last'   => true,
                ],
            ],
        ],
        '65' => [self::CMD => 'CMD_ICCID', 'length' => 0, 'name' => 'ICCID',
            'data_config'  => [
                'iccid' => [
                    'is_binary' => false,
                    'name'      => 'ICCID',
                    'to_last'   => true,
                ],
            ]],
        '70' => [self::CMD => 'CMD_COMMUNICATION', 'length' => 1, 'name' => '通信故障',
            'data_config'  => [
                'communicate_falut' => [
                    'is_binary'     => true,
                    'name'          => '通信故障',
                    'default_value' => '通信故障', // 当值为00时
                    'config'        => [],
                ],
            ]],
        '00' => [self::CMD => 'CMD_BEAT', 'length' => 5, 'name' => '心跳',
            'data_config'  => [
                'fault_and_alarm'     => [
                    'is_binary'     => true,
                    'name'          => '故障火警',
                    'default_value' => '正常',
                    'config'        => ['温感火警', '温度传感器故障', '', '', '烟雾火警', '传感器故障'],
                ],
                'status'              => [
                    'is_binary'     => true,
                    'name'          => '状态',
                    'default_value' => '正常',
                    'config'        => [
                        '预留', '拆除状态', '消音', '低压'],
                ],
                'heartbeat_interval'  => [
                    'is_binary' => false,
                    'name'      => '心跳间隔',
                ],

                'contaminate'         => [
                    'is_binary' => false,
                    'name'      => '污染程度',
                ],
                'smoke_concentration' => [
                    'is_binary' => false,
                    'name'      => '烟雾浓度',
                ],
            ],
        ],
        '0e' => [self::CMD => 'CMD_POWER_ON', 'length' => -2, 'name' => '开机'],
        '0d' => [self::CMD => 'CMD_SELF_CHECK', 'length' => -2, 'name' => '自检'],
        '01' => [self::CMD => 'CMD_FIRE', 'length' => 2, 'name' => '火警',
            'data_config'  => [
                'smoke_concentration' => [
                    'is_binary' => false,
                    'name'      => '烟雾滤波值',
                ],
            ],
        ],
        '02' => [self::CMD => 'CMD_SENSOR_ERR', 'length' => 1, 'name' => '传感器故障',
            'data_config'  => [
                'smoke_concentration' => [
                    'is_binary' => false,
                    'name'      => '烟雾滤波值',
                ],
            ],
        ],
        '03' => [self::CMD => 'CMD_LOWVOLTAGE_ERR', 'length' => 1, 'name' => '低压故障',
            'data_config'  => [
                'battery_value' => [
                    'is_binary' => false,
                    'name'      => '电池采样值',
                ],
            ],
        ],
        '04' => [self::CMD => 'CMD_FIRE_RM', 'length' => 2, 'name' => '火警解除',
            'data_config'  => [
                'smoke_concentration' => [
                    'is_binary' => false,
                    'name'      => '烟雾滤波值',
                ],
            ],
        ],
        '05' => [self::CMD => 'CMD_SENSOR_RM', 'length' => 1, 'name' => '传感器故障解除',
            'data_config'  => [
                'smoke_concentration' => [
                    'is_binary' => false,
                    'name'      => '烟雾滤波值',
                ],
            ],
        ],
        '06' => [self::CMD => 'CMD_LOWVOLTAGE_RM', 'length' => 1, 'name' => '低压故障解除',
            'data_config'  => [
                'battery_value' => [
                    'is_binary' => false,
                    'name'      => '电池采样值',
                ],
            ],
        ],
        '09' => [self::CMD => 'CMD_FIX_ON', 'length' => -2, 'name' => '烟感装上'],
        '0a' => [self::CMD => 'CMD_PULL_DOWN', 'length' => -2, 'name' => '烟感拆除'],

        // 下行 无用 todo
        // 'a0' => [self::CMD => 'CMD_ACK', 'length' => -2],
        // '07' => [self::CMD => 'CMD_VOICE_MUTE', 'length' => 1],
    ];

    /**
     * 解密
     * @param string $string
     * @return array|false|string[]
     */
    public function toDecrypt(string $string)
    {
        $structure = [
            'encrypted_text' => '', // 密文
            'status'         => '', // 状态
            'timestamp'      => '', // 时间戳
            'battery'        => '', // 电量
            'rsrp'           => '', // 信号强度
            'snr'            => '', // 信噪比
            'ecl'            => '', // 信号覆盖等级
            'cell_id'        => '', // 小区位置信息
            'rssi'           => '', // 小区信号质量等级
            'nc_earfcn'      => '', // 频点信息
            'reserved_info'  => '', // 预留信息
        ];
        $subArray = explode(',', $string);
        // 遍历 $arr，将每个值依次赋给 $structure 中的对应键
        foreach ($structure as $key => $value) {
            if (!empty($subArray)) {
                $structure[$key] = array_shift($subArray);
            } else {
                break; // 如果 $arr 已经遍历完，则跳出循环
            }
        }

        $structure['head']        = substr($structure['encrypted_text'], 0, 2);// 头
        $structure['device_type'] = hexdec(substr($structure['encrypted_text'], 2, 2));// 设备类型 02=>烟感，暂时都是烟感
        $index                    = hexdec(substr($structure['encrypted_text'], 4, 2));  // 序号，转10进制

        $hexCipherText = substr($structure['encrypted_text'], 6);

        $decryptedText = "";

        // 对每两个字符进行异或运算
        for ($i = 0; $i < strlen($hexCipherText); $i += 2) {
            $char = hexdec(substr($hexCipherText, $i, 2)) ^ $index;

            $littleString = str_pad(dechex($char), 2, '0', STR_PAD_LEFT);
            if ($index % 2 == 0) {
                // 高低位交换
                $char = substr($littleString, 1) . substr($littleString, 0, 1);
            } else {
                $char = $littleString;
            }
            // echo($char . "\n");
            $decryptedText .= $char;
        }
        $structure['decrypted_text'] = $substring = substr($structure['encrypted_text'], 0, 6) . $decryptedText;// 明文

        if (substr($decryptedText, -2, 2) != $this->checkSum(substr($substring, 0, -2))) {
            return false;
        }

        $offset    = 0;
        $sliceData = [];
        $data      = $cmdConfigs = [];
        foreach (self::SUBSTR_ARRAY as $value) {
            $sliceString = substr($substring, $offset, $value[1]);
            switch ($value[0]) {
                case self::DEVICE_TYPE:
                    $sliceData[$value[0]] = self::DEVICE_TYPE_ARRAY[$sliceString];
                    break;
                case self::INDEX:
                    $sliceData[$value[0]] = $index;
                    break;
                case self::CMD:
                    $sliceData[$value[0]] = $sliceString;
                    if (!isset(self::CMD_ARRAY[$sliceString])) {
                        break;
                    }
                    $sliceData['cmd_type'] = self::CMD_ARRAY[$sliceString][self::CMD] ?? '';
                    $cmdConfigs            = self::CMD_ARRAY[$sliceString]['data_config'] ?? [];
                    if (!in_array(self::CMD_ARRAY[$sliceString]['length'], [0, -1, -2])) {
                        $dataLen = self::CMD_ARRAY[$sliceString]['length'] ?? 0; // 这个长度貌似用处不大,以实际DATA_LENGTH为准
                    }
                    break;
                case self::DATA_LENGTH:
                    $sliceData[$value[0]] = hexdec($sliceString);
                    $dataLen              = $sliceData[$value[0]];
                    break;
                case self::DATA:
                    $value[1]    = $dataLen;
                    $sliceString = substr($substring, $offset, $value[1] * 2);
                    $string      = $this->longHexToBin($sliceString);
                    $byte        = str_split($string, 8);
                    // $sliceData[$value[0]] = $string;

                    foreach ($cmdConfigs as $key => $dataConfig) {
                        static $byteNum = 0;
                        // dd($cmdConfigs);
                        if ($dataConfig['is_binary']) {
                            $list = $dataConfig['config'];
                            // 将二进制数转换为数组，方便逐位检查
                            $binaryArray = str_split($byte[$byteNum]);
                            foreach ($binaryArray as $index => $bit) {
                                // 检查二进制位是否为1，如果是1则将对应的元素添加到 $selectedItems 数组中
                                if ($bit === '1' && isset($list[$index])) {
                                    $data[$dataConfig['name']][] = $list[$index];
                                }
                            }
                            if (empty($data[$dataConfig['name']])) {
                                $data[$dataConfig['name']][] = $dataConfig['default_value'] ?? '';
                            }
                        } else {
                            if (isset($dataConfig['to_last']) && $dataConfig['to_last']) {
                                $data[$key] = [
                                    // 剩余字段，转成10进制
                                    'value' => $this->longBinToDec(implode('', array_slice($byte, $byteNum))),
                                    'name'  => $dataConfig['name'] ?? '',
                                ];
                            } else {
                                // 二进制转10进制
                                $data[$key] = [
                                    'value' => bindec($byte[$byteNum]),
                                    'name'  => $dataConfig['name'] ?? '',
                                ];
                            }
                        }
                        $byteNum++;
                    }
                    // dd($data);
                    break;
                default:
                    break;
            }
            $offset += $value[1];
        }
        $structure['data'] = $data;
        return array_merge($structure, $sliceData);
    }

    /**
     * 校验和(小写)^ff
     * @param string $string
     * @return string
     */
    private function checkSum(string $string): string
    {
        // 将字符串按两个字符分割成数组元素
        $hexArray = str_split($string, 2);

        // 将每个数组元素从十六进制转换为十进制
        $decArray = array_map('hexdec', $hexArray);

        // 对数组中的所有元素求和
        $sum = array_sum($decArray);

        // 取和的低8位（和对256取模）
        $checksum = (int) ($sum % 256) ^ (int) base_convert('ff', 16, 10);

        return strtolower(str_pad(dechex($checksum), 2, '0', STR_PAD_LEFT));
    }

    /**
     * 很长一段16进制转2进制
     * @param string $hexString
     * @return string
     */
    private function longHexToBin(string $hexString): string
    {
        $strArr   = str_split($hexString, 1);
        $binArray = array_map(function ($hexString) {
            return str_pad(base_convert($hexString, 16, 2), 4, '0', STR_PAD_LEFT);
        }, $strArr);
        return implode('', $binArray);
    }

    /**
     * todo 待验证
     * 很长一段2进制转10进制
     * @param string $hexString
     * @return string
     */
    private function longBinToDec(string $hexString): string
    {
        $decimalNumber = '0';

        $length = strlen($hexString);

        for ($i = 0; $i < $length; $i++) {
            $bitValue = $hexString[$length - $i - 1];
            $decimalNumber = bcadd($decimalNumber, bcmul($bitValue, bcpow('2', $i)));
        }
        return $decimalNumber;
    }
}
