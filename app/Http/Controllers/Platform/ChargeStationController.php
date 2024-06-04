<?php

namespace App\Http\Controllers\Platform;

use App\Rules\EnumValueRule;
use Illuminate\Http\Request;
use App\Models\Platform\ChargeStation;

class ChargeStationController extends BaseChargeController
{
    public function store(Request $request)
    {
        $rules = [
            'stationId'        => 'required|string|unique:charge_stations,station_id',
            'operatorId'       => 'required|string',
            'equipmentOwnerId' => 'required|string',
            'stationName'      => 'required|string',
            'areaCode'         => 'required|string',
            'district'         => 'required|string',
            'street'           => 'required|string',
            'community'        => 'required|string',
            'village'          => 'required|string',
            'address'          => 'required|string',
            'serviceTel'       => 'required|string',
            'stationType'      => ['required', 'integer', new EnumValueRule(array_keys(ChargeStation::$formatTypeMaps))],
            'stationStatus'    => ['required', 'integer', new EnumValueRule(array_keys(ChargeStation::$formatStatusMaps))],
            'chargingNums'     => 'required|integer',
            'stationLng'       => 'required|numeric|between:-180,180',
            'stationLat'       => 'required|numeric|between:-90,90',
            'canopy'           => 'required|integer',
            'camera'           => 'required|integer',
            'smokeSensation'   => 'required|integer',
            'fireControl'      => 'required|integer',
            'feeType'          => ['required', 'integer', new EnumValueRule(array_keys(ChargeStation::$formatFeeTypeMaps))],
            'electricityFee'   => 'required|string',
            'serviceFee'       => 'required|string',
            'createDate'       => 'required|date_format:Y-m-d',
            'operationDate'    => 'required|date_format:Y-m-d',
        ];
        return parent::baseStore($request, new ChargeStation(), $rules, 'stationId');
    }

    public function update(Request $request)
    {
        $rules = [
            'stationId'        => 'required|string',
            'operatorId'       => 'required|string',
            'equipmentOwnerId' => 'required|string',
            'stationName'      => 'required|string',
            'areaCode'         => 'required|string',
            'district'         => 'required|string',
            'street'           => 'required|string',
            'community'        => 'required|string',
            'village'          => 'required|string',
            'address'          => 'required|string',
            'serviceTel'       => 'required|string',
            'stationType'      => ['required', 'integer', new EnumValueRule(array_keys(ChargeStation::$formatTypeMaps))],
            'stationStatus'    => ['required', 'integer', new EnumValueRule(array_keys(ChargeStation::$formatStatusMaps))],
            'chargingNums'     => 'required|integer',
            'stationLng'       => 'required|numeric|between:-180,180',
            'stationLat'       => 'required|numeric|between:-90,90',
            'canopy'           => 'required|integer',
            'camera'           => 'required|integer',
            'smokeSensation'   => 'required|integer',
            'fireControl'      => 'required|integer',
            'feeType'          => ['required', 'integer', new EnumValueRule(array_keys(ChargeStation::$formatFeeTypeMaps))],
            'electricityFee'   => 'required|string',
            'serviceFee'       => 'required|string',
            'createDate'       => 'required|date_format:Y-m-d',
            'operationDate'    => 'required|date_format:Y-m-d',
        ];

        return parent::baseUpdate($request, new ChargeStation(), $rules, 'stationId');
    }

    public function destroy(Request $request)
    {
        return parent::baseDelete($request, new ChargeStation());
    }

    public function index(Request $request)
    {
        return parent::baseIndex($request, new ChargeStation());
    }
}
