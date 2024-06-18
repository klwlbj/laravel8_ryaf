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
        $id  = $params['unitId'];
        # 设备imei 可换成平台设备表id
        $imei = $params['imei'];
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);
        $unitId     = UnitsServer::getInstance()->getUnitsId($creditCode);

        $params = [
            'fireDeviceId' => $this->getFireDeviceId($imei,$creditCode), //设备id 9423 + 资源吗 + 00 + 信用码 + 设备IMEI
            'name'         => $params['deviceName'], //设备名称 imei
            'deviceCode'   => $imei, //设备编码 imei
            'pointX'       => $params['pointX'], //设备经度
            'pointY'       => $params['pointY'], //设备纬度
            'deviceType'   => $params['deviceType'], //设备类型  5为独立式感烟报警器
            'notifyPhone'  => $params['notifyPhone'], //通知号码
            'unitId'       => $unitId, //单位id
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
        $id  = $params['unitId'];
        # 设备imei 可换成平台设备表id
        $imei = $params['imei'];
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);
        $unitId     = UnitsServer::getInstance()->getUnitsId($creditCode);

        $params = [
            'fireDeviceId' => $this->getFireDeviceId($imei,$creditCode), //设备id 9423 + 资源吗 + 00 + 信用码 + 设备IMEI
            'name'         => $params['deviceName'], //设备名称 imei
            'deviceCode'   => $imei, //设备编码 imei
            'pointX'       => $params['pointX'], //设备经度
            'pointY'       => $params['pointY'], //设备纬度
            'deviceType'   => $params['deviceType'], //设备类型  5为独立式感烟报警器
            'notifyPhone'  => $params['notifyPhone'], //通知号码
            'unitId'       => $unitId, //单位id
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
        $id = $params['unitId'];
        # 设备imei 可换成平台设备表id
        $imei = $params['imei'];
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
