<?php

namespace App\Http\Controllers;

use App\Utils\DaHua;

class DaHuaController extends BaseController
{
    public function analyze(string $string)
    {
        $util = new DaHua();
        return $util->parseString(strtolower(trim(urldecode($string))));
    }

    public function analyze2(string $string)
    {
        $spaced_string = chunk_split($string, 2, ' ');
        echo $spaced_string;
    }

    public function analyze3($string)
    {
        $util = new DaHua();

        // 处理请求
        echo chunk_split($util->createCmd($string), 2, ' ');
    }
}
