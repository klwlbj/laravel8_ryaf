<?php

namespace App\Http\Controllers\DaHua;

use App\Http\Server\DaHua\Response;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Ramsey\Uuid\Nonstandard\Uuid;

class ReportController
{
    public function report(Request $request)
    {
        $params = $request->all();

        $params = Tools::jsonDecode($params);

        #可接受事件
        $eventTypeArr = [
            'fireAlarm',
            'dismantleAlarm',
            'offlineAlarm',
            'onlineMessage',
            'deviceOfflineAlarm',
        ];

//        print_r($params);die;

        try {
            if (isset($params['thirdEvent']['eventType']) && in_array($params['thirdEvent']['eventType'], $eventTypeArr)) {
                $data = [
//                    'devMessageId' => $params['devMessageId'],
                    'id' => $params['id'],
                    'time' => $params['time'],
                    'deviceId' => $params['deviceId'],
                    'eventType' => $params['thirdEvent']['eventType'],
                    'eventName' => $params['thirdEvent']['eventName'],
                    'eventStatus' => $params['thirdEvent']['eventStatus'],
                    'eventCode' => $params['thirdEvent']['eventCode'],
                    'eventLevel' => $params['thirdEvent']['eventLevel'],
                    'productType' => $params['productType'],
                ];

                $logFileName = date('YmdHis') . '-' . $data['deviceId'] . '-' . md5(Uuid::uuid4()->toString());
                Tools::writeLog('', 'dahuacloud', $data, $logFileName, '%message%%context% %extra%');
            }
        } catch (\Exception $e) {
            Tools::writeLog('dahuacloud analyze exception: ' . $e->getMessage() . ' this json:', 'dahuacloud_exception', $params, 'exception');
        }

        Tools::writeLog('params:','dahuacloud', $params);
        return Response::returnJson(['code' => 0,'message' => '','date' => []]);
    }
}
