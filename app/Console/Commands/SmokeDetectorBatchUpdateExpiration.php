<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\SmokeDetector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SmokeDetectorBatchUpdateExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smoke-detector-batch-update-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量更新烟感nb卡过期时间';

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
            $chunkSize = 100; // 指定每个分块的大小
            SmokeDetector::query()
                ->whereNotNull('smde_nb_iid2')
                ->where('smde_nb_iid2', "!=", '')
                ->whereNull('smde_card_expiration')
                ->whereNotIn('smde_id', [33817,30016])
                ->chunkById($chunkSize, function ($list) {
                    $smokeDetectorIccids = [];
                    foreach ($list as &$item) {
                        if (in_array($item->smde_iot_platform, ['AEPTOUCHUAN','CTWING_ADV', 'CTWING_AEP'])) {
                            $item->smde_nb_iid3 = substr($item->smde_nb_iid2, 0, strlen($item->smde_nb_iid2) - 1);
                        } else {
                            $item->smde_nb_iid3 = $item->smde_nb_iid2;
                        }
                        $smokeDetectorIccids[] = $item->smde_nb_iid3;
                    }
                    // dd($smokeDetectorIccids);
                    $data = $this->sendRequest($smokeDetectorIccids);

                    foreach ($list as $item) {
                        $item->smde_card_expiration = $data[$item->smde_nb_iid3] ?? null;
                        if(!isset($data[$item->smde_nb_iid3])){
                            print_r($item->smde_id . PHP_EOL);
                        }
                        unset($item->smde_nb_iid3);
                        $item->save();
                    }
                    sleep(3);
                });
        } catch (\Exception $exception) {
            var_dump($exception);
        }
    }

    public function sendRequest($smokeDetectorIccids = [])
    {
        $response = $this->client->post('https://api.wl1688.net/iotc/getway', [
            'json' => [
                "appid"       => (string) env('NB_CARD_PLATFORM_APPID'),
                "appsecret"   => (string) env('NB_CARD_PLATFORM_APPSECTRET'),
                "name"        => "api.v2.all.card.page",
                // "msisdn"      => "",
                "iccids"      => $smokeDetectorIccids,
                "currentPage" => 1,
                "pageSize"    => 100,
            ],
        ]);
        $platformReturn = json_decode($response->getBody(), true);
        Log::info("卡商平台返回:" . date('H:i:s') . ':' . json_encode($platformReturn));

        if (isset($platformReturn['code']) && $platformReturn['code'] === 0) {
            if(empty($platformReturn['data'])){
                Log::info("卡商平台返回错误:" . date('H:i:s') . ':' . json_encode($smokeDetectorIccids));
            }
            return array_column($platformReturn['data'], 'cardEndTime', 'iccid');
        }
        sleep(1);
        return $this->sendRequest($smokeDetectorIccids);
    }
}
