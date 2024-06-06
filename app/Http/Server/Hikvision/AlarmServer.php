<?php

namespace App\Http\Server\Hikvision;

use App\Utils\Tools;
use App\Http\Server\BaseServer;

class AlarmServer extends BaseServer
{
    public $resourceCode = '900000';

    public const ALARM_TYPE = [
        1  => '电瓶车乱停乱放',
        2  => '燃气报警',
        3  => '灭火器遗失',
        4  => '水压报警',
        5  => '取水报警',
        6  => '温度报警',
        7  => '电气温度报警',
        8  => '剩余电流报警',
        9  => '液位报警',
        10 => '通用报警',
        11 => '烟雾检测',
        12 => '火点检测',
        13 => '物品遗留',
        14 => '温差报警',
        15 => '烟火检测',
        16 => '车辆违停',
        17 => '通道占用',
        18 => '火点报警',
        19 => '倾斜报警',
        20 => '手动报警',
        21 => '人员在离岗',
        22 => '无证上岗',
        23 => '过流报警',
        24 => '过压报警',
        25 => '欠压报警',
        26 => '防火门报警',
        27 => '火焰报警',
        28 => '波动报警',
        29 => '水浸报警',
        30 => '湿度报警',
        31 => '电弧报警',
        32 => '抽烟报警',
        33 => '人离火报警',
        34 => '人形报警',
        99 => '其他报警',
    ];

    public function getAlarmId($alarmId, $creditCode)
    {
        return "9423" . $this->resourceCode . "00" . $creditCode . '-' . $alarmId;
    }

    /*
     * 9.6.1.2 上传报警数据接口
     */
    public function report()
    {
        $id         = '114529';
        $alarmId    = 227;
        $deviceId   = '865118076532179';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);

        $params = [
            [
                'eventId'        => $this->getAlarmId($alarmId, $creditCode), // 报警数据事件唯一编码
                "unitId"         => UnitsServer::getInstance()->getUnitsId($creditCode), // 所属单位编号
                "deviceCategory" => 2, // 设备种类
                "deviceId"       => DevicesServer::getInstance()->getFireDeviceId($deviceId, $creditCode), // 设备编号
                "alarmType"      => '11', // 告警类型,详见 ALARM_TYPE
                'alarmLevel'     => '1', // 告警等级
                'images'         => [
                    // 'PicInfo' => [
                         [
                    'picUrl'  => 'https://www.hikfirecloud.com/web/img/banner-default.bfacf600.png',
                    'picName' => '123',
                    // 'format' => '',
                    // 'picData' => ''
                     ]
                    // ]

                    // 'https://www.hikfirecloud.com/web/img/banner-default.bfacf600.png',
                ], // 告警图片信息
                // 'cameraId'       => '', // 报警关联监控点id
                // 'videoUrl'       => '', // 告警视频数据url，多个url通过逗号分隔
                // 'audioUrl'       => '', // 告警音频数据url，多个url通过逗号分隔。
                "eventTime"      => Tools::getISO8601Date(), // 事件发生时间,2020-02-17T15:00:00.000+08:00格式
            ],
        ];

        return RequestServer::getInstance()->doRequest('fire/v1/alarms/report', ['alarms' => $params]);
    }

    /*
     * 9.6.1.3 上传报警确认数据接口
     */
    public function confirm()
    {
        $id         = '114529';
        $alarmId    = 227; // 需与上传报警的事件id一致
        // $deviceId   = '865118076532179';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id);

        $params = [
            [
                'eventId'        => $this->getAlarmId($alarmId, $creditCode), // 报警数据事件唯一编码
                "unitId"         => UnitsServer::getInstance()->getUnitsId($creditCode), // 所属单位编号
                'handleUserName' => '李敏华', // 确认人员名称
                'handleTime'     => Tools::getISO8601Date(), // 事件发生时间,2020-02-17T15:00:00.000+08:00格式
                'handleStatus'   => '2', // 确认结果：1-确认报警，2-误报
                'handleRemark'   => '123', // 确认意见
                // 'handleImages'=> '',
                // 'handleVideoUrl'=> '',
                // 'handleAudioUrl'=> '',
            ],
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/alarms/process', ['alarmConfirms' => $params]);
    }

    /*
     * 9.6.1.4 上传报警复核数据接口
     */
    public function reConfirm()
    {
        $id         = '114111';
        $alarmId    = 227; // 需与上传报警的事件id一致
        $regionCode = '440111';
        $creditCode = UnitsServer::getInstance()->getCreditCode($id, $regionCode);

        $params = [
            [
                'eventId'         => $this->getAlarmId($alarmId, $creditCode), // 报警数据事件唯一编码
                "unitId"          => UnitsServer::getInstance()->getUnitsId($creditCode), // 所属单位编号
                'confirmUserName' => '李总', // 确认人员名称
                'confirmTime'     => Tools::getISO8601Date(), // 复核时间,2020-02-17T15:00:00.000+08:00格式
                'confirmRemark'   => '123', // 确认意见
                'fireStartTime'   => Tools::getISO8601Date(), // 起火时间,2020-02-17T15:00:00.000+08:00格式
                // 'firePlace' => '', // 起火场所
                // 'fireIntensity' => '',// 着火火势：0-大，1-中，2-小，3-已灭
                // 'isTrapped' => '', // 是否有人被困：0-未知；1-有；2-无
                // 'trapped' => '', // 被困人数
                // 'fireSite' => '', // 起火源
                // 'fireCause' => '', // 起火原因
            ],
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/alarms/confirm', ['alarmConfirms' => $params]);
    }
}
