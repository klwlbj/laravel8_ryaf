<?php

namespace App\Models\Platform;


use Illuminate\Database\Eloquent\SoftDeletes;

class NotifyUrl extends BaseModel
{
    # 使用软删除
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'notify_url';
    public $timestamps = null;
}
