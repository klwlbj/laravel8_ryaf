<?php

namespace App\Http\Controllers;

use App\Utils\OneNet;
use Illuminate\Http\JsonResponse;
use App\Models\DeviceCacheCommands;
use Illuminate\Support\Facades\Log;
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
    public function getAndSendDeviceCacheCMD($imei, string $msgId = '')
    {
        DeviceCacheCommands::query()
            ->where('imei', $imei)
            ->where('is_success', 0)
            ->get()
            ->each(function ($item) use ($msgId) {
                sleep(1);
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
}
