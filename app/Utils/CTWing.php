<?php

namespace App\Utils;

use App\Utils\Apis\Aep_device_event;
use App\Utils\Apis\Aep_device_command;
use App\Utils\Apis\Aep_subscribe_north;
use App\Utils\Apis\Aep_device_command_cancel;
use App\Utils\Apis\Aep_device_command_lwm_profile;

class CTWing extends BaseIoTClient
{
    public const PRODUCT_ID = '16918237';
    public const DEVICE_ID  = '6543c6a31a574400a02581d46351251a';

    public function queryDeviceEventList($productId, $deviceId)
    {
        $result = Aep_device_event::QueryDeviceEventList(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
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

    public function queryDeviceEventTotal($productId, $deviceId)
    {
        $result = Aep_device_event::QueryDeviceEventTotal(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
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
     * 下发命令（暂时废弃）
     * @param $deviceId
     * @param $args
     * @return Apis\Core\返回响应：bool|null
     */
    public function createCommand($productId, $deviceId, string $args = '9000000192000c00000000060000ffff000C00023D')
    {
        $result = Aep_device_command::CreateCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
            json_encode([
                "content"   => [
                    'dataType' => 1, // 数据类型：1字符串，2十六进制
                    'payload'  => $args, // 指令内容,数据格式为十六进制时需要填十六进制字符串,
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
     * @param $deviceId
     * @param $args
     * @return Apis\Core\返回响应：bool|null
     */
    public function createCommandLwm2mProfile($productId, $deviceId, $command = 'longSilence', $dwPackageNo = '00000006')
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
            env('CTWING_MASTER_KEY'),
        );
        return $result;
    }

    public function queryCommandList($productId, $deviceId)
    {
        return Aep_device_command::QueryCommandList(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
            $productId,
            $deviceId,
        );
    }

    /**
     * 查询单条命令
     * @param $productId
     * @param $deviceId
     * @param $commandId
     * @return Apis\Core\返回响应：bool|null
     */
    public function queryCommand($productId, $deviceId, $commandId = '')
    {
        $result = Aep_device_command::QueryCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
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
     * @param $commandId
     * @return Apis\Core\返回响应：bool|null
     */
    public function cancelCommand($productId, $deviceId, $commandId = '')
    {
        $result = Aep_device_command::CancelCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
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
     * @return Apis\Core\返回响应：bool|null
     */
    public function cancelAllCommand($productId, $deviceId)
    {
        $result = Aep_device_command_cancel::CancelAllCommand(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
            json_encode([
                "deviceId"  => $deviceId,
                "productId" => $productId,
            ])
        );
        return $result;
    }

    public function getSubscriptionsList($productId, $pageNow = 1, $pageSize = 10)
    {
        $result = Aep_subscribe_north::GetSubscriptionsList(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $productId,
            $pageNow,
            $pageSize,
            env('CTWING_MASTER_KEY')
        );
        return $result;
    }

    public function deleteSubscription($productId, $subId = '', $subLevel = '')
    {
        $result = Aep_subscribe_north::DeleteSubscription(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $subId,
            $productId,
            $subLevel,
            env('CTWING_MASTER_KEY')
        );
        return $result;
    }

    public function createSubscription($productId, $deviceId, $subUrl = '', $subLevel = '')
    {
        $result = Aep_subscribe_north::CreateSubscription(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            env('CTWING_MASTER_KEY'),
            json_encode([
                "deviceId"      => $deviceId,
                "operator"      => "klwlbj",
                "productId"     => $productId,
                "subLevel"      => $subLevel,
                "subTypes"      => [1, 2, 3, 4, 5, 6], // 消息类型(必填),可填写1个或多个(1表示设备数据变化通知、2表示设备响应命令通知、3表示设备事件上报、4表示设备上下线通知、5表示创建删除设备、9表示TUP合并数据上报）
                "subUrl"        => $subUrl,
            ])
        );
        return $result;
    }

    public function getSubscription($productId, $subId = '')
    {
        return Aep_subscribe_north::GetSubscription(
            env('CTWING_KEY'),
            env('CTWING_SECRET'),
            $productId,
            $subId,
            env('CTWING_MASTER_KEY')
        );
    }
}
