<?php

namespace App\Http\Controllers;

use ZipStream\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Utils\Apis\Aep_device_command;
use Illuminate\Contracts\Foundation\Application;

class HaimanController extends BaseController
{
    public const CONVERT_TYPE_SPECIAL_CODE = 1;
    public const CONVERT_TYPE_ASCII        = 2;
    public const CONVERT_TYPE_NUMBER       = 3;

    public const CONVERT_TYPE_ENUM     = 4;
    public const CONVERT_TYPE_ORIGINAL = 5;

    public const ONENET_4G_PRODUCT_ID       = 'E2dMYR85jh';
    public const ONENET_INFRARED_PRODUCT_ID = 'O9nyomBJ89';

    public const CTWING_INFRARED_PRODUCT_ID = '17189257';

    public array $struct = [
        '1f46' => ['name' => 'signal', 'convertType' => self::CONVERT_TYPE_NUMBER], // 信号
        '1f49' => ['name' => 'IMEI', 'convertType' => self::CONVERT_TYPE_SPECIAL_CODE],
        '1f4a' => ['name' => 'IMSI', 'convertType' => self::CONVERT_TYPE_SPECIAL_CODE],
        '1f4f' => ['name' => 'ICCID', 'convertType' => self::CONVERT_TYPE_SPECIAL_CODE],
        '1f41' => ['name' => 'smoke_value', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 100], // 烟雾浓度
        '1f42' => ['name' => 'temp', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 100], // 温度
        '1f43' => ['name' => 'humidness', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 100], // 湿度
        '1f45' => ['name' => 'battery_value', 'convertType' => self::CONVERT_TYPE_NUMBER], // NB板电量
        '1f4b' => ['name' => 'longitude', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 10000000], // 经度
        '1f4c' => ['name' => 'latitude', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 10000000], // 纬度
        '1f47' => ['name' => 'alarm_status', 'convertType' => self::CONVERT_TYPE_ENUM], // 报警状态疑似和故障状态合并 todo
        '1f48' => ['name' => 'falut_status', 'convertType' => self::CONVERT_TYPE_ENUM], // 故障状态，无用
        '1f4d' => ['name' => 'maze_pollution', 'convertType' => self::CONVERT_TYPE_NUMBER], // 迷宫污染度
        '1f4e' => ['name' => 'NB-MCU_version', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 10], // NB-MCU版本
        '1f51' => ['name' => 'NB_module_version', 'convertType' => self::CONVERT_TYPE_ASCII], // NB模块版本号
        '1f52' => ['name' => 'RSRP', 'convertType' => self::CONVERT_TYPE_NUMBER],
        '1f53' => ['name' => 'RSRQ', 'convertType' => self::CONVERT_TYPE_NUMBER],
        '1f54' => ['name' => 'SNR', 'convertType' => self::CONVERT_TYPE_NUMBER],
        '1f55' => ['name' => 'smoke_threshold', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 100], // 烟雾阈值
        '1f56' => ['name' => 'temp_limit', 'convertType' => self::CONVERT_TYPE_NUMBER, "multiple" => 100], // 温度阈值
        '1f59' => ['name' => 'battery_percentL', 'convertType' => self::CONVERT_TYPE_NUMBER], // 电量阈值
        '1f5a' => ['name' => 'heartbeat_cycle', 'convertType' => self::CONVERT_TYPE_NUMBER], // 心跳时间
        '1f5c' => ['name' => 'CELLID', 'convertType' => self::CONVERT_TYPE_ASCII],
        '1f5d' => ['name' => 'infrared_sensitivity', 'convertType' => self::CONVERT_TYPE_NUMBER], // 红外灵敏度
        '1f5e' => ['name' => 'unmanned_time', 'convertType' => self::CONVERT_TYPE_ORIGINAL, 'multiple' => 100], // 上报无人时间,，单位：小时，数值放大100倍处理，即精度为0.01，例如，0x003C代表上报无人时间0.6小时，0x0320代表上报无人时间8小时
        '1f5f' => ['name' => 'deployment_time', 'convertType' => self::CONVERT_TYPE_ORIGINAL], // 前2个字节表示布防开始时间，后两个字节表示布防结束时间，两字节的布防时间中，高字节为小时数，低字节为分钟数，例如：0x141E0603表示20:30开始布防,06:03结束布防
        '1f65' => ['name' => 'deployment_mode', 'convertType' => self::CONVERT_TYPE_ORIGINAL], // 红外布防模式
        '1f66' => ['name' => 'sound_off', 'convertType' => self::CONVERT_TYPE_ORIGINAL], // 静音，1为静音，0为正常
    ];

    public array $alarmType = [
        0  => ['name' => '无报警', 'iono_type' => 0],
        1  => ['name' => '烟雾报警', 'iono_type' => 1],
        2  => ['name' => '烟雾报警解除', 'iono_type' => 2],
        3  => ['name' => '温度报警', 'iono_type' => 3],
        4  => ['name' => '温度报警解除', 'iono_type' => 4],
        5  => ['name' => '烟感低电量报警', 'iono_type' => 5],
        6  => ['name' => '烟感低电量报警解除', 'iono_type' => 6],
        7  => ['name' => 'NB低电量报警', 'iono_type' => 7],
        8  => ['name' => 'NB低电量报警解除', 'iono_type' => 8],
        9  => ['name' => '烟雾传感器故障', 'iono_type' => 9],
        10 => ['name' => '烟雾传感器故障解除', 'iono_type' => 10],
        11 => ['name' => '温湿度传感器故障', 'iono_type' => 11],
        12 => ['name' => '温湿度传感器故障解除', 'iono_type' => 12],
        13 => ['name' => '自检测试', 'iono_type' => 13],
        14 => ['name' => '自检测试完成', 'iono_type' => 14],
        15 => ['name' => '防拆触发', 'iono_type' => 15],
        16 => ['name' => '防拆恢复', 'iono_type' => 16],
        17 => ['name' => '烟雾板连接断开', 'iono_type' => 17],
        18 => ['name' => '烟雾板连接恢复', 'iono_type' => 18],
        19 => ['name' => '红外有人报警', 'iono_type' => 101], // 和张明总做的红外烟感类型冲突
        20 => ['name' => '红外无人报警', 'iono_type' => 102],
    ];

    public function mufflingByOneNet($imei)
    {
        // 拼接json数据
        $json = json_encode([
            "device_name" => $imei,
            "product_id"  => self::ONENET_4G_PRODUCT_ID, // 写死
            'identifier'  => 'set_mute',
            'params'      => [
                'mute' => 1,
            ],
        ]);

        return $this->insertDeviceCacheCMD($imei, $json);
    }

    // 4g用
    public function mufflingByCTWing($productId, $deviceId, $masterKey, $cmd = '1f560002157c')
    {
        #获取结果日志
        return Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'dataType' => 1,
                    "payload"  => $cmd,
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
    }

    // 红外用
    public function createCMDByCTWing($productId, $imei, $masterKey, $cmd)
    {
        // 从smoke_detector表中根据imei，获取smde_ctwing_device_id
        $deviceId = DB::connection('mysql2')
            ->table('smoke_detector')
            ->where('smde_imei', $imei)
            ->value('smde_ctwing_device_id');
        if (empty($deviceId)) {
            return [];
        }
        #获取结果日志
        return Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    'dataType' => 2, // 16进制字符串
                    "payload"  => $cmd,
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
    }

    public function hmOneNet4GWarmReturnJson(Request $request)
    {
        return $this->hmOneNet4GWarm($request, 1);
    }

    /**
     * 移动海曼烟感4G回调地址
     * @param Request $request
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hmOneNet4GWarm(Request $request, $isZhangMing = 0)
    {
        $data = $request->input();
        if (empty($data)) {
            return response()->json(['message' => 'Fail']);
        }
        // dd (json_decode(json_encode($data['msg']), true));

        $msg = config('app.debug') ? json_decode(json_encode($data['msg']), true) : json_decode($data['msg'], true);// 正式环境和测试环境数据格式不一样，测试时需要转换
        // dd($msg);
        $data['msg']  = $msg;
        $nonce        = $data['nonce'];
        $ionoPlatform = 'ONENET';

        Log::channel('haiman')->info("海曼移动4G msg:" . json_encode([
            'msg'   => $msg,
            'nonce' => $nonce,
            'time'  => $data['time'],
            'id'    => $data['id'],
        ]));

        $imei = $msg['deviceName'] ?? ($msg['dev_name'] ?? 0); // 设备imei
        if (!empty($imei) /*&& $type == 2*/) { // 心跳包时才下发 todo
            // 区分消息类型
            if (isset($msg['dev_name']) && $msg['type'] == 2) {
                // 在离线状态 不处理
            }
            if (isset($msg['notifyType']) && $msg['notifyType'] === 'event' && isset($msg['deviceName'])) {
                // 事件上报 不处理
            }
            if (isset($msg['notifyType']) && $msg['notifyType'] === 'property' && isset($msg['deviceName'], $msg['data']['params']) && count($msg['data']['params']) !== 5) {
                // 设备属性变更
                $time      = time();
                $productId = self::ONENET_4G_PRODUCT_ID; // 写死，海曼烟感产品id

                $alarmStatus                  = []; // 默认心跳包
                $heartbeatTime                = date("Y-m-d H:i:s.Y", (int) ($data['msg']['data']['params']['heartbeat_time']['time'] ?? microtime()) / 1000);
                $ionoMazePollution            = $data['msg']['data']['params']['MazePollution']['value'] ?? '';
                $ionoSmokeScope               = ($data['msg']['data']['params']['smoke_value']['value'] ?? 0);
                $ionoRsrp                     = $data['msg']['data']['params']['rsrp']['value'] ?? '';
                $ionoIMSI                     = $data['msg']['data']['params']['IMSI']['value'] ?? '';
                $ionoThresholdTemperature     = $data['msg']['data']['params']['tempLimit']['value'] ?? '';
                $ionoThresholdNbModuleBattery = $data['msg']['data']['params']['batteryPercentL']['value'] ?? '';
                $ionoTemperture               = $data['msg']['data']['params']['temp']['value'] ?? 0;
                $ionoIMEI                     = $data['msg']['data']['params']['IMEI']['value'] ?? '';
                $ionoRsrq                     = $data['msg']['data']['params']['rsrq']['value'] ?? '';
                $ionoSnr                      = $data['msg']['data']['params']['snr']['value'] ?? '';
                $ionoThresholdSmokeScope      = ($data['msg']['data']['params']['smoke_threshold']['value'] ?? 0);
                $ionoBattery                  = $data['msg']['data']['params']['battery_value']['value'] ?? '';
                $ionoICCID                    = $data['msg']['data']['params']['ICCID']['value'] ?? '';
                // 10进制转2进制代码
                $ionoAlarmSta     = decbin((int) ($data['msg']['data']['params']['alarmSta']['value'] ?? 0));
                $ionoAlarmStaList = [
                    // ['comment', 'iono_type'],
                    ['保留', 0],
                    ['自检', 13, 14],
                    ['烟雾告警', 1, 2],
                    ['高温告警', 3, 4],
                    ['防拆告警', 15, 16],
                    ['低压', 7],
                ];

                // ionoAlarmSta 反转后，对比$ionoAlarmStaList，根据bit生成对应状态
                $ionoAlarmSta = strrev($ionoAlarmSta);
                // 示例$ionoAlarmSta = '001'; 反转后变成'100'
                if ((int) $ionoAlarmSta == '0') {
                    $alarmStatus = [0];
                } else {
                    // 把字符串转换为数组
                    $ionoAlarmSta = str_split($ionoAlarmSta);
                    foreach ($ionoAlarmSta as $key => $value) {
                        if ($value == 1) {
                            $alarmStatus[] = $ionoAlarmStaList[$key][1];// 暂时保留
                            break;
                        }
                    }
                }

                $insertWarm = $this->insertWarm($heartbeatTime, $ionoMazePollution, $ionoSmokeScope, $ionoRsrp, $ionoTemperture, $ionoIMSI, $ionoThresholdTemperature, $ionoThresholdSmokeScope, $ionoBattery, $ionoICCID, $ionoPlatform, $data, $time, $ionoIMEI, $ionoThresholdNbModuleBattery, $imei, $ionoRsrq, $ionoSnr, $productId, $alarmStatus, '', '', $isZhangMing);
                if ($insertWarm && $isZhangMing) {
                    return response()->json(['message' => 'Success', 'data' => $insertWarm]);
                }
            }
            // 从命令缓存表中，获取命令，马上下发
            $this->getAndSendDeviceCacheCMD($imei, $data['id'] ?? '', 3);
        }

        return response()->json(['message' => 'Success']);
    }

    public function insertSmokeDetector(string $imei)
    {
        // return;
        $smde_type       = "烟感";
        $smde_brand_name = "海曼";
        $smde_model_name = "HM-608PH-NB红外";
        $smde_model_tag  = "";
        $smde_part_id    = 1; // 如约自己的设备
        return DB::connection('mysql2')->table('smoke_detector')->insert([
            "smde_type"       => $smde_type,
            "smde_brand_name" => $smde_brand_name,
            "smde_model_name" => $smde_model_name,
            "smde_imei"       => $imei,
            "smde_model_tag"  => $smde_model_tag,
            "smde_part_id"    => $smde_part_id,
        ]);
    }

    /**
     * 电信海曼烟感红外NB回调地址
     * @param Request $request
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function hmCTWingInfraredWarm(Request $request)
    {
        $jsonData = $request->all();
        if (isset($jsonData['payload']['APPdata'])) {
            $imei     = $jsonData['IMEI']; // 设备imei
            $deviceId = $jsonData['deviceId'] ?? '';

            // 解码 Base64 字符串为二进制数据
            $base64DecodedData = base64_decode($jsonData['payload']['APPdata']);

            // 将二进制数据转换为十六进制
            $ionoMsgValueHex = bin2hex($base64DecodedData);
            // $ionoMsgValueHex = base64ToHex($jsonData['payload']['APPdata']);
            $decodedMsg                     = $this->decode($ionoMsgValueHex);
            $jsonData['payload']['APPdata'] = $decodedMsg;

            $this->insertInfraredWarm($decodedMsg, $jsonData, $ionoMsgValueHex, $imei, 2, $deviceId);
        }
        Log::channel('haiman')->info("海曼电信红外:" . json_encode($jsonData));
        return response('', 200);
    }

    public function hmOneNetInfraredWarm(Request $request)
    {
        $data        = $request->input();
        $msg         = json_decode($data['msg'], true);
        $data['msg'] = $msg;
        $nonce       = $data['nonce'];

        if (isset($msg['value'])) {
            try {
                $imei            = $msg['deviceName'] ?? ($msg['dev_name'] ?? 0); // 设备imei
                $ionoMsgValueHex = $msg['value'];
                $decodedMsg      = $this->decode($msg['value']);
                $msg['value']    = $decodedMsg;
                $data['msg']     = $msg;

                $this->insertInfraredWarm($decodedMsg, $data, $ionoMsgValueHex, $imei);
            } catch (Exception $exception) {
                Log::info('hmOneNetInfraredWarm error:' . $exception->getMessage());
            }
        }

        Log::channel('haiman')->info("海曼红外移动4G msg:" . json_encode([
            'msg'   => $msg,
            'nonce' => $nonce,
            'time'  => $data['time'],
            'id'    => $data['id'],
        ]));

        return response()->json(['message' => 'Success']);
    }

    public function insertInfraredWarm(array $decodedMsg, array $data, string $ionoMsgValueHex, string $imei, $platform = 1, $deviceId = '')
    {
        // 设备属性变更
        $time      = time();
        $productId = $platform === 1 ? self::ONENET_INFRARED_PRODUCT_ID : self::CTWING_INFRARED_PRODUCT_ID; // 写死，海曼红外烟感产品id，移动和电信不一样

        $alarmStatus                  = []; // 默认心跳包
        $heartbeatTime                = date("Y-m-d H:i:s.Y", (int) ($data['time'] ?? microtime()) / 1000);
        $ionoMazePollution            = $decodedMsg['maze_pollution'] ?? '';
        $ionoSmokeScope               = ($decodedMsg['smoke_value'] ?? 0) * 100;
        $ionoRsrp                     = $decodedMsg['RSRP'] ?? '';
        $ionoIMSI                     = $decodedMsg['IMSI'] ?? '';
        $ionoThresholdTemperature     = $decodedMsg['temp_limit'] ?? '';
        $ionoThresholdNbModuleBattery = $msg['msg']['data']['params']['battery_percentL'] ?? '';
        $ionoTemperture               = $decodedMsg['temp'] ?? 0;
        $ionoIMEI                     = $decodedMsg['IMEI'] ?? '';
        $ionoRsrq                     = $decodedMsg['RSRQ'] ?? '';
        $ionoSnr                      = $decodedMsg['SNR'] ?? '';
        $ionoThresholdSmokeScope      = ($decodedMsg['smoke_threshold'] ?? 0) * 100;
        $ionoBattery                  = $decodedMsg['battery_value'] ?? '';
        $ionoICCID                    = $decodedMsg['ICCID'] ?? '';
        $ionoPlatform                 = $platform === 1 ? 'ONENET' : 'CTWING_AEP';

        if (isset($decodedMsg['alarm_status'])) {
            $alarmStatus[] = $decodedMsg['alarm_status']['iono_type'] ?? 0;
        }
        $this->insertWarm($heartbeatTime, $ionoMazePollution, $ionoSmokeScope, $ionoRsrp, $ionoTemperture, $ionoIMSI, $ionoThresholdTemperature, $ionoThresholdSmokeScope, $ionoBattery, $ionoICCID, $ionoPlatform, $data, $time, $ionoIMEI, $ionoThresholdNbModuleBattery, $imei, $ionoRsrq, $ionoSnr, $productId, $alarmStatus, $ionoMsgValueHex, $deviceId);
    }

    /**
     * 海曼透传数据解码
     * @param string $code
     * @return array
     */
    public function decode(string $code): array
    {
        $data = [];
        while ($code) {
            // 截取$code的前4位
            $type = substr($code, 0, 4);
            // 截取后舍去
            $code    = substr($code, 4);
            $length  = (int) hexdec(substr($code, 0, 4)) * 2; // 字符串长度
            $trueStr = substr($code, 4, $length); // 真实字符串
            // 判断是否是需要解码的字段
            // 转小写
            $type = strtolower($type);
            if (isset($this->struct[$type])) {
                $item = $this->struct[$type];
                // 解码
                switch ($item['convertType']) {
                    case self::CONVERT_TYPE_SPECIAL_CODE:
                        $data[$item['name']] = $this->convertSpecialCode($trueStr);
                        break;
                    case self::CONVERT_TYPE_ASCII:
                        $data[$item['name']] = $this->convertHexToASCII($trueStr);
                        break;
                    case self::CONVERT_TYPE_NUMBER:
                        $data[$item['name']] = $this->convertHexToNumber($trueStr) / ($item['multiple'] ?? 1);
                        break;
                    case self::CONVERT_TYPE_ENUM:
                        $data[$item['name']] = $this->convertHexToEnum($trueStr);
                        break;
                    case self::CONVERT_TYPE_ORIGINAL:
                    default:
                        $data[$item['name']] = $trueStr;
                        break;
                }
            } else {
                $data[$type] = $trueStr;
            }
            $code = substr($code, 4 + $length);
        }
        return $data;
    }

    // 解码
    public function convertSpecialCode($code)
    {
        // $code = "383634313630303631363134393238";
        // 转成864160061614928
        // 每2位16进制数转成10进制数
        $code    = str_split($code, 2);
        $newCode = array_map(function ($item) {
            return substr($item, -1);
        }, $code);
        return  implode($newCode);
    }

    public function convertHexToASCII($hex)
    {
        // $hex = '44354437463446';
        // 转成864160061614928
        // 每2位16进制数转成10进制数
        $hex     = str_split($hex, 2);
        $newCode = array_map(function ($item) {
            // 字符串保留最后一位
            return chr(hexdec($item));
        }, $hex);
        return  implode($newCode);
    }

    public function convertHexToNumber($hex)
    {
        // $hex = 'ff9e';
        // 先去除 0x 前缀，如果有的话
        $hex = ltrim($hex, '0x');

        // 判断是否是负数的补码表示（16进制数）
        if (strlen($hex) > 0 && strlen($hex) % 2 === 0) {
            // 如果是负数（补码表示），就判断首位是否是 F
            $firstChar = substr($hex, 0, 2);
            if ($firstChar === 'FF' || $firstChar === 'ff') {
                // 转换为负数
                // 转成2进制后取反+1
                $hex = substr($hex, 2); // 去掉 FF
                // 2进制
                $binary = base_convert($hex, 16, 2);
                // 取反
                $binary = str_replace(['0', '1'], ['2', '3'], $binary);
                $binary = str_replace(['2', '3'], ['1', '0'], $binary);
                // 加1
                $binary = decbin(bindec($binary) + 1);
                // 转回10进制
                $decimal = bindec($binary);
                return -$decimal;
            }
            // 正常转换
            return hexdec($hex);
        }

        // 正常转换
        return hexdec($hex);
    }

    public function convertHexToEnum($hex)
    {
        // $hex = '0013';
        // 16进制转10进制
        $decimal = hexdec($hex);
        return $this->alarmType[$decimal] ?? '';
    }

    /**
     * @param $heartbeatTime
     * @param $ionoMazePollution
     * @param $ionoSmokeScope
     * @param $ionoRsrp
     * @param $ionoTemperture
     * @param $ionoIMSI
     * @param $ionoThresholdTemperature
     * @param $ionoThresholdSmokeScope
     * @param $ionoBattery
     * @param $ionoICCID
     * @param $ionoPlatform
     * @param $data
     * @param int $time
     * @param $ionoIMEI
     * @param $ionoThresholdNbModuleBattery
     * @param $imei
     * @param $ionoRsrq
     * @param $ionoSnr
     * @param string $productId
     * @param array $alarmStatus
     * @param string $ionoMsgValueHex
     * @param string $deviceId
     */
    private function insertWarm($heartbeatTime, $ionoMazePollution, $ionoSmokeScope, $ionoRsrp, $ionoTemperture, $ionoIMSI, $ionoThresholdTemperature, $ionoThresholdSmokeScope, $ionoBattery, $ionoICCID, $ionoPlatform, $data, int $time, $ionoIMEI, $ionoThresholdNbModuleBattery, $imei, $ionoRsrq, $ionoSnr, string $productId, array $alarmStatus, string $ionoMsgValueHex = '', string $deviceId = '', $isZhangMing = 0)
    {
        $deviceUpdateData = [
            'smde_last_heart_beat'           => $heartbeatTime,
            'smde_last_maze_pollution'       => $ionoMazePollution,
            'smde_last_smokescope'           => $ionoSmokeScope,
            'smde_last_signal_intensity'     => $ionoRsrp,
            'smde_last_temperature'          => (int) $ionoTemperture * 100,
            'smde_nb_iid'                    => $ionoIMSI,
            'smde_threshold_temperature'     => (int) $ionoThresholdTemperature * 100,
            'smde_threshold_smoke_scope'     => $ionoThresholdSmokeScope,
            'smde_last_smoke_module_battery' => $ionoBattery,
            'smde_last_nb_module_battery'    => $ionoBattery,
            'smde_nb_iid2'                   => $ionoICCID,
            'smde_online'                    => 1,
            'smde_online_real'               => 1,
            'smde_iot_platform'              => $ionoPlatform,
            'smde_ctwing_device_id'          => $deviceId, // 针对电信平台
        ];
        $notificationInsertData = [
            'iono_platform'                    => $ionoPlatform,
            'iono_body'                        => json_encode($data),
            'iono_timestamp'                   => $time,
            'iono_msg_at'                      => $data['time'] ?? $time,
            'iono_msg_imei'                    => $ionoIMEI ?? '',
            'iono_msg_type'                    => 1,
            'iono_nonce'                       => $data['nonce'] ?? '',
            'iono_threshold_smoke_scope'       => $ionoThresholdSmokeScope ?? '',
            'iono_threshold_temperature'       => $ionoThresholdTemperature ?? '',
            'iono_threshold_nb_module_battery' => $ionoThresholdNbModuleBattery ?? '',
            'iono_smoke_scope'                 => $ionoSmokeScope ?? '',
            'iono_temperature'                 => $ionoTemperture * 100 ?? '',
            'iono_smoke_module_battery'        => $ionoBattery ?? '',
            'iono_nb_module_battery'           => $ionoBattery ?? '',
            // 'iono_type' => $ionoType ?? '',
            'iono_imei'                        => $imei,
            'iono_imsi'                        => $ionoIMSI ?? '',
            'iono_maze_pollution'              => $ionoMazePollution ?? '',
            'iono_nb_iccid'                    => $ionoICCID ?? '',
            'iono_rsrp'                        => $ionoRsrp ?? '',
            'iono_rsrq'                        => $ionoRsrq ?? '',
            'iono_snr'                         => $ionoSnr ?? '',
            'iono_category'                    => '烟感',
            'iono_status'                      => config('alarm_setting.other_alarm.status'),
            // 'iono_smde_id' => $smdeId,
            'iono_crt_time'                    => date("Y-m-d H:i:s.u"), // like 2025-01-09 16:38:45.098261
            'iono_alert_status'                => -1,
            'iono_device_status'               => -1,
            'iono_product_id'                  => $productId,
            'iono_msg_value_hex'               => $ionoMsgValueHex,
            // 'iono_type_list'                   => $alarmStatus,
        ];
        if ($isZhangMing) {
            $notificationInsertData['iono_type_list'] = $alarmStatus;
            return $notificationInsertData;
        }

        $this->insertIOT($deviceUpdateData, $notificationInsertData, $alarmStatus, $imei);
    }
}
