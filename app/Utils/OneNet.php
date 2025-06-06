<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;

class OneNet extends BaseIoTClient
{
    public const HOST         = 'https://iot-api.heclouds.com/nb-iot';
    public const IMEI_ONE_NET = 861561056131278; // 海康烟感-移动,暂不写死

    public $objId     = 3339;
    public $objInstId = 0;
    public $resId     = 5522;

    private Client $client;

    public function __construct($objId = null, $objInstId = null, $resId = null)
    {
        if ($objId !== null) {
            $this->objId = $objId;
        }
        if ($objInstId !== null) {
            $this->objInstId = $objInstId;
        }
        if ($resId !== null) {
            $this->resId = $resId;
        }
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
        $userId             = config('services.onenet.user_id');
        $accessKey          = config('services.onenet.access_key');
        $time               = time() + 30;
        $method             = 'md5';
        $res                = 'userid/' . $userId;
        $version            = '2022-05-01';
        $stringForSignature = $time . "\n" . $method . "\n" . $res . "\n" . $version;

        $decodedAccessKey = base64_decode($accessKey);
        $hmac             = hash_hmac($method, utf8_encode($stringForSignature), $decodedAccessKey, true);
        $signature        = base64_encode($hmac);
//        Tools::writeLog('sign：' . ('version=' . $version . '&res=' . urlencode($res) . '&et=' . $time . '&method=' . $method . '&sign=' . urlencode($signature)),'yuanliutest');
        return 'version=' . $version . '&res=' . urlencode($res) . '&et=' . $time . '&method=' . $method . '&sign=' . urlencode($signature);
    }

    /**
     * 读命令
     * @param $imei
     * @return mixed|void
     */
    public function loadResource($imei)
    {
        $time = time();
        try {
            $response = $this->client->request('GET', self::HOST . '/offline', [
                'query'   => [
                    "imei"         => $imei,
                    "obj_id"       => $this->objId,
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
     * 即时命令下发
     * @param $imei
     * @param string $command
     * @param string $dwPackageNo
     * @param string $cmd
     * @return mixed|void
     */
    public function execute($imei, string $command = self::LONG_SILENCE, string $dwPackageNo = '00000001', string $cmd = '')
    {
        $cmd = empty($cmd) ? $this->generateCommand($command, $dwPackageNo, false) : $cmd;
        try {
            $response = $this->client->request('POST', self::HOST . '/execute', [
                'query'   => [
                    "imei"        => $imei,
                    "obj_id"      => $this->objId,
                    "obj_inst_id" => $this->objInstId,
                    'res_id'      => $this->resId,
                ],
                'json'    => [
                    'args' => $cmd,
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
     * 即时写设备资源
     * @param $imei
     * @param string $command
     * @param string $dwPackageNo
     * @param string $cmd
     * @return mixed|void
     */
    public function realTimewriteResource($imei, string $command = self::LONG_SILENCE, string $dwPackageNo = '00000001', string $cmd = '')
    {
        $cmd = empty($cmd) ? $this->generateCommand($command, $dwPackageNo, false) : $cmd;
        try {
            $response = $this->client->request('POST', self::HOST, [
                'query'   => [
                    "imei"        => $imei,
                    "obj_id"      => $this->objId,
                    "obj_inst_id" => $this->objInstId,
                    'mode'        => 1, // 1：直接替换；2：局部更新
                ],
                'json'    => [
                    'data' => [
                        [
                            'res_id' => $this->resId,
                            'type'   => 1,
                            'val'    => $cmd,
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

    public function callService($data = [])
    {
        try {
            $response = $this->client->request('POST', 'https://iot-api.heclouds.com/thingmodel/call-service', [
                'query'   => [
                    //                    'timeout'        => 30,
                ],
                'json'    => $data,
                'headers' => ['authorization' => $this->getSign()],
            ]);
            Log::info('消音json:' . json_encode($data));

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::info($e);
        }
    }

    /**
     * 缓存写设备资源（海康烟感用这个）
     * @param $imei
     * @param string $command
     * @param string $dwPackageNo
     * @param string $cmd
     * @return mixed|void
     */
    public function customWriteResource($imei, string $command = self::LONG_SILENCE, string $dwPackageNo = '00000001', string $cmd = '')
    {
        $cmd  = empty($cmd) ? $this->generateCommand($command, $dwPackageNo, false) : $cmd;
        $time = time() + 10000;
        try {
            $response = $this->client->request('POST', self::HOST . '/offline', [
                'query'   => [
                    "imei"         => $imei,
                    "obj_id"       => $this->objId,
                    "obj_inst_id"  => $this->objInstId,
                    'mode'         => 1, // 1：直接替换；2：局部更新
                    'expired_time' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time),
                ],
                'json'    => [
                    'data' => [
                        [
                            'res_id' => $this->resId,
                            'type'   => 1,
                            'val'    => $cmd,
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

    public function writeResource($imei, $cmd, $objId = 8000, $objInstId = 0)
    {
        $time = time() + 3600;

        $response = $this->client->request('POST', self::HOST . '/offline', [
            'query'   => [
                "imei"         => $imei,
                "obj_id"       => $objId,
                "obj_inst_id"  => $objInstId,
                'mode'         => 2, // 1：直接替换；2：局部更新
                'expired_time' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time),
                'trigger_msg' => 7,
            ],
            'json'    => [
                'data' => [
                    [
                        'res_id' => 9001,
                        // 'type'   => 1,
                        'val'    => $cmd,
                    ],
                ],
            ],
            'headers' => ['authorization' => $this->getSign()],
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return $response;
    }

    public function createGasSettingCommand($imei, int $gasAlarmCorrection = 0)
    {
        $cmd = $this->generateCommand(self::GAS, '', true, ['gasAlarmCorrection' => $gasAlarmCorrection]);
        return $this->realTimewriteResource($imei, self::GAS, '', $cmd);
    }

    /**
     * 下发缓存命令(暂时燃气检测设备用)
     * @param $imei
     * @param string $command
     * @param string $dwPackageNo
     * @param string $cmd
     * @return mixed|void
     */
    public function issueCacheCommand($imei, string $command = self::LONG_SILENCE, string $dwPackageNo = '00000001', string $cmd = '')
    {
        $cmd  = empty($cmd) ? $this->generateCommand($command, $dwPackageNo, false) : $cmd;
        $time = time() + 1000;
        try {
            $response = $this->client->request('POST', self::HOST . '/execute/offline', [
                'query'   => [
                    "imei"         => $imei,
                    "obj_id"       => $this->objId,
                    "obj_inst_id"  => $this->objInstId,
                    'mode'         => '1', // 1：直接替换；2：局部更新
                    'expired_time' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time),
                    'res_id'       => $this->resId,
                ],
                'json'    => [
                    'args' => $cmd,
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
    public function cacheCommands($imei)
    {
        try {
            $time     = time() - 3600; // 过去24小时
            $response = $this->client->request('GET', self::HOST . '/offline/history', [
                'query'   => [
                    "imei"  => $imei,
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
     * @param $imei
     * @param $uuid
     * @return mixed|void
     */
    public function cacheCommand($imei, $uuid)
    {
        try {
            $response = $this->client->request('GET', self::HOST . '/offline/history/' . $uuid, [
                'query'   => [
                    "imei" => $imei,
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
     * @param $imei
     * @return mixed|void
     */
    public function cancelAllCacheCommand($imei)
    {
        try {
            $response = $this->client->request('PUT', self::HOST . '/offline/cancel/all/', [
                'query'   => [
                    "imei" => $imei,
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
     * @param string $imei
     * @param string $uuid
     * @return mixed|void
     */
    public function cancelCacheCommand(string $imei, string $uuid)
    {
        try {
            $response = $this->client->request('PUT', self::HOST . '/offline/cancel/' . $uuid, [
                'query'   => [
                    "imei" => $imei,
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
     * @param string $imei
     * @param string $uuid
     * @return mixed|void
     */
    public function logQuery(string $imei, string $uuid)
    {
        try {
            $response = $this->client->request('GET', self::HOST . '/offline/history/' . $uuid . '/piecewise', [
                'query'   => [
                    "imei" => $imei,
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

    public function deviceInfo($projectId, $imei)
    {
        try {
            $response = $this->client->request('GET', 'https://iot-api.heclouds.com/device/detail', [
                'query'   => [
                    "product_id"  => $projectId,
                    "device_name" => $imei,
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

    // NB设备专用
    public function deviceInfoNB($imei)
    {
        try {
            $response = $this->client->request('GET', 'https://iot-api.heclouds.com/device/detail', [
                'query'   => [
                    'product_id' => 0,
                    "imei"       => $imei,
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
