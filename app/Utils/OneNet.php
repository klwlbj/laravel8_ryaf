<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;

class OneNet extends BaseIoTClient
{
    public const HOST         = 'https://iot-api.heclouds.com/nb-iot';
    public const IMEI_ONE_NET = 861561056131278; // 海康烟感-移动

    public const OBJ_ID      = 3339;
    public const OBJ_INST_ID = 0;
    public const RES_ID      = 5522;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false, // 关闭 SSL 验证
        ]);
    }

    /**
     * 签名算法
     * @return string
     */
    public function getSign()
    {
        $userId             = env("ONE_NET_USERID");
        $accessKey          = env('ONE_NET_ACCESS_KEY');
        $time               = time() + 30;
        $method             = 'md5';
        $res                = 'userid/' . $userId;
        $version            = '2022-05-01';
        $stringForSignature = $time . "\n" . $method . "\n" . $res . "\n" . $version;

        $decodedAccessKey = base64_decode($accessKey);
        $hmac             = hash_hmac($method, utf8_encode($stringForSignature), $decodedAccessKey, true);
        $signature        = base64_encode($hmac);

        return 'version=' . $version . '&res=' . urlencode($res) . '&et=' . $time . '&method=' . $method . '&sign=' . urlencode($signature);
    }

    /**
     * 读命令
     * @return mixed|void
     */
    public function loadResource()
    {
        $time = time();
        try {
            $response = $this->client->request('GET', self::HOST . '/offline', [
                'query'   => [
                    "imei"         => self::IMEI_ONE_NET,
                    "obj_id"       => self::OBJ_ID,
                    'valid_time'   => date('Y-m-d', $time + 10) . 'T' . date('H:i:s', $time + 10),
                    'expired_time' => date('Y-m-d', $time + 100) . 'T' . date('H:i:s', $time + 1000),
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 直接写入实时命令（烟感用这个）
     * @param $args
     * @return mixed|void
     */
    public function writeResource($command = 'longSilence', $dwPackageNo = '00000001')
    {
        $args = self::COMMAND[$command] ?? self::COMMAND['longSilence'];
        $time = time() + 1000;
        try {
            $response = $this->client->request('POST', self::HOST . '/offline', [
                'query'   => [
                    "imei"         => self::IMEI_ONE_NET,
                    "obj_id"       => self::OBJ_ID,
                    "obj_inst_id"  => self::OBJ_INST_ID,
                    'mode'         => 1, // 1：直接替换；2：局部更新
                    'expired_time' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time),
                ],
                'json'    => [
                    'data' => [
                        [
                            'res_id' => self::RES_ID,
                            'type'   => 1,
                            'val'    => $args[0] . $dwPackageNo . $args[1],
                        ],
                    ],
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 下发缓存命令(暂时不用)
     * @param $args
     * @return mixed|void
     */
    public function issueCacheCommand($args = '9000000192000c00000000060000ffff000C00023D')
    {
        $time = time() + 1000;
        try {
            $response = $this->client->request('POST', self::HOST . '/execute/offline', [
                'query'   => [
                    "imei"         => self::IMEI_ONE_NET,
                    "obj_id"       => self::OBJ_ID,
                    "obj_inst_id"  => self::OBJ_INST_ID,
                    'mode'         => '1', // 1：直接替换；2：局部更新
                    'expired_time' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time),
                    'res_id'       => self::RES_ID,
                ],
                'json'    => [
                    'args' => $args,
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 命令列表
     * @return mixed|void
     */
    public function cacheCommands()
    {
        try {
            $time     = time() - 3600; // 过去24小时
            $response = $this->client->request('GET', self::HOST . '/offline/history', [
                'query'   => [
                    "imei"  => self::IMEI_ONE_NET,
                    'start' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time),
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 命令详情
     * @param $uuid
     * @return mixed|void
     */
    public function cacheCommand($uuid)
    {
        try {
            $response = $this->client->request('GET', self::HOST . '/offline/history/' . $uuid, [
                'query'   => [
                    "imei" => self::IMEI_ONE_NET,
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 取消所有命令
     * @return mixed|void
     */
    public function cancelAllCacheCommand()
    {
        try {
            $response = $this->client->request('PUT', self::HOST . '/offline/cancel/all/', [
                'query'   => [
                    "imei" => self::IMEI_ONE_NET,
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 取消某条命令
     * @param $uuid
     * @return mixed|void
     */
    public function cancelCacheCommand($uuid)
    {
        try {
            $response = $this->client->request('PUT', self::HOST . '/offline/cancel/' . $uuid, [
                'query'   => [
                    "imei" => self::IMEI_ONE_NET,
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 查询日志
     * @param $uuid
     * @return mixed|void
     */
    public function logQuery($uuid)
    {
        try {
            $response = $this->client->request('GET', self::HOST . '/offline/history/' . $uuid . '/piecewise', [
                'query'   => [
                    "imei" => self::IMEI_ONE_NET,
                ],
                'headers' => ['authorization' => $this->getSign()],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }
}
