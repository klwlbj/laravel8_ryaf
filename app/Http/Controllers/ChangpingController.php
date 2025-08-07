<?php

namespace App\Http\Controllers;

use App\Models\SmokeDetector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Server\ChangpingServer;

class ChangpingController extends BaseController
{
    public function pushAlarm(int $ionoId, $type = 1)
    {
        // 查找出昌平节点下的所有设备
        $imeis = SmokeDetector::on('mysql2')
            ->where('smde_node_ids', 'like', '%' . '462' . '%')
            ->where('smde_user_id', '<>', 0)
            ->pluck('smde_imei');

        // 查出所有警报
        $alarm = DB::connection('mysql2')->table('iot_notification_alert')
                ->whereIn('iono_type', [1, 2, 3, 4])
                ->where('iono_id', $ionoId)
                ->whereIn('iono_imei', $imeis)
                ->first();
        if (!$alarm) {
            return $this->response([], '没有报警数据');
        }
        if ($type == 1) {
            $res = $this->addFireAlarm($alarm);
        } else {
            $res = $this->updateFireAlarm($alarm);
        }
        return $res;

        if ($res['code'] == 200) {
            return $this->response($res, '推送成功');
        }
        return $this->response($res, '推送失败');
    }

    private function addFireAlarm($alarm)
    {
        $method      = 'api.v2.unit.addFireAlarm';
        $description = "警情:";
        // if ($alarm->iono_status == '待处理' || $alarm->iono_status == '已忽略') {
        if (!empty($alarm->iono_smoke_scope)) {
            $description .= "烟雾浓度" . round($alarm->iono_smoke_scope / 100, 2) . "dB/m；";
        }
        if (!empty($alarm->iono_temperature)) {
            $description .= "环境温度" . ($alarm->iono_temperature / 100) . "℃；";
        }
        // } else {
        //     $description .= $alarm->iono_conclusion;
        // }

        $data = [
            'monitorCode'     => $alarm->iono_imei,
            'nodeCode'        => $alarm->iono_imei,
            'happenTime'      => date('Y-m-d H:i:s', strtotime($alarm->iono_crt_time)),
            'alarmObjectType' => 2, // todo 报警类别（0网关  1探测点  2监测点）
            'alarmCode'       => '2-2-0',
            'alarmSummary'    => $description, // 警情描述
            'alarmSketch'     => $description, // 警情简述
            'dataId'          => $alarm->iono_id,
        ];

        $res = json_decode((new ChangpingServer())->sendRequest($method, $data), true);
        Log::channel('changping')->info('昌平报警推送内容：' . json_encode($data));
        Log::channel('changping')->info('昌平报警推送返回：' . json_encode($res));
        if (isset($res['success']) && $res['success']) {
            $keyValue   = $res['data'];
            $insertData = [];
            foreach ($keyValue as $key => $value) {
                $insertData[] = [
                    'tuk_iono_id'       => (int) $key,
                    'tuk_thirdparty_uk' => (int) $value,
                    'tuk_thpl_id'       => 9,
                ];
            }
            DB::connection('mysql2')->table('thirdparty_unique_key')->insert($insertData);
        }
        return $res;
    }

    private function updateFireAlarm($alarm)
    {
        $method  = 'api.v2.unit.updateFireAlarm';
        $alarmSn = DB::connection('mysql2')->table('thirdparty_unique_key')->where('tuk_iono_id', $alarm->iono_id)->value('tuk_thirdparty_uk');
        if (empty($alarm)) {
            return $this->response([], '没有报警数据');
        }
        $description = $alarm->iono_conclusion ?: '无';

        $data = [
            'alarmSn'          => $alarmSn,
            'dataId'           => $alarm->iono_id,
            'alarmType'        => $alarm->iono_conclusion == '确认警情' ? 1 : 2, // 信息类型(0未处理、1真实火警、2误报)
            'acceptancePerson' => DB::connection('mysql2')->table('node_account')->where('noac_id', $alarm->iono_handle_node_account_id)->value('noac_name'),
            'acceptanceTime'   => $alarm->iono_handle_time,
            'checkStatus'      => ($alarm->iono_status == '待处理' || $alarm->iono_status == '已忽略') ? 2 : 1, // 状态：0待核实，1，进行中，2已核实
            'alarmCause'       => $description, // 警情原因,
        ];
        // dd($data);
        $res = (new ChangpingServer())->sendRequest($method, $data);
        Log::channel('changping')->info('昌平修改报警推送内容：' . json_encode($data));
        Log::channel('changping')->info('昌平修改报警推送返回：' . ($res));
        return $res;
    }

    public function addHeartBeat($imei)
    {
        $method = 'api.v2.unit.addHeartBeat';
        // 在缓存中获取自增id
        $dataId = cache()->get('changping_device_data_id_' . $imei);
        if (!$dataId) {
            $dataId = 0;
        }

        // 获取imei的最后在线时间
        $device = DB::connection('mysql2')->table('smoke_detector')
            ->where('smde_node_ids', 'like', '%' . '462' . '%')
            ->where('smde_user_id', '<>', 0)
            ->where('smde_imei', $imei)
            ->first();

        if (!$device) {
            return $this->response([], '设备不存在');
        }
        $lastTime    = $device->smde_last_heart_beat;
        $smdePlaceId = $device->smde_place_id;

        $companyCode = DB::connection('mysql2')->table('changping_key_value')->where('cp_key', $smdePlaceId)->value('cp_value');
        if (!empty($companyCode)) {
            $data = [
                'monitorCode'  => $imei,
                'dataId'       => $imei . $dataId,
                "companyCode"  => $companyCode,
                "alarmSummary" => "online",
                "happenTime"   => $lastTime,
            ];
            // 自增id加1
            $dataId++;
            // 将自增id存入缓存
            cache()->put('changping_device_data_id_' . $imei, $dataId);
            // dd($data);

            $res = (new ChangpingServer())->sendRequest($method, $data);
            Log::channel('changping')->info('昌平心跳推送内容：' . json_encode($data));
            Log::channel('changping')->info('昌平心跳推送返回：' . json_encode($res));

            return $res;
        }
        return $this->response([], '位置不存在');
    }

    public function queryDevice()
    {
        $imeis = [
            864440068733370,
            864440068680605,
            864440068162448,
            864440068314999,
            863758072281792,
            864440068525586,
            866330078367590,
            863758072568123,
            863758072849994,
            866330078497207,
            864440068218679,
            864440068523987,
            864440068029050,
            861877076595445,
            863758072886723,
            863758072205668,
            861346078506396,
            861346079257049,
            867945070705767,
            867945070559578,
            863182070098209,
            863758073030768,
            863182070497765,
            861346078801854,
            864982073730958,
            862041079768553,
            865777075174133,
            862361072596185,
            862361072772422,
            866960076277712,
            866960076855384,
            866960076441078,
            862361072680328,
            866960076102845,
            866960076302452,
            866960076455409,
            862752073373048,
            862752073958038,
            862752071472321,
            862752073373337,
            862752073925953,
            862752073926563,
            863758072557092,
            863758072820789,
            862752073983846,
        ];
        foreach ($imeis as $imei) {
            $data = [
                'monitorCode' => $imei,
                "pageNo"      => 1,
            ];
            $method = 'api.v2.unit.selectUserMonitorInfo';
            $res    = (new ChangpingServer())->sendRequest($method, $data);
            // dd($res);
            $resList = json_decode($res, true);
            // dd($resList);
            if ($resList['success']) {
                $resData = json_decode($resList['data'], true);
                if (count($resData) > 0) {
                    $item = $resData[0];
                    // 更新thirdparty_unique_key表

                    $smdeId = DB::connection('mysql2')->table('smoke_detector')
                        ->where('smde_imei', $imei)
                        ->value('smde_id');

                    if ($smdeId) {
                        DB::connection('mysql2')->table('thirdparty_unique_key')->Insert(
                            [
                                'tuk_smde_id'       => $smdeId,
                                'tuk_thirdparty_uk' => $item['id'],
                                'tuk_thpl_id'       => 9, // 昌平
                            ],
                        );
                    }
                }
            } else {
                $res = [];
            }
            Log::channel('changping')->info('昌平查询设备推送内容：' . json_encode($data));
            Log::channel('changping')->info('昌平查询设备推送返回：' . json_encode($res));
        }
            return 1;
    }


    public function deleteDevice()
    {
        $data = [
            'ids' => "1942797957089259524,1950368744144388097,1927991877877915650,1927991877869527067",
        ];
        $method = 'api.v2.unit.delUserMonitorInfo';
        $res    = (new ChangpingServer())->sendRequest($method, $data);
        Log::channel('changping')->info('昌平删除设备推送返回：' . json_encode($res));
        return 2;

    }
}
