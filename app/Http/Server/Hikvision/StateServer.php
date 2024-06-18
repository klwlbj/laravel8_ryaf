<?php

namespace App\Http\Server\Hikvision;

use App\Utils\Tools;
use App\Http\Server\BaseServer;

class StateServer extends BaseServer
{
    public $resourceCode = '900003';

    public function getStatusId($statusId, $creditCode)
    {
        return "9423" . $this->resourceCode . "00" . $creditCode . '-' . $statusId;
    }

    public function report(array $input)
    {
        $unitId       = $input['unitId'];
        $statusId     = $input['statusId'];
        $deviceId     = $input['imei'];
        $onlineStatus = $input['onlineStatus'];
        $dateTime     = $input['dateTime'];
        $deviceName   = $input['deviceName'];

        // $unitId         = '114529';
        // $statusId   = 202;
        // $deviceId   = '865118076532179';
        $creditCode = UnitsServer::getInstance()->getCreditCode($unitId);

        $params = [
            [
                'eventId'        => $this->getStatusId($statusId, $creditCode), // 状态数据事件唯一编码
                "deviceCategory" => 2, // 设备种类,物联网感应设备/网关
                "deviceName"     => $deviceName, // 设备名称
                "deviceId"       => DevicesServer::getInstance()->getFireDeviceId($deviceId, $creditCode), // 设备编号
                "unitId"         => UnitsServer::getInstance()->getUnitsId($creditCode), // 所属单位编号
                // "faultStatus"    => '1',// 故障状态，多个故障码”，”分隔
                "onlineStatus"   => $onlineStatus, // 在离线状态：-1-未注册，0-离线，1-在线
                "eventTime"      => Tools::getISO8601Date($dateTime), // 事件发生时间,2020-02-17T15:00:00.000+08:00格式
            ],
            // ... 可多个
        ];

        return RequestServer::getInstance()->doRequest('fire/v1/devicestates/report', ['deviceStates' => $params]);
    }
}
