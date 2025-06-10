<?php

namespace App\Console\Commands;

use App\Models\Place;
use App\Models\SmokeDetector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Server\ChangpingServer;
use Illuminate\Support\Facades\Cache;

class PushChangPingSmokeDetectors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-CP-smoke-detectors:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $crtTime;

    public const MONITOR_TYPES = [
        'HM-618PH-4G' => 1901549230328725505,
        'YL-IOT-YW03' => 1901549715760693250,
        'HM-608PH-NB' => 1904368738677837825,
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->crtTime = Cache::get('changping_place_time', date("Y-m-d H:i:s"));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->pushPlace();
        $this->pushDevice();
        // $this->pushAlarm($imeis);
        $this->line('推送完成');
    }

    public function pushPlace(): void
    {
        $method = 'api.v2.unit.addResidence';
        $limit  = 1000;
        $offset = 0;
        // 清空表数据
        DB::connection('mysql2')->table('thirdparty_unique_key')
            ->where('tuk_thpl_id', 9)
            ->whereNotNull('tuk_plac_id')
            ->delete();

        // 数量

        $query = Place::on('mysql2')
            ->where('plac_node_id', '462')
            ->where('plac_user_id', '<>', 0)
            ->where('plac_name', '<>', '')
            ->where('plac_crt_time', '>=', $this->crtTime)
            // ->where('plac_id', 351784)
            ->leftJoin('user', 'user.user_id', '=', 'place.plac_user_id')
            ->leftJoin('order', 'order.order_id', '=', 'place.plac_order_id');
        $total = $query->count();
        if($total === 0) {
            $this->info('没有需要推送的昌平地址');
            return;
        }
        while ($offset < $total) {
            // 查找出昌平节点下的所有place
            $places = $query
                ->skip($offset)->take($limit)
                ->get();

            $list = [];

            foreach ($places as $item) {
                $userName   = !empty($item->user_name) ? $item->user_name : $item->order_user_name;
                $userMobile = !empty($item->user_mobile) ? $item->user_mobile : $item->order_user_mobile;
                if (empty($item->plac_name)) {
                    $this->error('地址不存在，plac_id：' . $item->plac_id);
                    continue;
                }
                $list[] = [
                    "companyName"   => $item->plac_name,
                    "address"       => $item->plac_address,
                    "areaCode"      => '110114111', // 区域编码全是“南邵镇”，暂时写死 todo
                    "lng"           => $item->plac_lng,
                    "lat"           => $item->plac_lat,
                    'corporator'    => $userName,
                    'corporatorTel' => $userMobile,
                    "dataId"        => $item->plac_id,
                ];
            }
            $data = [
                "validates" => $list,
            ];

            $res = json_decode((new ChangpingServer())->sendRequest($method, $data), true);

            if ($res['success']) {
                $keyValue = $res['data'];
                // dd($keyValue);
                $this->info('推送成功' . json_encode($keyValue, JSON_UNESCAPED_UNICODE));

                // 将数据转换为集合并合并所有项
                // 使用 merge 保持键名
                $flattened = collect($keyValue)->reduce(function ($carry, $item) {
                    return $carry + $item; // 保持键名不丢失
                }, []);

                $insertData = [];
                foreach ($flattened as $key => $value) {
                    $insertData[] = [
                        'tuk_plac_id'       => (int) $key,
                        'tuk_thirdparty_uk' => (int) $value,
                        'tuk_thpl_id'       => 9,
                    ];
                }

                DB::connection('mysql2')->table('thirdparty_unique_key')->insert($insertData);
                $this->info("昌平推送地址成功");
            } else {
                $this->info('昌平推送地址失败' . json_encode($data));

                $this->error(json_encode($res, JSON_UNESCAPED_UNICODE));
            }
            // 更新偏移量
            $offset += $limit;
        }
        Cache::put('changping_place_time', date("Y-m-d H:i:s"));
    }

    public function pushDevice()
    {
        $method = 'api.v2.unit.addUserMonitorInfo';
        $limit  = 100;
        $offset = 0;
        $query  = SmokeDetector::on('mysql2')
            ->where('smde_node_ids', 'like', '%' . '462' . '%')
            ->where('smde_user_id', '<>', 0)
            ->where('smde_deliver_time', '>=', $this->crtTime)
            ->leftJoin('place', 'place.plac_id', '=', 'smoke_detector.smde_place_id');
        $total = $query->count();
        if($total === 0) {
            $this->info('没有需要推送的昌平烟感');
            return;
        }
        while ($offset < $total) {
            // 查找出昌平节点下的所有烟感
            $devices = $query
                ->skip($offset)->take($limit)
                ->get();

            $list = [];

            foreach ($devices as $item) {
                $companyCode = DB::connection('mysql2')->table('thirdparty_unique_key')
                    ->where('tuk_plac_id', $item->smde_place_id)
                    ->value('tuk_thirdparty_uk');
                if (!empty($companyCode)) {
                    $list[] = [
                        "companyCode"    => $companyCode,
                        'monitorName'    => $item->smde_type,
                        'monitorCode'    => $item->smde_imei,
                        'monitorType'    => self::MONITOR_TYPES[$item->smde_model_name] ?? '',
                        'installDate'    => $item->smde_deliver_time,
                        'installAddress' => $item->plac_address,
                        'networkDate'    => $item->smde_deliver_time,
                        'runState'       => 0, // 运行状态（0正常 1故障 2报警）
                        'monitorState'   => 0, // 0在线 1离线
                        'dataId'         => $item->smde_id,
                    ];
                }
            }
            $data = [
                "validates" => $list,
            ];
            // $this->line('推送设备中：' . json_encode($list, JSON_UNESCAPED_UNICODE));

            $this->line((new ChangpingServer())->sendRequest($method, $data));
            // 更新偏移量
            $offset += $limit;
        }

        // return $imeis;
    }

    // 暂时不调用 todo
    public function pushAlarm($imeis = [])
    {
        $method = 'api.v2.unit.addFireAlarm';
        // 查出所有警报
        $alarms = DB::connection('mysql2')->table('iot_notification_alert')
            // ->where('fire_alarm_status', 0)
            ->whereIn('iono_imei', $imeis)
            ->limit(10)
            ->orderBy('iono_id', 'desc')
            ->get();

        foreach ($alarms as $alarm) {
            $data = [
                'monitorCode'     => $alarm->iono_imei,
                'nodeCode'        => $alarm->iono_imei,
                'happenTime'      => date('Y-m-d H:i:s', strtotime($alarm->iono_crt_time)),
                'alarmObjectType' => 2, // todo 报警类别（0网关  1探测点  2监测点）
                'alarmCode'       => '2-0-30',
                'alarmSummary'    => '1',
                'alarmSketch'     => '1',
                'dataId'          => $alarm->iono_id,
            ];
            $this->line((new ChangpingServer())->sendRequest($method, $data));
        }
    }
}
