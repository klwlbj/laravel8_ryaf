<?php

namespace App\Http\Controllers;

use App\Utils\WanLinYun;
use Illuminate\Http\Request;
use App\Http\Server\WanLinYunClass;

class WanLinYunController extends BaseController
{
    public function heartbeat(Request $request)
    {
        $wanLinYun = new WanLinYunClass();
        $params    = $request->json()->all();
        $res       = $wanLinYun->heartbeat($params);
        return $this->response($res);
    }

    public function common(Request $request)
    {
        $wanLinYun = new WanLinYunClass();
        $params    = $request->json()->all();
        $res       = $wanLinYun->common($params);
        echo "Inspection pass";
        die;

        return $this->response($res);
    }

    public function event(Request $request)
    {
        $wanLinYun = new WanLinYunClass();
        $params    = $request->json()->all();
        $res       = $wanLinYun->event($params);
        return $this->response($res);
    }

    public function offline(Request $request)
    {
        $wanLinYun = new WanLinYunClass();
        $params    = $request->json()->all();
        $res       = $wanLinYun->offline($params);
        return $this->response($res);
    }

    public function iccid(Request $request)
    {
        $wanLinYun = new WanLinYunClass();
        $params    = $request->json()->all();
        $res       = $wanLinYun->iccid($params);
        return $this->response($res);
    }

    public function remoteControl($chipcode, $clientId, $runTime, $switchState)
    {
        $client = new WanLinYun();
        return $client->remoteControl($chipcode, $clientId, $runTime, $switchState);
    }
}
