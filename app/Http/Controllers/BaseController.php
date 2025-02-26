<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Utils\OneNet;
use App\Models\SmokeDetector;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceCacheCommands;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BaseController extends \Illuminate\Routing\Controller
{
    /**
     * 生成json格式响应结果
     *
     * @param array $data
     * @param string $msg
     * @param int $statusCode
     * @return JsonResponse
     */
    public function response($data = [], $msg = 'success', $code = 0, $statusCode = 200)
    {
        return new JsonResponse(
            ['data' => $data, 'msg' => $msg, 'code' => $code],
            $statusCode,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    protected function validateParams($request, $rules, &$input)
    {
        // 进行验证
        // $rules     = [];// todo 待删
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
    }

    /**
     * 验签
     * @param string $msg
     * @param string $nonce
     * @param string $signature
     * @return bool
     */
    protected function checkSign(string $msg, string $nonce, string $signature): bool
    {
        $token = config('services.nb_manual_alarm.key');

        $sign = base64_encode(md5($token . $nonce . $msg, true));

        Log::info('sign:' . $sign);
        Log::info('signature:' . $signature);

        // 验证token
        if ($signature === $sign) {
            return true;
        }
        return false;
    }

    protected function aesDecrypt($encryptedData)
    {
        return openssl_decrypt($encryptedData, 'AES-128-CBC', env('NB_KEY'), OPENSSL_RAW_DATA, config('services.nb_manual_alarm.key'));
    }

    /**
     * 获取设备缓存命令并下发
     * @param $imei
     * @param string $msgId
     * @return void
     */
    public function getAndSendDeviceCacheCMD($imei, string $msgId = '', $sleepSecond = 1)
    {
        DeviceCacheCommands::query()
            ->where('imei', $imei)
            ->where('is_success', 0)
            ->get()
            ->each(function ($item) use ($msgId, $sleepSecond) {
                sleep($sleepSecond);
                // 下发命令
                $res = (new OneNet())->callService(json_decode($item->json));
                Log::info('消音返回:' . json_encode($res));
                if ($res['code'] == 0) {
                    $item->is_success = 1;
                    $item->msg_id     = $msgId;
                    $item->save();
                }
            });
    }

    /**
     * 插入设备缓存命令
     * @param $imei
     * @param $cmdJson
     * @return mixed
     */
    public function insertDeviceCacheCMD($imei, $cmdJson)
    {
        return DeviceCacheCommands::query()->insert([
            'imei'       => $imei,
            'json'       => $cmdJson,
            'type'       => 1, // 1:消音 2:解除消音...暂时写死
            'is_success' => 0, // 设定是否成功
            'created_at' => now(),
        ]);
    }

    public function transISOTime(string $dateTime)
    {
        // 2025-01-13T12:16:27.803+08:00
        // to 2025-01-13 12:16:27
        $date = new DateTime($dateTime);
        return $date->format('Y-m-d H:i:s');
    }

    public function insertIOT($deviceUpdateData, $notificationInsertData, $alarmStatus, $imei)
    {
        // $time = time();
        // inno_type 类型：0：无报警；1：烟雾报警；2：烟雾报警解除；3：温度报警；4：温度报警解除；5：烟感低电量报警；6：烟感低电量报警解除；7：NB低电量报警；8：NB低电量报警解除；9：烟雾传感器故障；10：烟雾传感器故障解除；11：温湿度传感器故障；12：温湿度传感器故障解除；13：自检测试开始；14：自检测试完成；15：防拆触发；16：防拆恢复；17：烟雾板连接断开；18：烟雾板连接恢复；；组包时高字节在前 ，低字节在后。201-电流过大；

        $url = '';
        try {
            // laravel事务代码
            DB::connection('mysql2')->transaction(function () use ($deviceUpdateData, $notificationInsertData, $alarmStatus, $imei, &$url) {
                $device = SmokeDetector::on('mysql2')->where('smde_imei', $imei)->first();
                if (!$device) {
                    return;
                }
                $smdeId = $device->smde_id;
                $device->update($deviceUpdateData);

                foreach ($alarmStatus as $ionoType) {
                    unset($notificationInsertData['iono_id']);
                    $notificationInsertData['iono_type']    = $ionoType;
                    $notificationInsertData['iono_smde_id'] = $smdeId;

                    $ionoId = DB::connection('mysql2')->table('iot_notification')->insertGetId($notificationInsertData);

                    $notificationInsertData['iono_id'] = $ionoId;
                    $orderId                           = $device->smde_order_id;

                    if (empty($orderId)) {
                        return;
                    }

                    switch($ionoType) {
                        // 自检
                        case 0:
                            DB::connection('mysql2')->table('iot_notification_self_check')->insert($notificationInsertData);
                            // 防拆恢复
                            // 查找之前的防拆恢复告警，如果有，恢复之前的告警
                            DB::connection('mysql2')->table('iot_notification_pull_fix')
                                ->where('iono_imei', $imei)
                                ->where('iono_type', 15)
                                ->where('iono_status', '')
                                ->update([
                                    'iono_status'      => '已恢复',
                                    'iono_handle_time' => $notificationInsertData['iono_crt_time'],
                                ]);
                            break;
                        case 13:
                            DB::connection('mysql2')->table('iot_notification_self_check')->insert($notificationInsertData);
                            break;
                        case 15:// 防拆
                            $notificationInsertData['iono_status'] = '';
                            DB::connection('mysql2')->table('iot_notification_pull_fix')->insert($notificationInsertData);
                            break;
                        case 1:
                            // case 3: todo 温度报警张明说先不做
                            $notificationInsertData['iono_status'] = '待处理';
                            // 查找报警人电话
                            $phone = DB::connection('mysql2')
                                ->table('order')
                                ->where('order_id', $orderId)
                                ->value('order_user_mobile');
                            // 之前15秒内发送过报警，不发送报警电话和短信。
                            if (DB::connection('mysql2')->table('alert')->where('alert_smde_imei', $imei)
                                ->where('alert_type', 'voice')
                                ->where('alert_mobile', $phone)
                                ->where('alert_crt_time', '>', date("Y-m-d H:i:s", strtotime('-15 seconds')))
                                ->exists()) {
                                //不发报警
                                $notificationInsertData['iono_remark'] = '之前15秒内发送过报警，不发送报警电话和短信。';
                            } else {
                                // 正常发报警；
                                $url = $notificationInsertData['iono_status'] == '待处理' ? 'https://pingansuiyue.crzfxjzn.com/async.php?oper=send_alert&iono_id=' . $ionoId : '';
                            }
                            DB::connection('mysql2')->table('iot_notification_alert')->insert($notificationInsertData);
                            break;
                        case 101:
                        case 102:
                            $timestamp = $notificationInsertData['iono_msg_at'];

                            $currentTime = Carbon::createFromTimestamp($timestamp)->format('H:i');

                            // 定义时间范围
                            $timeRanges = [
                                ['start' => '06:00', 'end' => '09:00'],
                                // ['start' => '09:30', 'end' => '10:45'], // todo 待删，测试用
                                ['start' => '11:00', 'end' => '14:00'],
                                ['start' => '17:00', 'end' => '20:00'],
                            ];

                            // 检查当前时间是否在某个时间范围内
                            foreach ($timeRanges as $range) {
                                if ($currentTime >= $range['start'] && $currentTime <= $range['end']) {
                                    $currentStart = date('Y-m-d ') . $range['start'] . ':00';
                                    $currentEnd   = date('Y-m-d ') . $range['end'] . ':00';
                                }
                            }
                            if (isset($currentStart, $currentEnd)) {
                                // 查找出iot_notification_alert表是否有iono_type为101的报警，在当前时间范围内
                                // 如果检测到有人，整个时间段停止检测
                                $infraredRecord = DB::connection('mysql2')->table('iot_notification_alert')
                                    ->where('iono_type', 101)
                                    ->where('iono_smde_id', $smdeId)
                                    ->where('iono_msg_at', '>=', strtotime($currentStart))
                                    ->where('iono_msg_at', '<=', strtotime($currentEnd))
                                    ->exists();
                                // 不存在才插入
                                if (!$infraredRecord) {
                                    $notificationInsertData['iono_status'] = ''; // todo 测试时暂时留空
                                    DB::connection('mysql2')->table('iot_notification_alert')->insert($notificationInsertData);
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            });
            // 发送告警电话和短信
            if (!empty($url)) {
                Http::withOptions(['verify' => false])->get($url);
            }
        } catch (Exception $e) {
            // 在异常情况下报错
            Log::info('海曼4g 移动 insert failed:' . $e->getLine() . ':' . $e->getMessage());
        }
    }
}
