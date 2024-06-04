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

    public function report(array $params)
    {
        $id         = '114111';
        $monitorId  = 12;
        $regionCode = '440111';
        $deviceId   = '868550067139398';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id, $regionCode);

        $params = [
            [
                'eventId'        => $this->getMonitorId($monitorId, $creditCode),
                "deviceCategory" => 2,
                "deviceName"     => '170',
                "deviceId"       => DevicesServer::getInstance()->getFireDeviceId($deviceId, $creditCode),
                "unitId"         => UnitsServer::getInstance()->getUnitsId($creditCode),
                "monitorValues"  => [
                    [ // 电量
                        'monitorTypeCode' => '1',
                        'monitorValue'    => '80',
                    ],
                    [ // 环境温度
                        'monitorTypeCode' => '80',
                        'monitorValue'    => '22',
                    ],
                    [ // 信号强度
                        'monitorTypeCode' => '10',
                        'monitorValue'    => '22',
                    ],
                    [ // 迷宫污染度
                        'monitorTypeCode' => '40',
                        'monitorValue'    => '22',
                    ],
                ],
                "eventTime"      => Tools::getISO8601Date(),
            ],
        ];

        return RequestServer::getInstance()->doRequest('fire/v1/monitors/report', ['monitors' => $params]);
    }
}
