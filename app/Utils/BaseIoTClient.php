<?php

namespace App\Utils;

class BaseIoTClient
{
    public const COMMAND = [
        'longSilence'  => ["9000000192000c00", "0000ffff000C00023D"], // 长消音
        'shortSilence' => ["9000000192000C00", "0000FFFF00002E"], // 短消音
        'reset'        => ["9000040192000C00", "0000FFFF000537"], // 复位
        'mask'         => ["9000050192000c00", "0000ffff00083a"], // 屏蔽
        'unmask'       => ["9000040192000c00", "0000ffff00093a"], // 解除屏蔽
        'faultSilence' => ["9000000192000c00", "0000ffff000B00073F"], // 欠压故障消音
    ];
}
