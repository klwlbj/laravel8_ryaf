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
}
