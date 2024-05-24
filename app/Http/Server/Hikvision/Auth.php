<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;

class Auth extends BaseServer
{
    public $ak = '22689412';
    public $sk = 'cs81nNQGSqGeubA7sCL3';

    public function getSign()
    {
        $milliseconds = microtime(true) * 1000;

        $str = base64_encode($this->ak . $milliseconds);

        $digestStr = hash_hmac('sha256', $str, $this->sk);

        return base64_encode($digestStr . ':' . $this->ak . ':' . $milliseconds);
    }
}
