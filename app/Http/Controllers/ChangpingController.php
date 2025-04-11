<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Server\ChangpingServer;
use App\Http\Server\HaizhuangServer;
use App\Models\SmokeDetector;
use Illuminate\Support\Facades\DB;

class ChangpingController extends BaseController
{
    public function pushAlarm(int $ionoId)
    {
        // 查找出昌平节点下的所有设备
        $imeis = SmokeDetector::on('mysql2')
            ->where('smde_node_ids', 'like', '%' . '462' . '%')
            ->where('smde_user_id', '<>', 0)
            ->pluck('smde_imei');

        // 查出所有警报
        $alarm = DB::connection('mysql2')->table('iot_notification_alert')
            // ->where('fire_alarm_status', 0)
            ->where('iono_id', $ionoId)
            ->whereIn('iono_imei', $imeis)
            ->first();
        if(!$alarm) {
            return $this->response([], '没有报警数据');
        }

        $method = 'api.v2.unit.addFireAlarm';

        $data = [
            'monitorCode'     => $alarm->iono_imei,
            'nodeCode'        => $alarm->iono_imei,
            'happenTime'      => date('Y-m-d H:i:s', strtotime($alarm->iono_crt_time)),
            'alarmObjectType' => 2, // todo 报警类别（0网关  1探测点  2监测点）
            'alarmCode'       => '2-2-0',
            'alarmSummary'    => $alarm->iono_conclusion, // 警情描述
            'alarmSketch'     => $alarm->iono_remark, // 警情简述
            'dataId'          => $alarm->iono_id,
        ];
        return (new ChangpingServer())->sendRequest($method, $data);
    }
}
