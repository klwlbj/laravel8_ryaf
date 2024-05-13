<?php

namespace App\Models\Platform;

class ChargeCell extends BaseModel
{
    public const CELL_TYPE_HOME        = 1;
    public const CELL_TYPE_DIRECT      = 2;
    public const CELL_TYPE_ALTERNATING = 3;
    public const CELL_TYPE_WIRELESS    = 4;
    public const CELL_TYPE_OTHER       = 5;

    public static array $formatEquipmentTypeMaps = [
        self::CELL_TYPE_HOME        => '家用插座',
        self::CELL_TYPE_DIRECT      => '直流直流接口插头',
        self::CELL_TYPE_ALTERNATING => '交流接口插头',
        self::CELL_TYPE_WIRELESS    => '无线充电座',
        self::CELL_TYPE_OTHER       => '其他',
    ];
}
