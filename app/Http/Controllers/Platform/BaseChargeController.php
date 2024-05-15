<?php

namespace App\Http\Controllers\Platform;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class BaseChargeController extends BaseController
{
    public function baseIndex(Request $request, $model)
    {
        $rules = [
            'pageIndex'  => 'required|integer',
            'pageSize'   => 'required|integer',
            'operatorId' => 'required|string',
        ];

        $input    = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if ($valicate) {
            return $valicate;
        }

        $pageIndex = $input['pageIndex']; // 获取页面索引
        $pageSize  = $input['pageSize'];   // 获取页面大小
        $offset    = ($pageIndex - 1) * $pageSize; // 计算偏移量

        // 执行分页查询
        $items = $model::skip($offset)->take($pageSize)
            ->where('operator_id', $input['operatorId'])
            ->select('*')
            ->get();
        // 查询总记录数
        $totalRecord = $model::where('operator_id', $input['operatorId'])->count();

        // 计算总页数
        $totalPage = ceil($totalRecord / $pageSize);

        $camelCaseRecords = $items->map(function ($item) {
            return collect($item)->mapWithKeys(function ($value, $key) {
                return [Str::camel($key) => $value];
            });
        });

        return response()->json(
            [
                'status'  => 200,
                'message' => '请求成功!',
                'data'    => [
                    'totalRecord' => $totalRecord,
                    'totalPage'   => $totalPage,
                    'pageList'    => $camelCaseRecords],
            ]
        );
    }

    public function baseDelete(Request $request, $model, string $idString = 'stationId')
    {
        $rules = [
            $idString    => 'required|string',
            'operatorId' => 'required|string',
        ];
        $input    = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if ($valicate) {
            return $valicate;
        }

        $item = $model::where(Str::snake($idString), $input[$idString])
            ->where('operator_id', $input['operatorId'])
            ->first();

        if (!$item) {
            return response()->json(['message' => '不存在'], 404);
        }

        $item->delete();

        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => '删除成功！']);
    }

    public function baseStore(Request $request, $model, array $rules)
    {
        $input    = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if ($valicate) {
            return $valicate;
        }

        foreach (array_keys($rules) as $key) {
            $model->{$key} = $input[$key];
        }

        $model->save();

        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => '提交成功！']);
    }

    public function baseUpdate(Request $request, $model, array $rules, string $idString, string $parentIdString = '')
    {
        // 进行验证
        $input    = [];
        $valicate = $this->validateParams($request, $rules, $input);
        if ($valicate) {
            return $valicate;
        }

        $item = $model::where(Str::snake($idString), $input[$idString])
            ->where('operator_id', $input['operatorId'])
            ->when(!empty($parentIdString), function ($query) use ($input, $parentIdString) {
                return $query->where(Str::snake($parentIdString), $input[$parentIdString]);
            })
            ->first();

        if (!$item) {
            return response()->json(['message' => '不存在'], 404);
        }
        unset($input[$idString], $input[$parentIdString], $input['operatorId']);
        foreach (array_keys($rules) as $key) {
            if (isset($input[$key])) {
                $item->{$key} = $input[$key];
            }
        }

        // 保存
        $item->save();
        return response()->json(['status' => 200, 'message' => '请求成功!', 'data' => '提交成功！']);
    }
}
