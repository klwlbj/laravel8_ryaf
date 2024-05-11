<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

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

}
