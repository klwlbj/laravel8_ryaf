<?php

namespace App\Console\Commands;

use App\Models\SmokeDetector;
use Illuminate\Console\Command;
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

    protected $url = 'https://test.xfirecloud.com:1099';

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
        echo $this->push();
    }

    /**
     * 获取第三方 Token 并缓存
     */
    public function getToken()
    {
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
                'account'  => 'M8mxcyv35HI=',
                'password' => 'j05OpYMpC9Nem35lxHLhOg==',
            ]);

        // 假设返回的 JSON 格式中有 'token' 字段
        if ($response->successful()) {
            $token = $response->json()['token'];

            // 将获取到的 Token 缓存 3000 分钟
            Cache::put('xfirecloud_token', $token, 3000);

            return $token;
        }

        // 如果请求失败，返回错误
        return false;
    }

    public function push()
    {
        $token = $this->getToken();
        if (!$token) {
            return false;
        }

        $list = SmokeDetector::query()->where('smde_status', '已交付')
            ->where('smde_node_ids', 'like', '%,106,%')
            ->where('smde_imei', '!=', '868550060116550')// 缺少地址不推送
            ->leftJoin('place', 'smde_place_id', '=', 'plac_id')
            ->leftJoin('order', 'smde_order_id', '=', 'order_id')
            ->leftJoin('user', 'plac_user_id', '=', 'user_id')
            ->select('smde_id', 'smde_imei', 'smde_node_ids', 'plac_lng', 'plac_lat', 'plac_address', 'smde_model_name', 'user_name', 'user_mobile', 'plac_name', 'smde_type', 'order_user_name', 'order_user_mobile')
            ->get();
        $this->line('Total: ' . count($list));
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
                "placeBuilding"        => "平云大厦 A 塔",
                "placeFloor"           => "1",
                "facilityType"         => $item->smde_type === '烟感' ? "HZ001" : 'HZ002', // 烟感或温感
                "deviceType"           => 'XF02', // 烟感或温感
                "gridInspector"        => $userName,
                "gridInspectorNumber"  => $userMobile,
                "gridInspectorUnit"    => $item->plac_name,
                "x"                    => $coordinate['bd_lon'],
                "y"                    => $coordinate['bd_lat'],
                "contact"              => $userName,
                "contactNumber"        => $userMobile,
                "province"             => "350002020000000000100027",
                "city"                 => "350002020000000000100037",
                "region"               => "350002020000000000103354",
                "street"               => "350002020000000000125766",
                "usePlace"             => "13",
            ];

            // 发送 POST 请求
            $response = Http::withOptions([
                'verify' => false,  // 禁用 SSL 验证
            ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token, // 将 Token 添加到 header 中
                ])->post('https://test.xfirecloud.com:1099/api/iff/gather/baseData', $data);

            // 检查请求是否成功
            if ($response->successful()) {
                // $this->line($item->smde_imei . 'Request successful');
                continue;
            }

            // 请求失败时返回错误信息
            $this->line($item->smde_imei . 'Request failed' . $response->body());
            $this->line($token);
        }
        return 'Push success';
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
