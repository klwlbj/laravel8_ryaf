<?php

namespace App\Console\Commands;

use App\Models\SmokeDetector;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SmokeDetectorBatchUpdateCardStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smoke-detector-batch-update-card-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量更新烟感nb卡状态';

    protected string $token  = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            SmokeDetector::query()
                ->whereNotNull('smde_nb_iid2')
                ->where('smde_nb_iid2', "!=", '')
                ->whereNull('smde_card_expiration')
                ->whereNotIn('smde_id', [33817,30016])
                ->get()
                ->each(function($item){
                    // 处理
                    $this->sendRequest($item);
                    $item->save();
                });

        } catch (\Exception $exception) {
            var_dump($exception);
        }
    }

    public function sendRequest($item)
    {
        if(empty($this->token)){

        }
        $response = $this->client->post('https://api.iot.10086.cn/v5/ec/query/sim-status', [
            'json' => [
                "transid"       => '',
                "iccid"   => '',
                "token"        => $this->token,
            ],
        ]);
        $platformReturn = json_decode($response->getBody(), true);
        // Log::info("卡商平台返回:" . date('H:i:s') . ':' . json_encode($platformReturn));

        /*if (isset($platformReturn['code']) && $platformReturn['code'] === 0) {
            if(empty($platformReturn['data'])){
                Log::info("卡商平台返回错误:" . date('H:i:s') . ':' . json_encode($smokeDetectorIccids));
            }
            return array_column($platformReturn['data'], 'cardEndTime', 'iccid');
        }*/
        sleep(1);
        return $platformReturn;
    }
}
