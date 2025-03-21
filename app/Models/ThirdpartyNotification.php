<?php

namespace App\Models;

class ThirdpartyNotification extends BaseModel
{
    protected $table   = 'thirdparty_notification';
    public $timestamps = null;

    public $primaryKey = 'thno_id';

    // 不允许批量赋值的字段
    protected $guarded = [];
}
