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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class BaseController extends \Illuminate\Routing\Controller
{
    public function migrationTest()
    {
        return Artisan::call("config:cache");
    }

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
     * @param int $sleepSecond
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
        $maxRetries = 3; // 最大重试次数
        $delay      = 200; // 每次重试之间的延迟（毫秒）

        // 定义一个重试机制
        $attempt = 0;
        $success = false;

        if ($imei == '865665053801837') {
            Log::info('雷达报警：' . json_encode([$deviceUpdateData, $notificationInsertData, $alarmStatus]));
        }
        while ($attempt < $maxRetries && !$success) {
            $url         = '';
            $ionoAlertId = null;
            try {
                // laravel事务代码
                DB::connection('mysql2')->transaction(function () use ($deviceUpdateData, $notificationInsertData, $alarmStatus, $imei, &$url, &$ionoAlertId) {
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
                                // 防拆恢复（针对物模型）
                                $this->insertPullFixFinished($imei, $notificationInsertData);
                                break;
                            case 13:
                                DB::connection('mysql2')->table('iot_notification_self_check')->insert($notificationInsertData);
                                break;
                            case 15:// 防拆
                            case 16:// 防拆恢复(针对透传)
                                $ionoType == 16 ? $this->insertPullFixFinished($imei, $notificationInsertData) : null;
                                $notificationInsertData['iono_status'] = '';
                                DB::connection('mysql2')->table('iot_notification_pull_fix')->insert($notificationInsertData);
                                break;
                            case 1:
                            case 3: // 温度报警
                                $notificationInsertData['iono_status'] = config('alarm_setting.pending_alarm.status');
                                // 查找报警人电话
                                $phone = DB::connection('mysql2')
                                    ->table('order')
                                    ->where('order_id', $orderId)
                                    ->value('order_user_mobile');
                                if ($device->smde_alert_ignore_until > date("Y-m-d H:i:s")) {
                                    $notificationInsertData['iono_status'] = '已忽略';
                                    $notificationInsertData['iono_remark'] = "根据设备设置的忽略报警时间段自动忽略";
                                } else {
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
                                        $url = 'https://pingansuiyue.crzfxjzn.com/async.php?oper=send_alert&iono_id=' . $ionoId;
                                    }
                                }

                                DB::connection('mysql2')->table('iot_notification_alert')->insert($notificationInsertData);
                                $ionoAlertId = $ionoId;
                                break;
                            case 101:// 红外
                            case 102:
                                $timestamp   = $notificationInsertData['iono_msg_at'];
                                $currentTime = Carbon::createFromTimestamp($timestamp)->format('H:i');

                                // 定义时间范围
                                $timeRanges = [
                                    ['start' => '06:00', 'end' => '09:00'],
                                    ['start' => '09:30', 'end' => '10:45'], // todo 待删，测试用
                                    ['start' => '11:00', 'end' => '14:00'],
                                    ['start' => '14:10', 'end' => '16:45'], // todo 待删，测试用
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
                                    $infraredRecord = DB::connection('mysql2')->table('iot_notification_infrared_appear')
                                        ->where('iono_type', 101)
                                        ->where('iono_smde_id', $smdeId)
                                        ->where('iono_msg_at', '>=', strtotime($currentStart))
                                        ->where('iono_msg_at', '<=', strtotime($currentEnd))
                                        ->exists();
                                    // 不存在才插入
                                    if (!$infraredRecord) {
                                        $notificationInsertData['iono_status'] = ''; // todo 测试时暂时留空
                                        DB::connection('mysql2')->table('iot_notification_infrared_appear')->insert($notificationInsertData);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    }
                });
                // 如果到这里没有异常，说明事务提交成功
                $success = true;
                // 发送告警电话和短信
                if (!empty($url)) {
                    // 推送数据到其他 URL
                    Http::withOptions(['verify' => false])->get($url);
                }
                if (isset($ionoAlertId)) {
                    // 推送区平台
                    $yunchuang = Http::withOptions([
                        'timeout' => 1,
                        'verify'  => false,
                    ])->get('https://pingansuiyue2.crzfxjzn.com/api/yunChuang/pushAlert/' . $ionoAlertId . '/' . $imei);
                    Log::info("区平台返回{$imei}.{$ionoAlertId}" . json_encode($yunchuang->json()));
                }
            } catch (Exception $e) {
                // 在异常情况下报错
                Log::info('海曼4g 移动 insert failed:' . $e->getLine() . ':' . $e->getMessage());
                $attempt++;

                // 如果超过最大重试次数，则抛出异常
                if ($attempt >= $maxRetries) {
                    throw new \Exception('操作失败，已超过最大重试次数');
                }

            // 延迟一段时间再重试
                usleep($delay * 1000); // usleep接受的是微秒，乘以1000转换为毫秒
            } catch (\Throwable $e) {
            }
        }
    }

    public function insertPullFixFinished($imei, $notificationInsertData)
    {
        // 查找之前的防拆恢复告警，如果有，恢复之前的告警
        DB::connection('mysql2')->table('iot_notification_pull_fix')
            ->where('iono_imei', $imei)
            ->where('iono_type', 15)
            ->where('iono_status', '')
            ->update([
                'iono_status'      => '已恢复',
                'iono_handle_time' => $notificationInsertData['iono_crt_time'],
            ]);
    }
}
