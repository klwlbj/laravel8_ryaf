<?php

namespace App\Console\Commands;

use App\Models\Place;
use App\Models\SmokeDetector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Server\ChangpingServer;

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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->pushPlace();
        $imeis = $this->pushDevice();
        // $this->pushAlarm($imeis);
        $this->line('推送完成');
    }

    public function pushPlace(): void
    {
        $method = 'api.v2.unit.addResidence';
        // 查找出昌平节点下的所有place
        $places = Place::on('mysql2')
            ->where('plac_node_id', '462')
            ->where('plac_user_id', '<>', 0)
            // ->where('plac_id', 351784)
            ->leftJoin('user', 'user.user_id', '=', 'place.plac_user_id')
            ->leftJoin('order', 'order.order_id', '=', 'place.plac_order_id')
            ->get();

        $list = [];

        foreach ($places as $item) {
            $userName   = !empty($item->user_name) ? $item->user_name : $item->order_user_name;
            $userMobile = !empty($item->user_mobile) ? $item->user_mobile : $item->order_user_mobile;
            $list[]     = [
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
        $this->line(json_encode($list, JSON_UNESCAPED_UNICODE));

        $res = json_decode((new ChangpingServer())->sendRequest($method, $data), true);

        // dd($res);
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
            foreach ($flattened as $key =>  $value) {
                $insertData[] = [
                    'cp_key'   => (int) $key,
                    'cp_value' => (int) $value,
                ];
            }
            // 清空表数据
            DB::connection('mysql2')->table('changping_key_value')->truncate();
            DB::connection('mysql2')->table('changping_key_value')->insert($insertData);
        } else {
            $this->error(json_encode($res, JSON_UNESCAPED_UNICODE));
        }
    }

    public function pushDevice()
    {
        $method = 'api.v2.unit.addUserMonitorInfo';
        // 查找出昌平节点下的所有烟感
        $devices = SmokeDetector::on('mysql2')
            ->where('smde_node_ids', 'like', '%' . '462' . '%')
            ->where('smde_user_id', '<>', 0)
            ->leftJoin('place', 'place.plac_id', '=', 'smoke_detector.smde_place_id')
            ->get();

        $imeis = $devices->pluck('smde_imei')->toArray();

        $list = [];

        foreach ($devices as $item) {
            $companyCode = DB::connection('mysql2')->table('changping_key_value')->where('cp_key', $item->smde_place_id)->value('cp_value');
            if (!empty($companyCode)) {
                $list[] = [
                    "companyCode"    => $companyCode,
                    'monitorName'    => $item->smde_type,
                    'monitorCode'    => $item->smde_imei,
                    'monitorType'    => self::MONITOR_TYPES[$item->smde_model_name] ?? '',
                    'installDate'    => $item->smde_deliver_time,
                    'installAddress' => $item->plac_address,
                    'networkDate'    => $item->smde_deliver_time,
                    'runState'       => 0,
                    'monitorState'   => 0,
                    'dataId'         => $item->smde_id,
                ];
            }
        }
        $data = [
            "validates" => $list,
        ];
        $this->line('推送设备中：'.json_encode($list, JSON_UNESCAPED_UNICODE));

        $this->line((new ChangpingServer())->sendRequest($method, $data));

        return $imeis;
    }

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
