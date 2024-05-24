<?php

namespace App\Http\Server\Platform;

use App\Http\Server\BaseServer;
use App\Models\Platform\ChargingRecord;
use App\Utils\Tools;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChargingRecordServer extends BaseServer
{
    /**验证上报信息参数
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function chargingRecordValidator($data): \Illuminate\Contracts\Validation\Validator
    {
        $validate = Validator::make($data, [
            'startChargeSeq'    => 'required',
            'cellId'            => 'required',
            'operatorId'        => 'required',
            'startDatetime'     => 'required',
            'endDatetime'       => 'required',
            'totalPower'        => 'required',
            'totalElecMoney'    => 'required',
            'totalServiceMoney' => 'required',
            'startType'         => 'required',
            'stopReason'        => 'required',
            'averagePower'      => 'required',
        ], [
            'startChargeSeq.required'    => 'startChargeSeq不能为空',
            'cellId.required'            => 'cellId不能为空',
            'operatorId.required'        => 'operatorId不能为空',
            'startDatetime.required'     => 'startDatetime不能为空',
            'endDatetime.required'       => 'endDatetime不能为空',
            'totalPower.required'        => 'totalPower不能为空',
            'totalElecMoney.required'    => 'totalElecMoney不能为空',
            'totalServiceMoney.required' => 'totalServiceMoney不能为空',
            'startType.required'         => 'startType不能为空',
            'stopReason.required'        => 'stopReason不能为空',
            'averagePower.required'      => 'averagePower不能为空',
        ]);

        return $validate;
    }

    public function chargingRecordSubmit($params)
    {
        $insertData = Tools::snake($params);

        if($params['operatorId'] != Auth::$operatorId){
            Response::setMsg('运营商id不为授权运营商');
            return false;
        }

        #插入流水记录
        ChargingRecord::query()->insert($insertData);

        return '上报成功';
    }

    public function chargingRecordListSubmit($params)
    {
        $insertData = [];
        foreach ($params as $key => $value) {
            $insertData[] = Tools::snake($value);
        }

        if(ChargingRecord::query()->insert($insertData) === false) {
            return false;
        }

        return '上报成功';
    }

    public function chargingRecordList($params): array
    {
        $page = $params['pageIndex'] ?? 1;
        $pageSize = $params['pageSize'] ?? 10;
        $point = ($page - 1) * $pageSize;

        $query = ChargingRecord::query()
            ->where(['operator_id' => $params['operatorId']]);

        $total = $query->count();

        $list = $query->offset($point)->limit($pageSize)->get()->toArray();

        $newList = [];
        foreach ($list as $key => $value){
            $newList[] = Tools::camel($value);
        }

        $totalPage =  ceil($total/$pageSize);
        return [
            'totalRecord' => $total,
            'totalPage' => $totalPage,
            'pageList' => $newList,
        ];
    }
}
