<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Exceptions\ClientNotConnectedToBrokerException;
use PhpParser\Node\Scalar\MagicConst\Dir;

class CopyClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-mqtt-client:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts a happy bird MQTT client';

    public function handle()
    {
        $this->line('welcome to happy bird MQTT client!');
        $app = require __DIR__.'/../../../bootstrap/app.php';

        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();
        try {
            for ($i=100;$i>0;$i--){
                // $log_message = "This is a log message.\n";
                // $log_file = "logs/heartbeat/".time().".log";
                // // chmod($log_file,777);
                // file_put_contents($log_file, $log_message);
                // sleep(5);

                $log_message = "This is a log message.\n";
                // $log_file = "logs/heartbeat/".time().".log";
                // Cache::set('key', time());
                Log::channel('my_custom_log')->info("Received all alarm message:");

                // chmod($log_file,777);
                // file_put_contents($log_file, $log_message);
                sleep(5);


            }
        } catch (ClientNotConnectedToBrokerException $e) {
            $this->error('Failed to establish connection to MQTT server,error message:' . $e);
        }
    }
}
