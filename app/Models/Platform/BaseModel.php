<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel  extends Model
{
    public const YES     = 1;
    public const NO      = 2;
    public const UNKNOWN = 3;

    public static array $formatWhetherOrNotMaps = [
        self::YES     => '是',
        self::NO      => '否',
        self::UNKNOWN => '未知',
    ];

    public function setAttribute($key, $value)
    {
        // 将驼峰形式的属性名转换为下划线形式
        $key = Str::snake($key);

        // 使用父类的 setAttribute 方法将值设置到属性
        return parent::setAttribute($key, $value);
    }

}
