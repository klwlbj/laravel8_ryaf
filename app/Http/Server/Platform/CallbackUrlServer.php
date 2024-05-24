<?php

namespace App\Http\Server\Platform;

use App\Http\Server\BaseServer;
use App\Models\Platform\NotifyUrl;
use App\Utils\Tools;
use Illuminate\Support\Str;

class CallbackUrlServer extends BaseServer
{
    public function updateCallbackUrl($params){
        $existId = NotifyUrl::query()
            ->where(['operator_id' => $params['operatorId'],'type' => $params['type']])
            ->whereNull('deleted_at')
            ->value('id');

        $insetData = Tools::snake($params);

        if(empty($existId)){
            #如果不存在则插入
            NotifyUrl::query()->insert($insetData);
        }else{
            #如果存在则更新
            if(NotifyUrl::query()->where(['id' => $existId])->update(['url' => $params['url']]) === false){
                Response::setMsg('修改失败');
                return false;
            }
        }

        return "修改成功!";
    }

    public function deleteCallbackUrl($params){
        $existId = NotifyUrl::query()
            ->where(['operator_id' => $params['operatorId'],'type' => $params['type']])
            ->value('id');

        if(empty($existId)){
            return "不存在回调地址，不需要删除!";
        }

        if(NotifyUrl::query()->where(['id' => $existId])->delete() === false){
            Response::setMsg('删除失败');
            return false;
        }

        return "删除成功!";
    }

    public function callbackData($data,$operatorId,$type){

    }
}
