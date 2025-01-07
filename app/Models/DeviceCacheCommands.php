<?php

namespace App\Models;

class DeviceCacheCommands extends BaseModel
{
    protected $connection = 'mysql2';
    protected $table      = 'device_cache_commands';
    public $timestamps    = true;
}
