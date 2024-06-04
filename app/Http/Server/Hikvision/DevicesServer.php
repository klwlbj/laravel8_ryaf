<?php

namespace App\Http\Server\Hikvision;

use App\Utils\Tools;
use App\Http\Server\BaseServer;

class DevicesServer extends BaseServer
{
    public $resourceCode = '203005';
    public $deviceType = [
        1  => '用户信息传输装置',
        2  => '用电主机',
        3  => '用水主机',
        4  => '独立式可燃气体报警器',
        5  => '独立式感烟报警器',
        6  => '物联网网关（DTU）',
        7  => '物联网消防报警网关(433)',
        8  => '室外消火栓',
        9  => '视频烟感',
        10 => '视频温感',
        11 => '智能分析仪',
        12 => '充电桩',
        13 => 'IPC视频网关',
        14 => '可视化烟雾探测器',
        15 => '安消智能摄像机',
        16 => '智能用电网关',
        17 => '火灾报警控制器',
        18 => 'LORA物联网关',
        19 => '电气火灾报警控制器',
        20 => '可燃气体报警控制器',
    ];

    public function getFireDeviceId($deviceId,$creditCode)
    {
        # 9423 + 资源吗 + 00 + 信用码 + 设备IMEI
        return "9423" . $this->resourceCode . "00" . $creditCode . '-' . $deviceId;
    }

    public function add($params)
    {
        # 单位表id
        $id         = '114111';
<<<<<<< Updated upstream

        # 设备imei 可换成平台设备表id
        $imei = '868550067139399';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);
=======
        $regionCode = '440111';
        $deviceId = '868550067139398';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id, $regionCode);
>>>>>>> Stashed changes
        $unitId     = UnitsServer::getInstance()->getUnitsId($creditCode);

        $params = [
            'fireDeviceId' => $this->getFireDeviceId($imei,$creditCode),
            'name'         => $imei,
            'deviceCode'   => $imei,
            'pointX'       => '113.224015',
            'pointY'       => '23.212011',
            'deviceType'   => '5',
            'notifyPhone'  => '13112283032',
            'unitId'       => $unitId,
        ];

        $date                 = Tools::getISO8601Date();
        $params['createTime'] = $date;
        $params['updateTime'] = $date;
        //        print_r($params);die;
        $data = [
            'fireDevices' => [
                $params,
            ],
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/fireDevices/add', $data);
    }

    public function update($params)
    {
        # 单位表id
        $id         = '114111';
        # 设备imei 可换成平台设备表id
        $imei = '868550067139399';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);
        $unitId     = UnitsServer::getInstance()->getUnitsId($creditCode);

        $params = [
            'fireDeviceId' => $this->getFireDeviceId($imei,$creditCode),
            'name'         => $imei,
            'deviceCode'   => $imei,
            'pointX'       => '113.224015',
            'pointY'       => '23.212011',
            'deviceType'   => '5',
            'notifyPhone'  => '13112283032',
            'unitId'       => $unitId,
        ];

        $date                 = Tools::getISO8601Date();
        $params['createTime'] = $date;
        $params['updateTime'] = $date;
        //        print_r($params);die;
        $data = [
            'fireDevices' => [
                $params,
            ],
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/fireDevices/update', $data);
    }

    public function delete($params)
    {
        # 单位表id
        $id         = '114111';
        # 设备imei 可换成平台设备表id
        $imei = '868550067139399';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);
        $fireDevicesId = $this->getFireDeviceId($imei,$creditCode);
        $data = [
            'fireDeviceIds' => [
                $fireDevicesId,
            ],
        ];

        return RequestServer::getInstance()->doRequest('fire/v1/fireDevices/delete', $data);
    }
}
