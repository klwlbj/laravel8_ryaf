<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;
use App\Utils\Tools;
use Illuminate\Support\Facades\Validator;

class UnitsServer extends BaseServer
{
    public $resourceCode = '309000';

    /**统一社会信用代码
     * @param $id
     * @param $regionCode
     * @return string
     */
    public function getCreditCode($id, $regionCode): string
    {
        #生成统一社会信用代码
        return "11" . $regionCode . str_pad($id, 10, '0', STR_PAD_LEFT);
    }

    public function getUnitsId($creditCode)
    {
        return "9423" . $this->resourceCode . "00" . $creditCode;
    }

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
        $id = '114111';
        $regionCode = '440111';
        $creditCode = $this->getCreditCode($id,$regionCode);
        $unitId = $this->getUnitsId($creditCode);

        $params = [
            'unitId' => $unitId,
            'unitName' => '白云区石井街道庆丰社区庆丰忠和里街75号101室',
            'address' => '白云区石井街道庆丰社区庆丰忠和里街75号101室',
            'pointX' => '113.224015',
            'pointY' => '23.212011',
            'creditCode' => $creditCode,
            'regionCode' => $regionCode,
        ];


        $date = Tools::getISO8601Date();
        $params['createTime'] = $date;
        $params['updateTime'] = $date;
//        print_r($params);die;
        $data = [
            'units' => [
                $params
            ]
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/units/add',$data);
    }

    public function update($params)
    {
        $id = '114111';
        $regionCode = '440111';
        $creditCode = $this->getCreditCode($id,$regionCode);
        $unitId = $this->getUnitsId($creditCode);

        $params = [
            'unitId' => $unitId,
            'unitName' => '白云区石井街道庆丰社区庆丰忠和里街75号101室',
            'address' => '白云区石井街道庆丰社区庆丰忠和里街75号101室',
            'pointX' => '113.224015',
            'pointY' => '23.212011',
            'creditCode' => $creditCode,
            'regionCode' => $regionCode,
        ];


        $date = Tools::getISO8601Date();
        $params['createTime'] = $date;
        $params['updateTime'] = $date;
//        print_r($params);die;
        $data = [
            'units' => [
                $params
            ]
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/units/update',$data);
    }

    public function delete($params)
    {
        $params['id'] = '114111';
        $regionCode = '440111';

        $creditCode = $this->getCreditCode($params['id'],$regionCode);
        $unitId = $this->getUnitsId($creditCode);
        $data = [
            'unitIds' => [
                $unitId
            ]
        ];
//        print_r($data);die;
        return RequestServer::getInstance()->doRequest('fire/v1/units/delete',$data);
    }
}
