<?php

namespace App\Http\Controllers;

use App\Http\Server\HaizhuangServer;

class HaizhuangController extends BaseController
{
    public function pushAlarm(int $ionoId)
    {
        return (new HaizhuangServer())->pushAlarm($ionoId);
    }

    public function batchPushAlarm()
    {
        $client = new HaizhuangServer();

        return $client->pushAlarm();
    }

    public function batchPushHandledAlarm()
    {
        $client = new HaizhuangServer();

        return $client->pushHandledAlarm();
    }
}
