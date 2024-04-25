<?php

namespace App\Http\Controllers;

use App\Utils\LiuRui;
use Illuminate\Http\Request;

class LiuRuiController
{
    public function toDecrypt(Request $request, string $string)
    {
        $util = new LiuRui();
        $data = $util->toDecrypt($string);
        return response()->json($data);
    }

    public function muffling($productId, $deviceId, $masterKey){
        return (new LiuRui())->muffling($productId, $deviceId, $masterKey);
    }
}
