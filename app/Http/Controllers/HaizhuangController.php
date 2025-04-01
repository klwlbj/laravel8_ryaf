<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Server\HaizhuangServer;

class HaizhuangController extends BaseController
{
    public function pushAlarm(int $ionoId)
    {
        return (new HaizhuangServer())->pushAlarm([], $ionoId);
    }
}
