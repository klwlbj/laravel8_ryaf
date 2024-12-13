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
    public const PRODUCT_MASTERKEY_ARRAY = [
        16922967 => "0434f19136324920a51ba7287fffb667", // '平安穗粤-海曼HM608/618NB透传版'
        // todo
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

    public const ONET_STATUS_ARRAY = [
        0 => '离线', 1 => '在线', 2 => '未激活',
    ];

    public const CARD_STATUS_ARRAY = [
        2  => '沉默期',
        4  => '已停机',
        5  => '已断网',
        8  => '待激活',
        9  => '正常使用',
        20 => '期满,关停',
    ];

    public function queryImei(Request $request)
    {
        $imei = $iccid = $request->input('key');
        $type = $request->input('type', 1);

        if ($type == 1) {
            $client = new CTWing();// 电信平台

            foreach (self::PRODUCT_MASTERKEY_ARRAY as $productId => $masterKey) {
                $res = json_decode($client->QueryDeviceByImei($productId, $imei, $masterKey), true);

                $cwtingDeviceStatus = '未导入';
                $cwtingNetStatus    = '';
                if ($res['code'] == 0) {
                    // return $res;
                    $cwtingDeviceStatus = self::CWTING_DEVICE_STATUS_ARRAY[$res['result']['deviceStatus'] ?? ''] ?? '未导入';
                    $cwtingNetStatus    = self::CWTING_NET_STATUS_ARRAY[$res['result']['netStatus'] ?? ''] ?? '';
                    break;
                }
            }

            $client = new OneNet();// 移动平台

            $res          = $client->deviceInfo(0, $imei);
            $onenetStatus = '未导入';
            if ($res['code'] == 0) {
                $onenetStatus = self::ONET_STATUS_ARRAY[$res['data']['status']] ?? '';
                // $onenetLastTime = $res['data']['last_time'] ?? '';
            }

            return response()->json([
                'message'   => '查询成功',
                'data'      => '电信cwting状态： ' . $cwtingDeviceStatus . $cwtingNetStatus . '; ' . '移动onenet状态： ' . $onenetStatus,
                'smde_imei' => SmokeDetector::query()->where('smde_imei', $imei)->value('smde_nb_iid'),
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
                'data'      => $data,
                'smde_imei' => SmokeDetector::query()->where('smde_nb_iid2', $iccid)->value('smde_nb_iid'),
            ]);
        }
    }

    public function view()
    {
        return view('imeiCheck');
    }
}
