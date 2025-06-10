<?php

namespace App\Utils;

use App\Utils\Apis\Aep_device_event;
use App\Utils\Apis\Aep_device_command;
use App\Utils\Apis\Aep_subscribe_north;
use App\Utils\Apis\Aep_device_management;
use App\Utils\Apis\Aep_nb_device_management;
use App\Utils\Apis\Core\返回响应：bool;
use App\Utils\Apis\Aep_device_command_cancel;
use App\Utils\Apis\Aep_device_command_lwm_profile;

class CTWing extends BaseIoTClient
{
    // PRODUCT_ID
    // NB:16918237
    // 4G:16922937

    // DEVICE_ID
    // NB:6543c6a31a574400a02581d46351251a
    // 4G:99013914868558064938549

    // masterID
    // NB: a7131673e84842178d56a704511c40ef
    // 4G: 3d03a509d3f04947b9f0aeb33d34cc2c

    /**
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @return bool|null
     */
    public function queryDeviceEventList($productId, $deviceId, $masterKey)
    {
        $result = Aep_device_event::QueryDeviceEventList(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "productId" => $productId, //必填
                "deviceId"  => $deviceId, //必填
                "startTime" => strtotime('-30 day') * 1000, //必填,30天内
                "endTime"   => time() * 1000, //必填
                "pageSize"  => 10, //必填
            ])
        );
        return $result;
    }

    /**
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @return 返回响应：bool|null
     */
    public function queryDeviceEventTotal($productId, $deviceId, $masterKey)
    {
        $result = Aep_device_event::QueryDeviceEventTotal(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "productId" => $productId, //必填
                "deviceId"  => $deviceId, //必填
                "startTime" => strtotime('-30 day') * 1000, //必填,30天内
                "endTime"   => time() * 1000, //必填
            ])
        );
        return $result;
    }

    /**
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @return 返回响应：bool|null
     */
    public function QueryDevice($productId, $deviceId, $masterKey)
    {
        $result = Aep_device_management::QueryDevice(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            $deviceId,
            $productId
        );
        return $result;
    }

    /**
     * 下发命令（4G卡用）海康
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $command
     * @param string $dwPackageNo
     * @return 返回响应：bool|null
     */
    public function createCommand($productId, $deviceId, $masterKey, string $command = self::LONG_SILENCE, string $dwPackageNo = '00000006')
    {
        $cmd = $this->generateCommand($command, $dwPackageNo, true);

        $result = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    "payload" => [
                        "val" => $cmd,
                    ],
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
        return $result;
    }

    public function createCmdCommand($productId, $deviceId, $masterKey, $cmdType)
    {
        $result = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    "params"            => [
                        "alarm_mode" => $cmdType, // 0解除报警,1火警,2紧急情况报警
                    ],
                    'serviceIdentifier' => "alarm_cmd",
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
        return $result;
    }

    /**
     * 非透传设备下发自定义命令
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $serviceIdentifier
     * @param $params
     * @return 返回响应：bool|null
     */
    public function createCustomCommand($productId, $deviceId, $masterKey, string $serviceIdentifier, $params = [])
    {
        $result = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    "params"            => $params,
                    'serviceIdentifier' => $serviceIdentifier,
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                "ttl"           => 7200,
            ])
        );
        return $result;
    }

    /**
     * 非透传设备消声
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param int $second
     * @return 返回响应：bool|null
     */
    public function createNTTMufflingCommand($productId, $deviceId, $masterKey, int $second = 120)
    {
        $result = Aep_device_command::CreateCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "content"   => [
                    "params"            => [
                        "muffling" => $second,
                    ],
                    'serviceIdentifier' => "cmd",
                ],
                "deviceId"  => $deviceId,
                "operator"  => "ryaf", // 操作者，暂时写死
                "productId" => $productId,
                // "ttl"           => 7200,
            ])
        );
        return $result;
    }

    /**
     * lwm2m协议有profile指令下发接口
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $command
     * @param string $dwPackageNo
     * @param string $cmd
     * @return bool|null
     */
    public function createCommandLwm2mProfile($productId, $deviceId, $masterKey, string $command = self::LONG_SILENCE, string $dwPackageNo = '00000001', string $cmd = '')
    {
        $cmd = empty($cmd) ? $this->generateCommand($command, $dwPackageNo, false) : $cmd;

        return Aep_device_command_lwm_profile::CreateCommandLwm2mProfile(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            json_encode([
                "command"   => [
                    "serviceId" => "Msg",
                    "method"    => "CMD",
                    "paras"     => [
                        "val" => $cmd,
                    ],
                ],
                "deviceId"  => $deviceId,
                "operator"  => "klwlbj",
                "productId" => $productId,
            ]),
            $masterKey
        );
    }

    /**
     * lwm2m协议有profile指令下发接口
     * @param $productId
     * @param $imei
     * @param $masterKey
     * @return bool|null
     */
    public function CreateCommandLwm2mProfileByIMEI($productId, $imei, $masterKey)
    {
        $deviceId = 'c6cf6f487cd24e8cbd3bed7daf0f4dbf';// 先写死$deviceId
        // todo 通过imei查询deviceId

        return Aep_device_command_lwm_profile::CreateCommandLwm2mProfile(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            json_encode([
                "command"   => [
                    "serviceId" => "Msg",
                    "method"    => "CMD",
                    "paras"     => [
                        // 'val'=> "1F5600021C84" // 写死命令 todo
                        "muffling" => 1, // 消声时间
                    ],
                ],
                "deviceId"  => $deviceId,
                "operator"  => "klwlbj",
                "productId" => $productId,
            ]),
            $masterKey
        );
    }

    /**
     * @param int $byWorkMode 工作模式，1火警，2违规住人，3养老
     * @param int $wSensitivity 微波探测灵敏度 0-1023
     * @param int $byCommunicationFrequency 通讯频率 0-255
     * @param int $byEnabled 微波检测使能，0关1开
     * @param int $byCycle 微波检测周期 60-3600
     * @param int $byTimes 微波检测次数 0-20
     * @param int $byWorkPeriod 微波检测工作时段个数
     * @param int $byStartTime 开始时间
     * @param int $byEndTime 关闭时间
     * @return bool|null
     */
    public function createMicrowaveSettingCommand($productId, $deviceId, $masterKey, int $byWorkMode = 2, int $wSensitivity = 0, int $byCommunicationFrequency = 0, int $byEnabled = 1, int $byCycle = 20, int $byTimes = 20, int $byWorkPeriod = 1, int $byStartTime = 0, int $byEndTime = 23)
    {
        $params = compact('byWorkMode', 'wSensitivity', 'byCommunicationFrequency', 'byEnabled', 'byCycle', 'byTimes', 'byWorkPeriod', 'byStartTime', 'byEndTime');
        $cmd    = $this->generateCommand(self::MICROWAVE_SETTING, '', false, $params);

        return $this->createCommandLwm2mProfile($productId, $deviceId, $masterKey, self::MICROWAVE_SETTING, '', $cmd);
    }

    public function createGasSettingCommand($productId, $deviceId, $masterKey, int $gasAlarmCorrection = 0)
    {
        $cmd = $this->generateCommand(self::GAS, '', true, ['gasAlarmCorrection' => $gasAlarmCorrection]);
        return $this->createCommandLwm2mProfile($productId, $deviceId, $masterKey, self::GAS, '', $cmd);
    }

    /**
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @return 返回响应：bool|null
     */
    public function queryCommandList($productId, $deviceId, $masterKey)
    {
        return Aep_device_command::QueryCommandList(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            $productId,
            $deviceId,
        );
    }

    /**
     * 查询单条命令
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $commandId
     * @return 返回响应：bool|null
     */
    public function queryCommand($productId, $deviceId, $masterKey, string $commandId = '')
    {
        $result = Aep_device_command::QueryCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            $commandId,
            $productId,
            $deviceId,
        );
        return $result;
    }

    /**
     * 取消命令
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $commandId
     * @return 返回响应：bool|null
     */
    public function cancelCommand($productId, $deviceId, $masterKey, string $commandId = '')
    {
        $result = Aep_device_command::CancelCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "commandId" => $commandId,
                "deviceId"  => $deviceId,
                "productId" => $productId,
            ])
        );
        return $result;
    }

    /**
     * 取消所有命令
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @return 返回响应：bool|null
     */
    public function cancelAllCommand($productId, $deviceId, $masterKey)
    {
        $result = Aep_device_command_cancel::CancelAllCommand(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "deviceId"  => $deviceId,
                "productId" => $productId,
            ])
        );
        return $result;
    }

    /**
     * @param $productId
     * @param $masterKey
     * @param int $pageNow
     * @param int $pageSize
     * @return 返回响应：bool|null
     */
    public function getSubscriptionsList($productId, $masterKey, int $pageNow = 1, int $pageSize = 10)
    {
        $result = Aep_subscribe_north::GetSubscriptionsList(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $productId,
            $pageNow,
            $pageSize,
            $masterKey
        );
        return $result;
    }

    /**
     * @param $productId
     * @param $masterKey
     * @param string $subId
     * @param string $subLevel
     * @return 返回响应：bool|null
     */
    public function deleteSubscription($productId, $masterKey, string $subId = '', string $subLevel = '')
    {
        $result = Aep_subscribe_north::DeleteSubscription(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $subId,
            $productId,
            $subLevel,
            $masterKey
        );
        return $result;
    }

    /**
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $subUrl
     * @param string $subLevel
     * @return 返回响应：bool|null
     */
    public function createSubscription($productId, $deviceId, $masterKey, string $subUrl = '', string $subLevel = '')
    {
        $result = Aep_subscribe_north::CreateSubscription(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $masterKey,
            json_encode([
                "deviceId"  => $deviceId,
                "operator"  => "klwlbj",
                "productId" => $productId,
                "subLevel"  => $subLevel,
                "subTypes"  => [1, 2, 3, 4, 5, 6], // 消息类型(必填),可填写1个或多个(1表示设备数据变化通知、2表示设备响应命令通知、3表示设备事件上报、4表示设备上下线通知、5表示创建删除设备、9表示TUP合并数据上报）
                "subUrl"    => $subUrl,
            ])
        );
        return $result;
    }

    /**
     * @param $productId
     * @param $masterKey
     * @param string $subId
     * @return 返回响应：bool|null
     */
    public function getSubscription($productId, $masterKey, string $subId = '')
    {
        return Aep_subscribe_north::GetSubscription(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $productId,
            $subId,
            $masterKey
        );
    }

    /**
     * @param $productId
     * @param $masterKey
     * @param string $subId
     * @return 返回响应：bool|null
     */
    public function QueryDeviceByImei($productId, $imei, $masterKey)
    {
        return Aep_nb_device_management::QueryDeviceByImei(
            config('services.ctwing.key'),
            config('services.ctwing.secret'),
            $productId,
            $imei,
            $masterKey
        );
    }
}
