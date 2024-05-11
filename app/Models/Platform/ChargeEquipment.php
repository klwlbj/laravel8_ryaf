<?php

namespace App\Models\Platform;

class ChargeEquipment extends BaseModel
{
    public const EQUIPMENT_TYPE_ALTERNATING = 1;
    public const EQUIPMENT_TYPE_DIRECT      = 2;
    public const EQUIPMENT_TYPE_INTEGRATION = 3;

    public static array $formatEquipmentTypeMaps = [
        self::EQUIPMENT_TYPE_ALTERNATING => '交流',
        self::EQUIPMENT_TYPE_DIRECT      => '直流设备 ',
        self::EQUIPMENT_TYPE_INTEGRATION => '交直流一体设备',
    ];

    public const EQUIPMENT_CATEGORY_PILE        = 1;
    public const EQUIPMENT_CATEGORY_ARK         = 2;
    public const EQUIPMENT_CATEGORY_INTERCHANGE = 3;

    public static array $formatEquipmentCategoryMaps = [
        self::EQUIPMENT_CATEGORY_PILE        => '充电桩',
        self::EQUIPMENT_CATEGORY_ARK         => '充电柜',
        self::EQUIPMENT_CATEGORY_INTERCHANGE => '换电柜',
    ];
}
