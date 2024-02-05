<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Server\HikvisionICloud;

class HikvisionCloudController
{
    public const SUBSCRIBE_MESSAGE_TYPE = [
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
        if ($jsonData) {
            Log::channel('hikvision')->info('Received HIK JSON ' . $code . ' data: ' . json_encode($jsonData));
        }
        // 处理 JSON 数据的代码 todo
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
    设备类型字典
    */
    public function deviceTypeDict()
    {
        return $this->cloudClient->doRequest('/api/device/v2/dict');
    }
}
