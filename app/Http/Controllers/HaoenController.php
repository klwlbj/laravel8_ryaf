<?php

namespace App\Http\Controllers;

use App\Utils\CTWing;

class HaoenController extends BaseController
{
    public function createCmdCommand($productId, $deviceId, $masterKey, $cmdType)
    {
        $client = new CTWing();
        return $client->createCmdCommand($productId, $deviceId, $masterKey, $cmdType);
    }
}
