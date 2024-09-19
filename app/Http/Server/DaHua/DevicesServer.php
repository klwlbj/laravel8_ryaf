<?php

namespace App\Http\Server\DaHua;

use App\Http\Server\BaseServer;
use App\Utils\Tools;

class DevicesServer extends BaseServer
{
    public function getList($params)
    {
        $req = [
        ];

        if(!empty($params['createTime'])){
            $req['createTime'] = $params['createTime'] . '000';
        }

        if(!empty($params['searchAfter'])){
            $req['searchAfter'] = Tools::jsonDecode($params['searchAfter']);
        }

        if(!empty($params['storeId'])){
            $req['storeId'] = $params['storeId'];
        }


//        print_r($req);die;
        $res = RequestServer::getInstance()->doRequest('device/api/scroll/deviceList',$req);
//        print_r($res);die;
        if($res['code'] != 0){
            Response::setMsg($res['errMsg']);
            return false;
        }
        return [
            'list' => $res['data']['openApiDeviceVo'] ?? [],
            'searchAfter' => $res['data']['searchAfter'] ?? [],
        ];
    }
}
