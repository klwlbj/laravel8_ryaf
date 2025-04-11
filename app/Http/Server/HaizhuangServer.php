<?php

namespace App\Http\Server;

use App\Models\SmokeDetector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HaizhuangServer
{
    public const LOOP_TYPES = [
        'smoke'       => 'V608', // 烟雾
        'temperature' => 'V602', // 温度
        'battery'     => 'V607', // 电量
    ];

    public const MSG_TYPES = [
        '0'  => '01',
        '1'  => '10',
        '2'  => '01',
        '3'  => '10',
        '4'  => '01',
        '5'  => '10',
        '6'  => '01',
        '7'  => '10',
        '8'  => '01',
        '9'  => '20',
        '10' => '01',
        '11' => '20',
        '12' => '01',
        '13' => '01',
        '14' => '01',
        '15' => '20',
        '16' => '01',
        '17' => '20',
        '18' => '01',
    ];

    public const MONITOR_TYPES = [
        'HM-618PH-4G' => 1901549230328725505,
        'YL-IOT-YW03' => 1901549715760693250,
        'HM-608PH-NB' => 1904368738677837825,
    ];

    protected $url = 'https://test.xfirecloud.com:1099'; // 测试环境
    // protected $url = 'https://www.xfirecloud.com:1099'; // 正式环境

    protected $token = '';

    public function __construct()
    {
        DB::setDefaultConnection('mysql2');
        $this->token = $this->getToken();
    }

    /**
     * 获取第三方 Token 并缓存
     */
    public function getToken()
    {
        Cache::forget('xfirecloud_token'); // 清除缓存
        // 检查缓存中是否已经有 Token
        $cachedToken = Cache::get('xfirecloud_token');

        if ($cachedToken) {
            // 如果缓存中有 Token，直接返回
            return $cachedToken;
        }

        // 如果缓存中没有 Token，向第三方 API 请求 Token
        $response = Http::withOptions([
            'verify' => false,  // 禁用 SSL 验证
        ])
            ->post($this->url . '/api/iff/gather/login', [
                'account'  => config('services.haizhuang.account'), // 替换为实际的账号
                'password' => config('services.haizhuang.password'), // 替换为实际的密码
            ]);

        // 假设返回的 JSON 格式中有 'token' 字段
        if ($response->successful() && !empty($response->json()['token'])) {
            $token = $response->json()['token'];

            // 将获取到的 Token 缓存 3000 分钟
            Cache::put('xfirecloud_token', $token, 3000);
            // dd($token);
            return $token;
        }

        // 如果请求失败，返回错误
        return false;
    }

    public function pushDevice($onlyReturn = 0)
    {
        $list = SmokeDetector::query()->where('smde_status', '已交付')
            ->where('smde_node_ids', 'like', '%,106,%')
            ->where('smde_imei', '!=', '868550060116550')// 缺少地址不推送
            ->leftJoin('place', 'smde_place_id', '=', 'plac_id')
            ->leftJoin('order', 'smde_order_id', '=', 'order_id')
            ->leftJoin('user', 'plac_user_id', '=', 'user_id')
            ->select('smde_id', 'smde_imei', 'smde_node_ids', 'plac_lng', 'plac_lat', 'plac_address', 'smde_model_name', 'user_name', 'user_mobile', 'plac_name', 'smde_type', 'order_user_name', 'order_user_mobile')
            ->get();
        // dd(BaseModel::printSql($list)); // 打印SQL语句 todo
        $imeis = $list->pluck('smde_imei')->toArray();
        Log::info('token:' . $this->token);
        // dd(count($list));
        if ($onlyReturn) {
            return $imeis;
        }

        Log::info('Total: ' . count($list));
        foreach ($list as $item) {
            $coordinate = $this->transformCoordinate(floatval($item->plac_lng), floatval($item->plac_lat));
            // 定义请求参数
            $userName   = !empty($item->user_name) ? $item->user_name : $item->order_user_name;
            $userMobile = !empty($item->user_mobile) ? $item->user_mobile : $item->order_user_mobile;
            $data       = [
                "accessWay"            => "SQ",
                "manufacturer"         => "RY",
                "deviceName"           => $item->smde_model_name,
                "deviceAreaNum"        => "0",
                "deviceLoopNum"        => "0",
                "deviceId"             => $item->smde_imei,
                "installationLocation" => $item->plac_address,
                "facilityType"         => $item->smde_type === '烟感' ? "HZ001" : 'HZ002', // 烟感或温感
                "deviceType"           => 'XF02', // 烟感或温感
                "gridInspector"        => $userName,
                "gridInspectorNumber"  => $userMobile,
                "gridInspectorUnit"    => $item->plac_name,
                "x"                    => $coordinate['bd_lon'],
                "y"                    => $coordinate['bd_lat'],
                "contact"              => $userName,
                "contactNumber"        => $userMobile,
                "province"             => "350002020000000000100027", // 写死
                "city"                 => "350002020000000000100037",
                "region"               => "350002020000000000103354",
                "street"               => "350002020000000000125766",
                "usePlace"             => "13",
            ];

            // dd(json_encode($data));
            // 发送 POST 请求
            $response = Http::withOptions([
                'verify' => false,  // 禁用 SSL 验证
            ])
                ->withHeaders([
                    'token' => $this->token, // 将 Token 添加到 header 中
                ])->post($this->url . '/api/iff/gather/baseData', $data);

            // dd($response->body());
            // 检查请求是否成功
            if ($response->successful()) {
                Log::info($item->smde_imei . $response->body());
                continue;
            }

            // 请求失败时返回错误信息
            Log::info($item->smde_imei . 'Request failed' . $response->body());
            // Log::info($token);
        }
        return $imeis;
    }

    public function pushAlarm($imeis = [], $ionoId = 0)
    {
        $alarms = DB::connection('mysql2')->table('iot_notification')
            ->where('iono_type', '!=', -1)
            // ->whereIn('iono_type', [0, -1])
            // ->whereIn('iono_type', [1, 2])
            ->when($imeis, function ($query) use ($imeis) {
                return $query->whereIn('iono_imei', $imeis);
            })
            ->limit(100)
            ->when($ionoId, function ($query) use ($ionoId) {
                return $query->where('iono_id', $ionoId);
            })
            ->orderBy('iono_id', 'desc')
            ->get();
        // dd($alarms);
        if (empty($alarms)) {
            return ['msg' => '没有报警数据'];
        }

        $success = $fail = 0;
        foreach ($alarms as $alarm) {
            $dataPack = [];
            if (isset($alarm->iono_temperature)) {
                $dataPack[] = [
                    "loopType"   => self::LOOP_TYPES['temperature'],
                    'currentVal' => $alarm->iono_temperature,
                    "limitHigh"  => empty($alarm->iono_threshold_temperature) ? 6000 : ($alarm->iono_threshold_temperature == 60 ? $alarm->iono_threshold_temperature * 100 : $alarm->iono_threshold_temperature),
                    "limitLow"   => 0,
                ];
            }
            if (isset($alarm->iono_smoke_scope)) {
                $dataPack[] = [
                    "loopType"   => self::LOOP_TYPES['smoke'],
                    "currentVal" => $alarm->iono_smoke_scope,
                    "limitHigh"  => empty($alarm->iono_threshold_smoke_scope) ? 40 : $alarm->iono_threshold_smoke_scope,
                    "limitLow"   => 0,
                ];
            }
            if (isset($alarm->iono_nb_module_battery)) {
                $dataPack[] = [
                    "loopType"   => self::LOOP_TYPES['battery'],
                    'currentVal' => $alarm->iono_nb_module_battery,
                    "limitHigh"  => 100,
                    "limitLow"   => empty($alarm->iono_threshold_nb_module_battery) ? 20 : $alarm->iono_threshold_nb_module_battery,
                ];
            }
            $data = [
                "accessWay"     => "SQ",
                'deviceAreaNum' => 0,
                'deviceId'      => $alarm->iono_imei,
                'deviceLoopNum' => 0,
                'msgType'       => self::MSG_TYPES[$alarm->iono_type] ?? '01',
                'gatherTime'    => date('Y-m-d H:i:s', strtotime($alarm->iono_crt_time)),
                // 'loopType'      => self::LOOP_TYPES[$alarm->iono_type] ?? 'V604',
                "dataPack"      => $dataPack,
            ];

            // 发送 POST 请求
            $response = Http::withOptions([
                'verify' => false,  // 禁用 SSL 验证
            ])
                ->withHeaders([
                    'token' => $this->token, // 将 Token 添加到 header 中
                ])->post($this->url . '/api/iff/gather/msg', $data);

            // dd($response->body());
            // 检查请求是否成功
            if ($response->successful()) {
                $success++;
                Log::info(json_encode($data));
                Log::info($alarm->iono_imei . $response->body());
                continue;
            }
            $fail++;
            Log::info($alarm->iono_imei . 'Push alarm failed' . $response->body());
        }
        return ['success' => $success, 'fail' => $fail];
    }

    /** GCJ-02(火星，高德)坐标转换成BD-09(百度)坐标
     * @param bd_lon 百度经度
     * @param bd_lat 百度纬度
     */
    public function transformCoordinate($gg_lon, $gg_lat)
    {
        $x_pi   = 3.14159265358979324 * 3000.0 / 180.0;
        $x      = $gg_lon;
        $y      = $gg_lat;
        $z      = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta  = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $bd_lon = $z * cos($theta) + 0.0065;
        $bd_lat = $z * sin($theta) + 0.006;
        // 保留小数点后六位
        $data['bd_lon'] = round($bd_lon, 6);
        $data['bd_lat'] = round($bd_lat, 6);
        return $data;
    }
}
