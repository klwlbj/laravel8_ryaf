<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\Exceptions\ClientNotConnectedToBrokerException;

class HappyBirdMqttClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'happy-bird-mqtt-client:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts a happy bird MQTT client';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('welcome to happy bird MQTT client!');
        try {
            $mqtt = MQTT::connection();
            $date = now();
            $mqtt->subscribe('lnsendout/ruyue_mqtt/+/+/alarm/#', function (string $topic, string $message) use ($date) {
                Log::info("Received message:{$date} {$message} on topic {$topic}");
                // 对data进行处理
            }, 1);

            $mqtt->subscribe('lnsendout/ruyue_mqtt/+/+/rtdata/#', function (string $topic, string $message) use ($date) {
                Log::info("Received message:{$date} {$message} on topic {$topic}");
                // 对data进行处理
            }, 1);
            $mqtt->loop(true);// 不退出
        } catch (ClientNotConnectedToBrokerException $e) {
            $this->error('Failed to establish connection to MQTT server,error message:' . $e);
        }
    }
}
