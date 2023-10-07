<?php

namespace App\Utils;

class BaseIoTClient
{
    public const LONG_SILENCE      = 0;
    public const SHORT_SILENCE     = 1;
    public const RESET             = 2;
    public const MASK              = 3;
    public const UNMASK            = 4;
    public const FAULT_SILENCE     = 5;
    public const MICROWAVE_SETTING = 6;
    public const GAS               = 7;
    public const GAS_LONG_SILENCE = 8;
    public const COMMAND           = [
        self::LONG_SILENCE      => ["9000000192000c00", "0000ffff000C00023D"], // 长消音
        self::GAS_LONG_SILENCE =>  ['900000019200000C', '0000FFFF0000000000'],
        self::SHORT_SILENCE     => ["9000000192000C00", "0000FFFF00002E"], // 短消音
        self::RESET             => ["9000040192000C00", "0000FFFF000537"], // 复位
        self::MASK              => ["9000050192000c00", "0000ffff00083a"], // 屏蔽
        self::UNMASK            => ["9000040192000c00", "0000ffff00093a"], // 解除屏蔽
        self::FAULT_SILENCE     => ["9000000192000c00", "0000ffff000B00073F"], // 欠压故障消音
        self::MICROWAVE_SETTING => ['9000000191000C000000000000000019FFFF000C', 'FF'], // 配置烟感fy310
        self::GAS               => ['9000000191000c00000000000001001AFFFF00040401'], // 燃气
                                   //9000000191000c00000000000001001AFFFF0004040100D222
                                   //9000000191000c00000000000001001AFFFF0004040100D222
    ];

    public function generateCommand($command = self::LONG_SILENCE, $dwPackageNo = '', $checkSum = false, $params = []): string
    {
        $args = self::COMMAND[$command] ?? self::COMMAND[self::LONG_SILENCE];
        switch ($command) {
            case self::GAS:
                extract($params);
                $gasAlarmCorrection = sprintf("%04X", $gasAlarmCorrection); //10进制转16进制
                $cmd                = $args[0] . $gasAlarmCorrection;
                $cmd .= $this->checkSum($cmd);
                break;
            case self::MICROWAVE_SETTING:
                extract($params);
                $byWorkMode               = sprintf("%02X", $byWorkMode);
                $wSensitivity             = sprintf("%02X", $wSensitivity);
                $byCommunicationFrequency = sprintf("%04X", $byCommunicationFrequency);
                $byEnabled                = sprintf("%02X", $byEnabled);
                $byCycle                  = sprintf("%04X", $byCycle);
                $byTimes                  = sprintf("%02X", $byTimes);
                $byWorkPeriod             = sprintf("%02X", $byWorkPeriod);
                $byStartTime              = sprintf("%02X", $byStartTime);
                $byEndTime                = sprintf("%02X", $byEndTime);
                $cmd                      = '0C' . $byWorkMode . $wSensitivity . $byCommunicationFrequency . $byEnabled . $byCycle . $byTimes . $byWorkPeriod . $byStartTime . $byEndTime; // 拼接各参数，0C是长度，暂时写死

                $cmd = $args[0] . $cmd . $args[1];
                break;
            default:
                if ($checkSum) {
                    $cmd = substr($args[0] . $dwPackageNo . $args[1], 0, -2); //剔除最后两位
                    $cmd .= $this->checkSum($cmd);
                } else {
                    $cmd = $args[0] . $dwPackageNo . $args[1]; //剔除最后两位
                }
                break;
        }

        return $cmd;
    }

    /**
     * 和校验
     * @param $string
     * @return string
     */
    protected function checkSum($string): string
    {
        // 将字符串按两个字符分割成数组元素
        $hexArray = str_split($string, 2);

        // 将每个数组元素从十六进制转换为十进制
        $decArray = array_map('hexdec', $hexArray);

        // 对数组中的所有元素求和
        $sum = array_sum($decArray);

        // 取和的低8位（和对256取模）
        $checksum = $sum % 256;

        return strtoupper(str_pad(dechex($checksum), 2, '0', STR_PAD_LEFT));
    }
}
