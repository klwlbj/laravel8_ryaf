<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Server\HaizhuangServer;

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
        parent::__construct();
        $this->client = new HaizhuangServer();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $imeis = $this->client->pushDevice(0);
        $imeis = [868550067222294, 868550060332868, 868550067110614, 868550067099973, 865257066231119, 868550067139738, 865118074613658, 868550067054143, 868550067518923, 868550060067035, 868550068172746, 865118074298781, 865118074631452, 868550068714331, 868550060239766, 868550060339640, 860586060332084, 868550067235551, 868550060344343, 868550060031999, 868550068705438, 868550060071714, 868550068699383, 868550060073843, 868550067822028];

        $dataPack = [
            [
                "loopType"   => "V602",
                "currentVal" => "2800",
                "limitHigh"  => 6000,
                "limitLow"   => 0,
            ],
            [
                "loopType"   => "V608",
                "currentVal" => "0",
                "limitHigh"  => 40,
                "limitLow"   => 0,
            ],
            [
                "loopType"   => "V607",
                "currentVal" => "100",
                "limitHigh"  => 100,
                "limitLow"   => 20,
            ],
        ];
        $this->client->pushFakeAlarm($imeis, $dataPack);
    }
}
