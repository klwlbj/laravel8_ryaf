<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class Haiman
{
    public function mufflingByOneNet($json)
    {
        $ontNet = new OneNet();
        $res =  $ontNet->callService(json_decode($json));
        Log::info('消音返回:' . json_encode($res));
        return $res;
    }
}
