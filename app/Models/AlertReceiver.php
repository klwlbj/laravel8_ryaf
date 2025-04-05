<?php

namespace App\Models;

class AlertReceiver extends BaseModel
{
    protected $table   = 'alert_receiver';
    public $timestamps = null;

    public $primaryKey = 'alre_id';

    // 不允许批量赋值的字段
    protected $guarded = [];
}
