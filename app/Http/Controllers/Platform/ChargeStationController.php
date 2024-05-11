<?php

namespace App\Http\Controllers\Platform;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Platform\ChargeStation;
use App\Http\Controllers\BaseController;

class ChargeStationController extends BaseController
{
    public function store(Request $request)
    {
        // 验证规则
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
            'stationType'      => 'required|integer',
            'stationStatus'    => 'required|integer',
            'chargingNums'     => 'required|integer',
            'stationLng'       => 'required|numeric',
            'stationLat'       => 'required|numeric',
            'canopy'           => 'required|integer',
            'camera'           => 'required|integer',
            'smokeSensation'   => 'required|integer',
            'fireControl'      => 'required|integer',
            'feeType'          => 'required|integer',
            'electricityFee'   => 'required|string',
            'serviceFee'       => 'required|string',
            'createDate'       => 'required|date_format:Y-m-d',
            'operationDate'    => 'required|date_format:Y-m-d',
        ];

        $input = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if($valicate){
            return $valicate;
        }

        // 创建新的充电站并插入数据库
        $station = new ChargeStation();

        foreach (array_keys($rules) as $key) {
            $station->$key = $input[$key];
        }

        $station->save();

        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => '提交成功！']);
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
            'stationType'      => 'required|integer',
            'stationStatus'    => 'required|integer',
            'chargingNums'     => 'required|integer',
            'stationLng'       => 'required|numeric',
            'stationLat'       => 'required|numeric',
            'canopy'           => 'required|integer',
            'camera'           => 'required|integer',
            'smokeSensation'   => 'required|integer',
            'fireControl'      => 'required|integer',
            'feeType'          => 'required|integer',
            'electricityFee'   => 'required|string',
            'serviceFee'       => 'required|string',
            'createDate'       => 'required|date_format:Y-m-d',
            'operationDate'    => 'required|date_format:Y-m-d',
        ];

        // 进行验证
        $input = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if($valicate){
            return $valicate;
        }

        $station = ChargeStation::where('station_id', $input['stationId'])
            ->where('operator_id', $input['operatorId'])
            ->first();

        if (!$station) {
            return response()->json(['error' => 'Station not found'], 404);
        }
        unset($input['stationId'], $input['operatorId']);
        foreach (array_keys($rules) as $key) {
            $station->$key = $input[$key];
        }

        // 保存更新后的站点信息
        $station->save();

        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => '提交成功！']);
    }

    public function destroy(Request $request)
    {
        $rules = [
            'stationId'  => 'required|string',
            'operatorId' => 'required|string',
        ];
        $input = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if($valicate){
            return $valicate;
        }

        $station = ChargeStation::where('station_id', $input['stationId'])
            ->where('operator_id', $input['operatorId'])
            ->first();

        if (!$station) {
            return response()->json(['message' => '不存在'], 404);
        }

        $station->delete();

        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => '删除成功！']);
    }

    public function index(Request $request)
    {
        $rules = [
            'pageIndex'  => 'required|integer',
            'pageSize'   => 'required|integer',
            'operatorId' => 'required|string',
        ];

        $input = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if($valicate){
            return $valicate;
        }

        $pageIndex = $input['pageIndex']; // 获取页面索引
        $pageSize  = $input['pageSize'];   // 获取页面大小
        $offset    = ($pageIndex - 1) * $pageSize; // 计算偏移量

        // 执行分页查询
        $items = ChargeStation::skip($offset)->take($pageSize)->where('operator_id', $input['operatorId'])->get();

        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => $items]);
    }
}
