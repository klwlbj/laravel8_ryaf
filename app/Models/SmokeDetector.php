<?php

namespace App\Models;

class SmokeDetector extends BaseModel
{
    protected $table   = 'smoke_detector';
    public $timestamps = null;

    public $primaryKey = 'smde_id';

    // 不允许批量赋值的字段
    protected $guarded = [];
}
