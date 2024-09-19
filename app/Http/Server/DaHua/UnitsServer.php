<?php

namespace App\Http\Server\DaHua;

use App\Http\Server\BaseServer;

class UnitsServer extends BaseServer
{
    public function getList($params)
    {
        $req = [
            'pageNum' => $params['page'] ?? 1,
            'pageSize' => $params['pageSize'] ?? 100,
//            'createTime' => $params['createTime'] ?? strtotime(date('Y-m-d')),
        ];

        if(!empty($params['createTime'])){
            $req['createTime'] = $params['createTime'] . '000';
        }
//        print_r($req);die;
        $res = RequestServer::getInstance()->doRequest('store/api/store/page',$req,'GET');

        if($res['code'] != 0){
            Response::setMsg($res['errMsg']);
            return false;
        }

        return [
            'list' => $res['data']['pageData'],
            'page' => $res['data']['currentPage'],
            'total' => $res['data']['totalRows'],
            'pageSize' => $res['data']['pageSize'],
            'totalPage' => $res['data']['totalPage'],
        ];
    }
}
