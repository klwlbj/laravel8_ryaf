<?php

namespace App\Http\Server\Platform;

use App\Http\Server\BaseServer;
use App\Models\Platform\CellInfo;
use App\Models\Platform\CellInfoLog;
use Illuminate\Support\Facades\Validator;

class ReportServer extends BaseServer
{
    /**验证上报信息参数
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function cellInfoValidator($data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'cellId'         => 'required',
            'equipmentId'    => 'required',
            'operatorId'     => 'required',
            'updateDatetime' => 'required',
            'cellStatus'     => 'required',
            'doorStatus'     => 'required',
            'errorCode'      => 'required',
            'current'        => 'required',
            'voltage'        => 'required',
            'power'          => 'required',
            'quantity'       => 'required',
            'envTemperature' => 'required',
            'cirTemperature' => 'required',
        ], [
            'cellId.required'         => 'cellId不能为空',
            'equipmentId.required'    => 'equipmentId不能为空',
            'operatorId.required'     => 'chargingMetaInfoId不能为空',
            'updateDatetime.required' => 'updateDatetime不能为空',
            'cellStatus.required'     => 'cellStatus不能为空',
            'doorStatus.required'     => 'doorStatus不能为空',
            'errorCode.required'      => 'errorCode不能为空',
            'current.required'        => 'current不能为空',
            'voltage.required'        => 'voltage不能为空',
            'power.required'          => 'power不能为空',
            'quantity.required'       => 'quantity不能为空',
            'envTemperature.required' => 'envTemperature不能为空',
            'cirTemperature.required' => 'cirTemperature不能为空',
        ]);
    }

    public function cellInfoReport($params): string
    {
        if($params['operatorId'] != Auth::$operatorId){
            Response::setMsg('运营商id不为授权运营商');
            return false;
        }

        $existId = CellInfo::query()
            ->where([
                'operator_id'  => $params['operatorId'],
                'equipment_id' => $params['equipmentId'],
                'cell_id'      => $params['cellId'],
            ])->whereNull('deleted_at')->value('id');

        $insertData = [
            'cell_id'         => $params['cellId'],
            'equipment_id'    => $params['equipmentId'],
            'operator_id'     => $params['operatorId'],
            'update_datetime' => $params['updateDatetime'],
            'cell_status'     => $params['cellStatus'],
            'door_status'     => $params['doorStatus'],
            'error_code'      => $params['errorCode'],
            'current'         => $params['current'],
            'voltage'         => $params['voltage'],
            'power'           => $params['power'],
            'quantity'        => $params['quantity'],
            'env_temperature' => $params['envTemperature'],
            'cir_temperature' => $params['cirTemperature'],
        ];

        if(isset($params['residualCurrent']) && !empty($params['residualCurrent'])) {
            $insertData['residual_current'] = $params['residualCurrent'];
        }

        if(isset($params['startChargeSeq']) && !empty($params['startChargeSeq'])) {
            $insertData['start_charge_seq'] = $params['startChargeSeq'];
        }

        if(empty($existId)) {
            #没有记录则插入
            CellInfo::query()->insert($insertData);
        } else {
            #有记录则更新最新信息
            CellInfo::query()->where(['id' => $existId])->update($insertData);
        }

        #插入流水记录
        CellInfoLog::query()->insert($insertData);

        return '上报成功';
    }
}
