<?php

namespace App\Http\Server\Hikvision;

use App\Utils\Tools;
use App\Http\Server\BaseServer;

class MonitorServer extends BaseServer
{
    public $resourceCode = '900002';

    public function getMonitorId($monitorId, $creditCode)
    {
        return "9423" . $this->resourceCode . "00" . $creditCode . '-' . $monitorId;
    }

    public function report(array $input)
    {
        $unitId     = $input['unitId'];
        $monitorId  = $input['monitorId'];
        $deviceId   = $input['imei'];
        $dateTime   = $input['dateTime'];
        $deviceName = $input['deviceName'];

        // $unitId         = '114529';
        // $monitorId  = 12;
        // $deviceId   = '865118076532179';
        $creditCode = UnitsServer::getInstance()->getCreditCode($unitId);

        $params = [
            [
                'eventId'        => $this->getMonitorId($monitorId, $creditCode),
                "deviceCategory" => 2, // 设备种类,物联网感应设备/网关
                "deviceName"     => $deviceName,
                "deviceId"       => DevicesServer::getInstance()->getFireDeviceId($deviceId, $creditCode),
                "unitId"         => UnitsServer::getInstance()->getUnitsId($creditCode),
                "monitorValues"  => [
                    [ // 电量
                        'monitorTypeCode' => '1',
                        'monitorValue'    => $input['battery'],
                    ],
                    [ // 环境温度
                        'monitorTypeCode' => '80',
                        'monitorValue'    => $input['temperature'],
                    ],
                    // [ // 环境湿度
                    //     'monitorTypeCode' => '90',
                    //     'monitorValue'    => $input['humidness'],
                    // ],
                    [ // 信号强度
                        'monitorTypeCode' => '10',
                        'monitorValue'    => $input['signal'],
                    ],
                    [ // 迷宫污染度
                        'monitorTypeCode' => '40',
                        'monitorValue'    => $input['pollution'],
                    ],
                ],
                "eventTime"      => Tools::getISO8601Date($dateTime),
            ],
        ];

        return RequestServer::getInstance()->doRequest('fire/v1/monitors/report', ['monitors' => $params]);
    }
}
