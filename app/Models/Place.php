<?php

namespace App\Models;

class Place extends BaseModel
{
    protected $table   = 'place';
    public $timestamps = null;

    public $primaryKey = 'plac_id';

    // 不允许批量赋值的字段
    protected $guarded = [];
}
