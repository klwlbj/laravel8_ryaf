<?php

namespace App\Http\Controllers\Platform;

use App\Rules\EnumValueRule;
use Illuminate\Http\Request;
use App\Rules\ExistsInTableRule;
use App\Models\Platform\ChargeEquipment;

class ChargeEquipmentController extends BaseChargeController
{
    public function store(Request $request)
    {
        $rules = [
            'equipmentId'            => 'required|string|unique:charge_equipments,equipment_id',
            'stationId'              => ['required', new ExistsInTableRule('charge_stations', 'station_id')],
            'operatorId'             => 'required|string',
            'equipmentName'          => 'required|string',
            'manufacturerBrand'      => 'required|string',
            'equipmentModel'         => 'required|string',
            'manufacturerId'         => 'required|string',
            'productionDate'         => 'required|date',
            'equipmentType'          => ['required', 'integer', new EnumValueRule(array_keys(ChargeEquipment::$formatEquipmentTypeMaps))],
            'equipmentCategory'      => ['required', 'integer', new EnumValueRule(array_keys(ChargeEquipment::$formatEquipmentCategoryMaps))],
            'validateConnectorCount' => 'required|integer',
            'ratedVoltage'           => 'required|numeric|between:0,99.9',
            'ratedCurrent'           => 'required|numeric|between:0,99.9',
            'ratedPower'             => 'required|numeric|between:0,99.9',
            'equipmentLng'           => 'required|numeric|between:-180.000000,180.000000',
            'equipmentLat'           => 'required|numeric|between:-90.000000,90.000000',
            'operationDate'          => 'required|date',
            'camera'                 => ['required', 'integer', new EnumValueRule(array_keys(ChargeEquipment::$formatWhetherOrNotMaps))],
        ];

        return parent::baseStore($request, new ChargeEquipment(), $rules);
    }

    public function update(Request $request)
    {
        $rules = [
            'equipmentId'            => 'required|string',
            'stationId'              => ['required', new ExistsInTableRule('charge_stations', 'station_id')],
            'operatorId'             => 'required|string',
            'equipmentName'          => 'required|string',
            'manufacturerBrand'      => 'required|string',
            'equipmentModel'         => 'required|string',
            'manufacturerId'         => 'required|string',
            'productionDate'         => 'required|date',
            'equipmentType'          => ['required', 'integer', new EnumValueRule(array_keys(ChargeEquipment::$formatEquipmentTypeMaps))],
            'equipmentCategory'      => ['required', 'integer', new EnumValueRule(array_keys(ChargeEquipment::$formatEquipmentCategoryMaps))],
            'validateConnectorCount' => 'required|integer',
            'ratedVoltage'           => 'required|numeric|between:0,99.9',
            'ratedCurrent'           => 'required|numeric|between:0,99.9',
            'ratedPower'             => 'required|numeric|between:0,99.9',
            'equipmentLng'           => 'required|numeric|between:-180.000000,180.000000',
            'equipmentLat'           => 'required|numeric|between:-90.000000,90.000000',
            'operationDate'          => 'required|date',
            'camera'                 => ['required', 'integer', new EnumValueRule(array_keys(ChargeEquipment::$formatWhetherOrNotMaps))],
        ];

        return parent::baseUpdate($request, new ChargeEquipment(), $rules, 'equipmentId', 'stationId');
    }

    public function destroy(Request $request)
    {
        return parent::baseDelete($request, new ChargeEquipment(), 'equipmentId');
    }

    public function index(Request $request)
    {
        return parent::baseIndex($request, new ChargeEquipment());
    }
}
