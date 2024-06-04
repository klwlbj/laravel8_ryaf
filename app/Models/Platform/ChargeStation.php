<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\SoftDeletes;

class ChargeStation extends BaseModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public const TYPE_TAKE_OUT       = 1;
    public const TYPE_EXPRESS        = 2;
    public const TYPE_OTHER_SPECIFIC = 3;
    public const TYPE_PLOT_PUBLIC    = 4;
    public const TYPE_OTHER_PUBLIC   = 5;
    public const TYPE_OTHER          = 6;

    public static array $formatTypeMaps = [
        self::TYPE_TAKE_OUT       => '外卖专用',
        self::TYPE_EXPRESS        => '快递专用',
        self::TYPE_OTHER_SPECIFIC => '其他专用',
        self::TYPE_PLOT_PUBLIC    => '小区公共',
        self::TYPE_OTHER_PUBLIC   => '其他公共',
        self::TYPE_OTHER          => '其他',
    ];

    public const STATUS_UNKNOWN     = 0;
    public const STATUS_BUILDED     = 1;
    public const STATUS_CLOSED      = 5;
    public const STATUS_MAINTENANCE = 60;
    public const STATUS_NORMAL      = 50;

    public static array $formatStatusMaps = [
        self::STATUS_UNKNOWN     => '未知',
        self::STATUS_BUILDED     => '建设中',
        self::STATUS_CLOSED      => '关闭下线',
        self::STATUS_MAINTENANCE => '维护中',
        self::STATUS_NORMAL      => '正常使用',
    ];

    public const FEE_TYPE_HOUR           = 1;
    public const FEE_TYPE_POWER          = 2;
    public const FEE_TYPE_SERVICE_CHARGE = 3;
    public const FEE_TYPE_QUANTITY       = 4;
    public const FEE_TYPE_OTHER          = 5;

    public static array $formatFeeTypeMaps = [
        self::FEE_TYPE_HOUR           => '按时计费',
        self::FEE_TYPE_POWER          => '分功率按时计费',
        self::FEE_TYPE_SERVICE_CHARGE => '电费另计，服务费按时计费',
        self::FEE_TYPE_QUANTITY       => '按电度计费',
        self::FEE_TYPE_OTHER          => '其他',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function($table) {
            $table->ChargeEquipment->each(function($chargeEquipment) {
                $chargeEquipment->ChargeCell->each->delete(); // 删除表2关联的表3数据
                $chargeEquipment->delete(); // 删除表2数据
            });
        });
    }

    public function ChargeEquipment()
    {
        return $this->hasMany(ChargeEquipment::class, 'station_id', 'station_id');
    }
}
