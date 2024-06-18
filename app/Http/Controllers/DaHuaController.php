<?php

namespace App\Http\Controllers;

use App\Utils\DaHua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    /**
     * 大华报警回调
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function dhCTWingWarm(Request $request)
    {
        $jsonData = $request->all();
        Log::info('dhctwingWarm:' . json_encode($jsonData));

        return response('', 200);
    }
}
