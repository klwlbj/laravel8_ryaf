<?php

namespace App\Models\Platform;

use Illuminate\Support\Str;

class ChargeStation extends BaseModel
{
    // public static $snakeAttributes = true;

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

    public const FIRE_TYPE_HOUR           = 1;
    public const FIRE_TYPE_POWER          = 2;
    public const FIRE_TYPE_SERVICE_CHARGE = 3;
    public const FIRE_TYPE_QUANTITY       = 4;
    public const FIRE_TYPE_OTHER          = 5;

    public static array $formatFireTypeMaps = [
        self::FIRE_TYPE_HOUR           => '按时计费',
        self::FIRE_TYPE_POWER          => '分功率按时计费',
        self::FIRE_TYPE_SERVICE_CHARGE => '电费另计，服务费按时计费',
        self::FIRE_TYPE_QUANTITY       => '按电度计费',
        self::FIRE_TYPE_OTHER          => '其他',
    ];

    // // 定义访问器：将数据库字段转为大写驼峰
    // public function getAttribute($key)
    // {
    //     // $value = parent::getAttribute($key);
    //
    //     // 转换数据库字段为大写驼峰形式
    //     return Str::studly($key);
    // }
    //
    // // 可选：将字段保存时转为下划线形式
    // public function setAttribute($key, $value)
    // {
    //     $key = Str::snake($key); // 转换为下划线形式
    //     return parent::setAttribute($key, $value);
    // }

    public function setAttribute($key, $value)
    {
        // 将驼峰形式的属性名转换为下划线形式
        $key = Str::snake($key);

        // 使用父类的 setAttribute 方法将值设置到属性
        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        // 自定义转换规则，将下划线形式的字段名转换为驼峰形式
        $key = Str::camel($key);

        return parent::getAttribute($key);
    }

    // get()  list $model->key


}
