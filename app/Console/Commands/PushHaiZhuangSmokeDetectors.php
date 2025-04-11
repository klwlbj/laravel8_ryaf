<?php

namespace App\Console\Commands;

use App\Http\Server\HaizhuangServer;
use App\Models\BaseModel;
use App\Models\SmokeDetector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PushHaiZhuangSmokeDetectors extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-HZ-smoke-detectors:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    protected $client;

    public function __construct()
    {
        // $this->token = $this->getToken();
        $this->client = new HaizhuangServer();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $imeis = $this->client->pushDevice(0);
        // $this->client->pushAlarm($imeis);
    }
}
