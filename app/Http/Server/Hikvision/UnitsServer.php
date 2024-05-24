<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;
use Illuminate\Support\Facades\Validator;

class UnitsServer extends BaseServer
{
    public function verifyParams($params)
    {
        $validate = Validator::make($params, [
            'units'         => 'required',
        ], [
            'units.required'         => 'units不能为空',
        ]);

        if($validate->fails())
        {
            Response::setMsg($validate->errors()->first());
            return false;
        }

        foreach ($params['units'] as $key => $value){
            $itemValidate = Validator::make($value, [
                'unitId'         => 'required',
                'unitName'         => 'required',
                'creditCode'         => 'required',
                'regionCode'         => 'required',
                'createTime'         => 'required',
                'updateTime'         => 'required',
            ], [
                'unitId.required'         => 'unitId不能为空',
                'unitName.required'         => 'unitName不能为空',
                'creditCode.required'         => 'creditCode不能为空',
                'regionCode.required'         => 'regionCode不能为空',
                'createTime.required'         => 'createTime不能为空',
                'updateTime.required'         => 'updateTime不能为空',
            ]);

            if($itemValidate->fails())
            {
                Response::setMsg('第' . ($key + 1) . '条数据有误：' . $itemValidate->errors()->first());
                return false;
            }
        }

        return true;
    }
    public function add($params)
    {
        $res = RequestServer::getInstance()->doRequest('fire/v1/units/add',$params);
        print_r($res);die;
    }

    public function update($params)
    {

    }
}
