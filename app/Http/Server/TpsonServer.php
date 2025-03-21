<?php

namespace App\Http\Server;

use App\Models\Node;
use App\Models\Place;
use App\Models\AlertReceiver;
use App\Models\SmokeDetector;
use App\Models\DeviceLastestData;
use App\Models\ThirdpartyNotification;

class TpsonServer extends BaseServer
{
    public const int THIRD_PLATFORM_ID = 9;// 第三方平台ID，拓深专用

    public function createPlace($nodeId = 0)
    {
        // 插入place表
        return Place::query()->insertGetId([
            'plac_node_id'  => $nodeId,
            'plac_node_ids' => Node::getParentIds($nodeId) . ',' . $nodeId . ',',
            'plac_name'     => '测试',
            'plac_address'  => '测试',
            'plac_lng'      => '0',
            'plac_lat'      => '0',
            'plac_thpl_id'  => self::THIRD_PLATFORM_ID,
        ]);
    }

    public function createDevice($imei, $nodeId = 0)
    {
        $placeId      = $this->createPlace(8);
        $placeNodeIds = Node::getParentIds(8) . ',' . $nodeId . ',';
        $this->createDeviceLastData(['deviceCode' => $imei]);
        return SmokeDetector::query()->insert([
            'smde_type'       => '智能空开',
            'smde_brand_name' => 'TPSON',
            'smde_model_name' => 'MCB1000',
            'smde_imei'       => $imei,
            'smde_node_ids'   => $placeNodeIds,
            'smde_place_id'   => $placeId,
            'smde_lng'        => '0',
            'smde_lat'        => '0',
        ]);
    }

    public function createDeviceLastData(array $json = [])
    {
        $deviceId = SmokeDetector::query()->where('smde_imei', $json['deviceCode'] ?? 0)->value('smde_id');
        // dd($deviceId);
        $data     = $json['data'] ?? [];
        foreach ($data as $item) {
            switch ($item['key']) {
                case 'current':
                    $current = $item['value'];
                    break;
                case 'voltage':
                    $voltage = $item['value'];
                    break;
                case 'temperature':
                    $temperature = $item['value'];
                    break;
                default:
                    break;
            }
        }
        $leakCurrent = $json['leakCurrent'] ?? 0;// 剩余电流缺少 todo
        if(empty($json)){ // todo 有问题
            return DeviceLastestData::query()->insert([
                'dld_smde_id'      => $deviceId,
                'dld_upd_time'     => date('Y-m-d H:i:s'),
                'dld_current'      => $current ?? 0,
                'dld_voltage'      => $voltage ?? 0,
                'dld_temperature'  => $temperature ?? 0,
                'dld_leak_current' => $leakCurrent,
                'dld_other_params' => json_encode($json),
            ]);
        }

        return DeviceLastestData::query()->where('dld_smde_id', $deviceId)->update([
            // 'dld_smde_id'      => $deviceId,
            'dld_upd_time'     => date('Y-m-d H:i:s'),
            'dld_current'      => $current ?? 0,
            'dld_voltage'      => $voltage ?? 0,
            'dld_temperature'  => $temperature ?? 0,
            'dld_leak_current' => $leakCurrent,
            'dld_other_params' => json_encode($json),
        ]);
    }

    public function createNotification($json)
    {
        $deviceId = $json['deviceCode'] ?? 0;

        $device = SmokeDetector::query()
            ->where('smde_imei', $deviceId)
            ->first();
        if (!$device) {
            return false;
        }
        return ThirdpartyNotification::insert([
            'thno_thpl_id'      => self::THIRD_PLATFORM_ID,
            'thno_type'         => $json['alarmType'] ?? 0,
            'thno_alarm_status' => 1,
            'thno_raw'          => json_decode($json),
            'thno_node_id'      => $device->node_id,
            'thno_nodes_id'     => $device->nodes_id,
            'thno_status'       => 1,
            'thno_conclusion'   => '',
        ]);
    }

    public function getAlertReceiver($alertPlaceId)
    {
        return AlertReceiver::query()
            ->where('aler_plac_id', $alertPlaceId)
            ->get();
    }
}
