<?php

namespace App\Models;

class DeviceLastestData extends BaseModel
{
    protected $table   = 'device_lastest_data';
    public $timestamps = null;

    public $primaryKey = 'dld_id';

    // 不允许批量赋值的字段
    protected $guarded = [];
}
