<?php

namespace App\Http\Controllers;

use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Server\HikvisionICloud;

class HikvisionCloudController
{
    public const MSG_TYPE = [
        980001 => "资源信息变更通知",
        980002 => "报警实时信息",
        980003 => "报警处理信息",
        980004 => "故障实时信息",
        980005 => "故障处理信息",
        980006 => "设备在离线消息",
        980007 => "监控点在离线消息",
        980008 => "监测实时信息",
        980009 => "指令下发状态信息",
        980015 => "充电桩消息",
        980016 => "设备事件消息",
        980019 => "远程远程配置消息",
    ];

    public const NOTIFY_TYPE = [
        980001001 => '添加',
        980001002 => '修改',
        980001003 => '删除',
    ];

    public const DATA_TYPE = [
        980001101 => '运营公司',
        980001102 => '单位',
        980001103 => '建筑物',
        980001104 => '消防设备',
        980001105 => '传感器',
        980001106 => '视频设备',
        980001107 => '监控点',

        980002101 => '设备报警实时信息',
        980003101 => '设备报警处理信息',
        980004101 => '设备故障实时信息',
        980005101 => '设备故障处理信息',
        980006101 => '设备在离线消息',
        980007101 => '监控点在离线消息',
        980008101 => '设备监测实时信息',
        980009101 => '指令下发状态信息',
        980010101 => '实时巡查记录',
        980011101 => '实时维保记录',
        980012101 => '实时隐患消息',
        980013101 => '隐患处理消息',
        980014101 => '在离岗消息',
        980015101 => '充电事件',
        980015102 => '充电状态',
        980016101 => '设备事件实时信息',
        980019101 => '参数配置下发结果',
        980019102 => '设备参数变更通知',
        980019103 => '传感器阈值下发结果',
        980019104 => '传感器阈值变更通知',
    ];

    public HikvisionICloud $cloudClient;

    public function __construct()
    {
        $this->cloudClient = new HikvisionICloud(env('HIK_KEY'), env('HIK_SECRET'));
    }

    public function index()
    {
        $params = ['msgType' => ''];
        var_dump($this->cloudClient->doRequest('/api/subscription/v2/list', $params));
    }

    /*
     * 回调
     */
    public function callback(Request $request, string $code)
    {
        $jsonData = $request->json()->all();
        // if ($jsonData) {
        //     Log::channel('hikvision')->info('Received HIK JSON ' . $code . ' data: ' . json_encode($jsonData));
        // }

        // 判断型号deviceSerial，单独打印日志

        if (isset($jsonData['fps']['msgList'][0]['body']['data'][0]['systemType'])) {
            $systemType = $jsonData['fps']['msgList'][0]['body']['data'][0]['systemType'];

            if ($systemType == "500006") {
                // 打印日志
                // dd($jsonData);

                if (isset($jsonData['fps']['msgList'][0]['msgType'])) {
                    $jsonData['fps']['msgList'][0]['msgTypeName'] = self::MSG_TYPE[$jsonData['fps']['msgList'][0]['msgType']] ?? '';
                }

                if (isset($jsonData['fps']['msgList'][0]['body']['dataType'])) {
                    $jsonData['fps']['msgList'][0]['body']['dataTypeName'] = self::DATA_TYPE[$jsonData['fps']['msgList'][0]['body']['dataType']] ?? '';
                }

                if (isset($jsonData['fps']['msgList'][0]['body']['dataType'])) {
                    $jsonData['fps']['msgList'][0]['body']['notifyTypeName'] = self::NOTIFY_TYPE[$jsonData['fps']['msgList'][0]['body']['notifyType']] ?? '';
                }

                $resourceSerial = $jsonData['fps']['msgList'][0]['body']['data'][0]['resourceSerial'] ?? ($jsonData['fps']['msgList'][0]['body']['data'][0]['deviceSerial'] ?? '');

                if (!empty($resourceSerial)) {
                    Tools::deviceLog('camera-' . $code, $resourceSerial, 'hikvision', $jsonData);
                }
            }
        }
        return response()->json(['status' => 'success']);
    }

    /*
     * 增加订阅
     */
    public function addSubcription()
    {
        // 示例url
        $code   = 980019;
        $url    = 'http://test.crzfxjzn.com/api/hikvision/callback/' . $code;
        $params = [
            'msgType' => $code,
            "postUrl" => $url,
        ];
        return $this->cloudClient->doRequest('/api/subscription/v2/add', $params);
    }

    public function subcriptionList($msgType = 0)
    {
        $params = $msgType ? ['msgType' => $msgType] : [];
        return $this->cloudClient->doRequest('/api/subscription/v2/list', $params);
    }

    /*
     * 分页查询设备事件消息
     */
    public function getTraditionMsg(Request $request)
    {
        // 示例：{
        //     "startTime": "2019-01-11T11:13:31.000+08:00",
        //     "stopTime": "2025-01-11T11:13:31.000+08:00",
        //     "pageNo": 1,
        //     "pageSize": 20,
        //     "flagId": "0",
        //     "offset": 0
        // }
        $jsonData = $request->json()->all();
        // 验证json todo
        return $this->cloudClient->doRequest('/api/businessData/v2/getTraditionMsg', $jsonData);
    }

    /*
     * 报警处理
     */
    public function alarmHandle(Request $request)
    {
        // 示例：{
        //     "alarmID": "9658214525",
        //     "processDescription": "设备报警，已处理",
        //     "processUserName": "张三",
        //     "processTime": "2019-01-11T11:13:31.000+08:00",
        //     "imageURLs": [
        //         "https://test-data.oss-cn.aliyuncs.com/fp_cloud_android/image/162114906.jpg"
        //     ]
        // }
        $jsonData = $request->json()->all();
        // 验证json todo
        return $this->cloudClient->doRequest('/api/businessData/v2/alarm/process', $jsonData);
    }

    /*
     * 分页查询设备状态
     */
    public function getFireDeviceStatus(Request $request)
    {
        // 示例：{
        //     "flagId": "0",
        //     "offset": "0",
        //     "pageNo": 1,
        //     "pageSize": 20
        // }

        $jsonData = $request->json()->all();
        // 验证json todo
        return $this->cloudClient->doRequest('/api/businessData/v2/getFireDeviceStatus', $jsonData);
    }

    /*
     * 获取设备当前参数值
     */
    public function getParamConfig(int $deviceID = 0)
    {
        return $this->cloudClient->doRequest('/api/device/v2/getParamConfig', ['deviceID' => $deviceID]);
    }

    /*
     * 消防传感器修改
     */
    public function updateSensor(Request $request)
    {
        // 示例：{
        //     "sensorID": "876153405987282945",
        //     "sensorName": "传感器1",
        //     "location": "杭州",
        //     "extendData": "[{\\\"data\\\":{\\\"wireConfig\\\":[{\\\"circuitNo\\\":\\\"1\\\",\\\"wireType\\\":\\\"1\\\"}],\\\"circuitType\\\":\\\"2\\\"},\\\"extendType\\\":\\\"circuit\\\"}]"
        // }
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/channel/v2/update', $jsonData);
    }

    /*
     * 消防传感器删除
     */
    public function deleteSensor(int $sensorID = 0)
    {
        return $this->cloudClient->doRequest('/api/channel/v2/delete', ['sensorID' => $sensorID]);
    }

    /*
     * 消防传感器增加
     */
    public function addSensor(Request $request)
    {
        // 示例：{
        //     "sensorName": "温感",
        //     "sensorSerial": "1",
        //     "deviceID": "110",
        //     "sensorType": 0,
        //     "location": "杭州",
        //     "extendData": "[{\"data\":{\"wireConfig\":[{\"circuitNo\":\"1\",\"wireType\":\"0\"}],\"circuitType\":\"2\"},\"extendType\":\"circuit\"}]"
        // }
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/channel/v2/add', $jsonData);
    }

    /*
     * 消防设备修改
     */
    public function updateDevice(Request $request)
    {
        // 示例：{
        //     "deviceID": "876153405987282945",
        //     "deviceName": "燃气设备",
        //     "location": "杭州市西湖区文三路",
        //     "lat": 32,
        //     "lon": 120
        // }
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/device/v2/update', $jsonData);
    }

    /*
     * 消防设备删除
     */
    public function deleteDevice(int $deviceID = 0)
    {
        return $this->cloudClient->doRequest('/api/device/v2/delete', ['deviceID' => $deviceID]);
    }

    /*
     * 消防传感器增加
     */
    public function addVideoDevice(Request $request)
    {
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/videoDevice/v2/add', $jsonData);
    }

    /*
     * 摄像头地址
     */
    public function getCameraPlayURL(Request $request)
    {
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/videoDevice/v2/liveAddress', $jsonData);
    }

    /*
     * 获取报警
     */
    public function getAlarm(Request $request)
    {
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/businessData/v2/getAlarm', $jsonData);
    }

    /*
     * 消防设备添加
     */
    public function addDevice(Request $request)
    {
        // 示例：{
        //     "deviceName": "烟感",
        //     "deviceSerial": "112598786655565",
        //     "deviceModel": "800004",
        //     "deviceType": 600002,
        //     "communicationType": 900004,
        //     "verificationCode": "123456",
        //     "producterID": 180001,
        //     "productModelID": 32109,
        //     "location": "杭州西湖",
        //     "autoAddChannel": true,
        //     "unitID": "864898412617191425",
        //     "lat": 45,
        //     "lon": 112
        // }
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/device/v2/add', $jsonData);
    }

    /*
     * 获取视频监控点列表
     */
    public function getCamera(Request $request)
    {
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/videoDevice/v2/getCamera', $jsonData);
    }

    /*
     * 获取视频设备列表
     */
    public function getVideoDevice(Request $request)
    {
        $jsonData = $request->json()->all();
        return $this->cloudClient->doRequest('/api/videoDevice/v2/getVideoDevice', $jsonData);
    }

    /*
    设备类型字典
    */
    public function deviceTypeDict()
    {
        return $this->cloudClient->doRequest('/api/device/v2/dict');
    }
}
