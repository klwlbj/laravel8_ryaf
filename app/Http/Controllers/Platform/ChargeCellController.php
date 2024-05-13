<?php

namespace App\Http\Controllers\Platform;

use App\Rules\EnumValueRule;
use Illuminate\Http\Request;
use App\Rules\ExistsInTableRule;
use App\Models\Platform\ChargeCell;

class ChargeCellController extends BaseChargeController
{
    public function store(Request $request)
    {
        $rules = [
            'cellId'                 => 'required|string|unique:charge_cells,cell_id',
            'equipmentId'            => ['required', new ExistsInTableRule('charge_equipments', 'equipment_id')],
            'operatorId'             => 'required|string',
            'cellType'               => 'required|integer|in:1,2,3,4,5',
            'cellStandard'           => 'required|string',
            'ratedVoltageUpperLimit' => 'required|numeric',
            'ratedVoltageLowerLimit' => 'required|numeric',
            'ratedCurrent'           => 'required|numeric',
            'ratedPower'             => 'required|numeric',
            'electricityFee'         => 'required|string',
            'serviceFee'             => 'required|string',
            'fireControl'            => 'required|integer|in:1,2,3',
            'smokeSensation'         => 'required|integer|in:1,2,3',
        ];

        return parent::baseStore($request, new ChargeCell(), $rules);
    }

    public function update(Request $request)
    {
        $rules = [
            'cellId'                 => 'required|string',
            'equipmentId'            => ['required', new ExistsInTableRule('charge_equipments', 'equipment_id')],
            'operatorId'             => 'required|string',
            'cellType'               => ['required', 'integer', new EnumValueRule(array_keys(ChargeCell::$formatEquipmentTypeMaps))],
            'cellStandard'           => 'required|string',
            'ratedVoltageUpperLimit' => 'required|numeric',
            'ratedVoltageLowerLimit' => 'required|numeric',
            'ratedCurrent'           => 'required|numeric',
            'ratedPower'             => 'required|numeric',
            'electricityFee'         => 'required|string',
            'serviceFee'             => 'required|string',
            'fireControl'            => ['required', 'integer', new EnumValueRule(array_keys(ChargeCell::$formatWhetherOrNotMaps))],
            'smokeSensation'         => ['required', 'integer', new EnumValueRule(array_keys(ChargeCell::$formatWhetherOrNotMaps))],
        ];

        return parent::baseUpdate($request, new ChargeCell(), $rules, 'cellId', 'equipmentId');
    }

    public function destroy(Request $request)
    {
        return parent::baseDelete($request, new ChargeCell(), 'cellId');
    }

    public function index(Request $request)
    {
        return parent::baseIndex($request, new ChargeCell());
    }
}
