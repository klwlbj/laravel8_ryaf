<?php

namespace App\Http\Controllers;

use App\Utils\CTWing;
use App\Utils\OneNet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\SmokeDetector;
use Illuminate\Support\Facades\DB;

class IMEICheckController extends BaseController
{
    public function __construct()
    {
        DB::setDefaultConnection('mysql2');
    }
    public const CWTING_PRODUCT_MASTERKEY_ARRAY = [
        16922967 => [
            'masterKey' => "0434f19136324920a51ba7287fffb667",
            'type'      => "LWM2M",
            "net_type"  => "NB",
            'name'      => "平安穗粤-海曼HM608/618NB透传版",
        ],
        17085637 => [
            'masterKey' => "9c6f31f078024c0c8ab9d288eeaec206",
            'type'      => "LWM2M",
            "net_type"  => "4G",
            'name'      => "平安穗粤-六瑞-如约4G定制版",
        ],
        15084506 => [
            'masterKey' => "32fcdd66128548629c1cf93771afd22b",
            'type'      => "LWM2M",
            "net_type"  => "NB",
            'name'      => "小微安全-东昂JTY-YG-002NB",
        ],
        17038132 => [
            'masterKey' => "66289024a00d49578a77933298ee2b5a",
            'type'      => "LWM2M",
            "net_type"  => "NB",
            'name'      => "小微安全-海曼HM608/618NB透传版",
        ],
        15599428 => [
            'masterKey' => "08d3fbc2c028477c8afc3e9ce60d714d",
            'type'      => "LWM2M",
            "net_type"  => "NB",
            'name'      => "平安穗粤-海曼HM608NB",
        ],
        17084269 => [
            'masterKey' => "c9636dcae10841aa859d5511589483b6",
            'type'      => "MQTT",
            "net_type"  => "4G",
            'name'      => "平安穗粤-海曼HM6184G",
        ],
        17102042 => [
            'masterKey' => "3859262b741f40d0a0c3bd8d64a5cebe",
            'type'      => "MQTT",
            "net_type"  => "4G",
            'name'      => "源流Y3_4G_烟感MQTT",
        ],
    ];
    public const ONENET_PRODUCT_MASTERKEY_ARRAY = [
        'E2dMYR85jh' => [
            'type'     => "4G",
            "net_type" => "4G",
            'name'     => "海曼4g烟感",
        ],
        'HzFl9NvY5q' => [
            'type'     => "4G",
            "net_type" => "4G",
            'name'     => "源流4G烟感",
        ],
        '8Sq1N7k9OB' => [
            'type'     => "NB",
            "net_type" => "NB",
            'name'     => "安宁消防",
        ],
        "kC06Yb93QB" => [
            'type'     => "4G",
            "net_type" => "4G",
            'name'     => "六瑞4G烟感",
        ],
        '407478'     => [
            'type'     => "NB",
            "net_type" => "NB",
            'name'     => "平安穗粤",
        ],
        '426083'     => [
            'type'     => "NB",
            "net_type" => "NB",
            'name'     => "小微安全",
        ],
        '341854'     => [
            'type'     => "NB",
            "net_type" => "NB",
            'name'     => "智慧消防",
        ],
    ];

    public const CWTING_DEVICE_STATUS_ARRAY = [
        '0' => '已注册',
        '1' => '已激活',
        '2' => '已注销',
    ];

    public const CWTING_NET_STATUS_ARRAY = [
        '1' => '在线',
        '2' => '离线',
    ];

    public const ONENET_STATUS_ARRAY = [
        0 => '离线',
        1 => '在线',
        2 => '未激活',
    ];

    public const CARD_STATUS_ARRAY = [
        2  => '沉默期', // 不使用不计费，过了沉默期系统自动计费，进入待激活
        4  => '已停机', // 电信
        5  => '已断网', // 移动
        8  => '待激活',
        9  => '正常使用',
        20 => '期满,关停',
    ];

    public function queryImei(Request $request)
    {
        $imei = $iccid = trim($request->input('key'));
        $type = $request->input('type', 1);

        if ($type == 1) {
            $client       = new CTWing();// 电信平台
            $cwtingDetail = [
                '状态' => "未导入",
            ];

            foreach (self::CWTING_PRODUCT_MASTERKEY_ARRAY as $productId => $item) {
                // 如果是4g
                if ($item['type'] == 'MQTT') {
                    $res = json_decode($client->QueryDevice($productId, $productId . $imei, $item['masterKey']), true);
                } else {
                    // 如果是LWM2M
                    $res = json_decode($client->QueryDeviceByImei($productId, $imei, $item['masterKey']), true);
                }
                if ($res['code'] == 0 && isset($res['result'])) {
                    $cardType           = $item['net_type'];
                    $groupName          = $item['name'];
                    $cwtingDeviceStatus = self::CWTING_DEVICE_STATUS_ARRAY[$res['result']['deviceStatus'] ?? ''] ?? '未导入';
                    $cwtingNetStatus    = self::CWTING_NET_STATUS_ARRAY[$res['result']['netStatus'] ?? ''] ?? '未知';
                    $activeTime         = (!empty($res['result']['activeTime'])) ? date("Y-m-d H:i:s", $res['result']['activeTime'] / 1000) : '空';
                    $createTime         = (!empty($res['result']['createTime'])) ? date("Y-m-d H:i:s", $res['result']['createTime'] / 1000) : '空';
                    $onlineAt           = (!empty($res['result']['onlineAt'])) ? date("Y-m-d H:i:s", $res['result']['onlineAt'] / 1000) : '空';
                    $cwtingDetail       = [
                        '卡片类型'   => $cardType,
                        '设备分组'   => $groupName,
                        '设备状态'   => $cwtingDeviceStatus,
                        "网络状态"   => $cwtingNetStatus,
                        "激活时间"   => $activeTime,
                        '创建时间'   => $createTime,
                        '最近在线时间' => $onlineAt,
                    ];
                    break;
                }
            }

            $client = new OneNet();// 移动平台

            $onenetDetail = [
                '状态' => "未导入",
            ];
            foreach (self::ONENET_PRODUCT_MASTERKEY_ARRAY as $productId => $item) {
                $res = $item['type'] === '4G' ? $client->deviceInfo($productId, $imei) : $client->deviceInfoNB($imei);
                if ($res['code'] == 0) {
                    $onenetCardType   = $item['type'];
                    $onenetGroupName  = $item['type'] === '4G' ? $item['name'] : self::ONENET_PRODUCT_MASTERKEY_ARRAY[$res['data']['pid']]['name'] ?? '未知';
                    $onenetStatus     = self::ONENET_STATUS_ARRAY[$res['data']['status']] ?? '空';
                    $onenetActiveTime = (!empty($res['data']['activate_time']) ? $this->transISOTime($res['data']['activate_time']) : '空');
                    $onenetCreateTime = (!empty($res['data']['create_time']) ? $this->transISOTime($res['data']['create_time']) : '空');
                    $onenetLastTime   = (!empty($res['data']['last_time']) ? $this->transISOTime($res['data']['last_time']) : '空');
                    $onenetDetail     = [
                        '卡片类型'   => $onenetCardType,
                        '设备分组'   => $onenetGroupName,
                        '状态'     => $onenetStatus,
                        "激活时间"   => $onenetActiveTime,
                        '创建时间'   => $onenetCreateTime,
                        '最近在线时间' => $onenetLastTime,
                    ];
                    break;
                }
            }

            $smokeDetector = SmokeDetector::query()->where('smde_imei', $imei)->first();

            if ($smokeDetector) {
                $notification = DB::connection('mysql2')->table('iot_notification')->where('iono_imei', $imei)->orderBy("iono_id", 'desc')->first();
                $deviceDetail = [
                    '型号'         => $smokeDetector->smde_model_name ?? '空',
                    '设备状态'       => $smokeDetector->smde_status ?? '空',
                    "交付时间"       => $smokeDetector->smde_deliver_time,
                    "最近心跳时间"     => $smokeDetector->smde_last_heart_beat ?? '空',
                    "平台最近收到消息时间" => $notification->iono_crt_time ?? '空',
                    'IMSI'       => $smokeDetector->smde_nb_iid ?? '空',
                    "ICCID"      => $smokeDetector->smde_nb_iid2 ?? '空',
                    '是否存在订单'     => $smokeDetector->smde_order_id ? '是' : '否',
                    '是否绑定用户'     => $smokeDetector->smde_user_id ? '是' : '否',
                    '是否绑定地址'     => $smokeDetector->smde_place_id ? '是' : '否',
                    '是否连接区平台'    => $smokeDetector->smde_yunchuang_id ? "是" : '否',
                    "虚拟心跳时间"     => $smokeDetector->smde_fake_heart_beat,
                ];
            } else {
                $deviceDetail = [
                    '状态' => '未导入',
                ];
            }
            $cwtingStr = $onenetStr = $deviceStr = '';
            foreach ($cwtingDetail as $name => $detail) {
                $cwtingStr .= $name . '：' . $detail . '<br>';
            }
            foreach ($onenetDetail as $name => $detail) {
                $onenetStr .= $name . '：' . $detail . '<br>';
            }
            foreach ($deviceDetail as $name => $detail) {
                $deviceStr .= $name . '：' . $detail . '<br>';
            }

            return response()->json([
                'message' => '查询成功',
                'data'    => '<br>电信平台：<br>' . $cwtingStr .
                    '<br><br><br>移动平台：<br>' . $onenetStr
                    . '<br><br><br>平安穗粤平台：<br>' . $deviceStr,
            ]);
        }if ($type == 2) {
            $response = (new Client(['verify' => false]))->post('https://api.wl1688.net/iotc/getway', [
                'json' => [
                    "appid"     => (string) env('NB_CARD_PLATFORM_APPID'),
                    "appsecret" => (string) env('NB_CARD_PLATFORM_APPSECTRET'),
                    "name"      => "api.v2.card.info",
                    "iccid"     => $iccid,
                ],
            ]);
            $platformReturn = json_decode($response->getBody(), true);
            if (isset($platformReturn['code']) && $platformReturn['code'] === 0) {
                if (empty($platformReturn['data'])) {
                    $data = '查询卡商平台失败';
                } else {
                    $list = $platformReturn['data'][0];
                    $data = '卡商平台查询数据：到期时间：' . $list['cardEndTime'] . '; IMSI：' . $list['imsi'] . '; 卡片状态：' . self::CARD_STATUS_ARRAY[$list['cardStatus']] ?? '空';
                }
            }

            return response()->json([
                'message'   => '查询成功',
                'data'      => $data ?? '',
                'smde_imei' => SmokeDetector::query()->where('smde_nb_iid2', $iccid)->value('smde_nb_iid') ?: '空',
            ]);
        }
    }

    public function view()
    {
        return view('imeiCheck');
    }
}
