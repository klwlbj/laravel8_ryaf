<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WanLinYun
{
    public const HOST = 'http://api.wanlinyun.com/index.php';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function remoteControl($chipcode, $clientId, $runTime, $switchState)
    {
        $json = [
            "token"     => ENV('WLY_TOKEN'),
            "user_name" => ENV('WLY_USERNAME'),
            "setdata"   => [
                "chipcode"     => $chipcode,
                "client_id"    => $clientId,
                "run_time"     => $runTime,
                "switch_state" => $switchState,
                "loop"         => "01",
            ],
        ];
        $timestamp = now();

        Log::info("send JSON: " . json_encode($json) . " | Timestamp: {$timestamp}");
        $response = $this->client->request('POST', self::HOST . '?act=Api&method=three_power_set', [
            'json' => $json,
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }
        return false;
    }
}
