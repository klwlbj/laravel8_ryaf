<?php

namespace App\Utils;

use App\Utils\Apis\Aep_device_event;
use App\Utils\Apis\Aep_device_command;
use App\Utils\Apis\Aep_subscribe_north;
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
     * @return 返回响应：bool|null
     */
    public function queryDeviceEventList($productId, $deviceId, $masterKey)
    {
        $result = Aep_device_event::QueryDeviceEventList(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
     * 下发命令（4G卡用）
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $command
     * @param string $dwPackageNo
     * @return 返回响应：bool|null
     */
    public function createCommand($productId, $deviceId, $masterKey, string $command = 'longSilence', string $dwPackageNo = '00000006')
    {
        $args = self::COMMAND[$command] ?? self::COMMAND['longSilence'];

        $cmd = substr($args[0] . $dwPackageNo . $args[1], 0, -2); //剔除最后两位

        $result = Aep_device_command::CreateCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $masterKey,
            json_encode([
                "content"   => [
                    "payload" => [
                        "val" => $cmd . $this->checkSum($cmd), // 校验和
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

    /**
     * lwm2m协议有profile指令下发接口
     * @param $productId
     * @param $deviceId
     * @param $masterKey
     * @param string $command
     * @param string $dwPackageNo
     * @return 返回响应：bool|null
     */
    public function createCommandLwm2mProfile($productId, $deviceId, $masterKey, string $command = 'longSilence', string $dwPackageNo = '00000006')
    {
        $args = self::COMMAND[$command] ?? self::COMMAND['longSilence'];

        $result = Aep_device_command_lwm_profile::CreateCommandLwm2mProfile(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            json_encode([
                "command"   => [
                    "serviceId" => "Msg",
                    "method"    => "CMD",
                    "paras"     => [
                        "val" => $args[0] . $dwPackageNo . $args[1],
                    ],
                ],
                "deviceId"  => $deviceId,
                "operator"  => "klwlbj",
                "productId" => $productId,
            ]),
            $masterKey
        );
        return $result;
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $productId,
            $pageNow,
            $pageSize,
            $masterKey
        );
        return $result;
    }

    /**
     * @param $productId
     * @param string $subId
     * @param string $subLevel
     * @return 返回响应：bool|null
     */
    public function deleteSubscription($productId, $masterKey, string $subId = '', string $subLevel = '')
    {
        $result = Aep_subscribe_north::DeleteSubscription(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
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
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $productId,
            $subId,
            $masterKey
        );
    }

    /**
     * 和校验
     * @param $string
     * @return string
     */
    private function checkSum($string): string
    {
        // 将字符串按两个字符分割成数组元素
        $hexArray = str_split($string, 2);

        // 将每个数组元素从十六进制转换为十进制
        $decArray = array_map('hexdec', $hexArray);

        // 对数组中的所有元素求和
        $sum = array_sum($decArray);

        // 取和的低8位（和对256取模）
        $checksum = $sum % 256;

        return strtoupper(str_pad(dechex($checksum), 2, '0', STR_PAD_LEFT));
    }
}
