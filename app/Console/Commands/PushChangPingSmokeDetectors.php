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

    public const NODE_IDS_LIST = [
        502 => '110114004',
        515 => '110114008',
        535 => '110114009',
        537 => '110114115',
        538 => '110114104',
        539 => '110114111',
        540 => '110114010',
        561 => '110114110',
        565 => '110114112',
        614 => '110114011',
        740 => '110114012',
        780 => '110114113',
        781 => '110114120',
        814 => '110114001',
        815 => '110114005',
        816 => '110114118',
        817 => '110114003',
        818 => '110114002',
        819 => '110114119',
        820 => '110114013',
        // 821=> '', // 备用
        822 => '110114007',
        // 823=> '', // 备用
        824 => '110114116',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->crtTime = Cache::get('changping_place_time', date("Y-m-d H:i:s"));
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
        // dd(2);
        $this->pushDevice();
        // $this->pushAlarm($imeis);
        $this->line('推送完成');
    }

    public function pushPlace(): void
    {
        $method = 'api.v2.unit.addResidence';
        $limit  = 10;
        $offset = 0;
        $time = "-3 days";
        // 清空表数据
        /*DB::connection('mysql2')->table('thirdparty_unique_key')
            ->where('tuk_thpl_id', 9)
            ->whereNotNull('tuk_plac_id')
            ->delete();
        die;*/

        $query = Place::on('mysql2')
            ->where('plac_node_ids', 'like', '%,462,%')
            ->where('plac_name', '<>', '')
            ->where('plac_address', '<>', '')
            ->where('plac_lng', '<>', '')
            ->where('plac_lat', '<>', '')
            ->where('plac_crt_time', '>=', date('Y-m-d', strtotime($time)))
            // ->where('plac_crt_time', '>=', date('Y-m-d', strtotime("-3 days")))
            ->leftJoin('user', 'user.user_id', '=', 'place.plac_user_id')
            ->leftJoin('order', 'order.order_id', '=', 'place.plac_order_id');
        $total = $query->count();
        // dd($total);
        if ($total === 0) {
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
                // 处理字符串
                $NodeIdsArray = explode(',', trim($item->plac_node_ids, ','));
                // dd($NodeIdsArray);
                $streeNodeId = $NodeIdsArray[2] ?? 0;

                $list[] = [
                    "companyName"   => $item->plac_name,
                    "address"       => $item->plac_address,
                    "areaCode"      => self::NODE_IDS_LIST[$streeNodeId] ?? '110114111', // 区域编码全是“南邵镇”，暂时写死 todo
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
            // $this->line('推送地址中：' . json_encode($data, JSON_UNESCAPED_UNICODE));

            $res = json_decode((new ChangpingServer())->sendRequest($method, $data), true);
            if ($res && $res['success']) {
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
                // 存在相同tuk_plac_id，则只更新tuk_thirdparty_uk
                DB::connection('mysql2')->table('thirdparty_unique_key')->upsert(
                    $insertData,                // 要插入或更新的数据
                    ['tuk_plac_id'],               // 唯一索引字段
                    ['tuk_thirdparty_uk']     // 如果记录已存在
                );
                // DB::connection('mysql2')->table('thirdparty_unique_key')->insert($insertData);
                $this->info("昌平推送地址成功");
            } else {
                $this->info('昌平推送地址失败' . json_encode($res));

                $this->error(json_encode($res, JSON_UNESCAPED_UNICODE));
            }
            // 更新偏移量
            $offset += $limit;
        }
        // Cache::put('changping_place_time', date("Y-m-d H:i:s"), 60 * 60 * 24 * 365); // 缓存一年
    }

    public function pushDevice()
    {
        $method = 'api.v2.unit.addUserMonitorInfo';
        $limit  = 10;
        $offset = 0;
        $time = '-3 days';
        $query  = SmokeDetector::on('mysql2')
            ->where('smde_node_ids', 'like', '%' . ',462,' . '%')
            ->where('smde_user_id', '<>', 0)
            ->where(function($query) use ($time) {
                $query->where('smde_crt_time', '>=', date('Y-m-d H:i:s', strtotime($time)))
                    ->Orwhere('smde_last_heart_beat', '>=', date('Y-m-d H:i:s', strtotime($time)));
            })
            // ->where('plac_crt_time', '>=', date('Y-m-d', strtotime("-3 days")))
            // ->whereIn('smde_place_id', [367549, 367890, 369885, 380177])
            // ->where('smde_deliver_time', '>=', $this->crtTime)
            ->leftJoin('place', 'place.plac_id', '=', 'smoke_detector.smde_place_id');
        $total = $query->count();
        $this->line('昌平烟感数量：' . $total);
        // die;
        // dd($total);
        if ($total === 0) {
            $this->info('没有需要推送的昌平烟感');
            return;
        }
        while ($offset < $total) {
            // 查找出昌平节点下的所有烟感
            $devices = $query
                ->skip($offset)->take($limit)
                ->orderBy('smde_id', 'desc')
                ->get();

            $list = [];
            // dd($devices);

            foreach ($devices as $item) {
                if($item->smde_last_heart_beat > date('Y-m-d H:i:s', strtotime("-2 day"))){
                    $item->smde_online = 1;
                    // 两天内有心跳的，则认为是在线的
                    $item->save();
                }
                $companyCode = DB::connection('mysql2')->table('thirdparty_unique_key')
                    ->where('tuk_plac_id', $item->smde_place_id)
                    ->value('tuk_thirdparty_uk');
                if (!empty($companyCode)) {
                    $list[] = [
                        "companyCode"    => $companyCode,
                        'monitorName'    => $item->smde_type,
                        'monitorCode'    => $item->smde_imei,
                        'monitorType'    => self::MONITOR_TYPES[$item->smde_model_name] ?? '',
                        'installDate'    => $item->smde_deliver_time ?? date('Y-m-d H:i:s', strtotime($item->smde_crt_time)),
                        'imei'           => $item->smde_imei,
                        'installAddress' => $item->plac_address,
                        'networkDate'    => $item->smde_deliver_time ?? date('Y-m-d H:i:s', strtotime($item->smde_crt_time)),
                        'runState'       => 0, // 运行状态（0正常 1故障 2报警）
                        'monitorState'   => $item->smde_online ? 0 : 1, // 0在线 1离线
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
