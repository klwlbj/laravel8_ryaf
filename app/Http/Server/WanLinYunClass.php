<?php

namespace App\Http\Server;

use App\Models\wlyDevice;
use Illuminate\Support\Facades\DB;
use App\Models\wlyDeviceChangeLogs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WanLinYunClass
{
    public array $rule = [
        'deviceCode'   => 'required|string|min:1',
        'deviceType'   => 'required|string|in:603',
        'deviceStatus' => 'required|in:1,2,3',
        'netStatus'    => 'required|in:1,2,3',
        'time'         => 'required|int',
        'content'      => 'nullable|string',
        'detail'       => 'required|array',
    ];

    public function common(array $params)
    {
        /*// 定义验证规则
        $rules = array_merge($this->rule, [
            'detail.type'             => 'required|in:0301,0302,0303',
            'detail.output_model_A'   => 'in:1,2', // 1非联动 2联动继电器
            'detail.output_model_B'   => 'in:1,2',
            'detail.output_model_C'   => 'in:1,2',
            'detail.input_type_A'     => 'in:0,1', // 0断开 1连接
            'detail.input_type_B'     => 'in:0,1',
            'detail.input_type_C'     => 'in:0,1',
            'detail.output_type_A'    => 'in:0,1', // 0关 1开
            'detail.output_type_B'    => 'in:0,1',
            'detail.output_type_C'    => 'in:0,1',
            'detail.temperature'      => 'numeric', // 温度 单位：摄氏度
            'detail.signal_intensity' => 'numeric', // 信号强度 单位：dbm
            'detail.client_id'        => 'string',

            'detail.event'            => 'string',
            'detail.loop'             => 'string',

            'detail.iccid'            => 'string',
        ]);*/

        // $this->validateParams($params, $rules);

        return $this->handleAndSaveParams($params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function heartbeat(array $params)
    {
        // 定义验证规则
        $rules = array_merge($this->rule, [
            'detail.type'             => 'required|in:0301',
            'detail.output_model_A'   => 'required|in:1,2', // 1非联动 2联动继电器
            'detail.output_model_B'   => 'required|in:1,2',
            'detail.output_model_C'   => 'required|in:1,2',
            'detail.input_type_A'     => 'required|in:0,1', // 0断开 1连接
            'detail.input_type_B'     => 'required|in:0,1',
            'detail.input_type_C'     => 'required|in:0,1',
            'detail.output_type_A'    => 'required|in:0,1', // 0关 1开
            'detail.output_type_B'    => 'required|in:0,1',
            'detail.output_type_C'    => 'required|in:0,1',
            'detail.temperature'      => 'required|numeric', // 温度 单位：摄氏度
            'detail.signal_intensity' => 'required|numeric', // 信号强度 单位：dbm
            'detail.client_id'        => 'required|string',
        ]);

        $this->validateParams($params, $rules);

        return $this->handleAndSaveParams($params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function event(array $params)
    {
        // 定义验证规则
        $rules = array_merge($this->rule, [
            'detail.type'           => 'required|in:0302',
            'detail.output_model_A' => 'required|in:1,2', // 1非联动 2联动继电器
            'detail.output_model_B' => 'required|in:1,2',
            'detail.output_model_C' => 'required|in:1,2',
            'detail.input_type_A'   => 'required|in:0,1', // 0断开 1连接
            'detail.input_type_B'   => 'required|in:0,1',
            'detail.input_type_C'   => 'required|in:0,1',
            'detail.output_type_A'  => 'required|in:0,1', // 0关 1开
            'detail.output_type_B'  => 'required|in:0,1',
            'detail.output_type_C'  => 'required|in:0,1',
            'detail.client_id'      => 'required|string',
            'detail.event'          => 'required|string',
            'detail.loop'           => 'required|string',
        ]);

        $this->validateParams($params, $rules);

        return $this->handleAndSaveParams($params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function offline(array $params)
    {
        // 定义验证规则
        $rules = array_merge($this->rule, [
            'detail.type'      => 'required|in:0302',
            'detail.client_id' => 'required|string',
            'detail.event'     => 'required|string|in:offline',
        ]);

        $this->validateParams($params, $rules);

        return $this->handleAndSaveParams($params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function iccid(array $params)
    {
        // 定义验证规则
        $rules = array_merge($this->rule, [
            'detail.type'      => 'required|in:0303',
            'detail.client_id' => 'required|string',
            'detail.iccid'     => 'required|string',
        ]);

        $this->validateParams($params, $rules);

        return $this->handleAndSaveParams($params);
    }

    /**
     * @param array $params
     * @param array $rules
     * @return void
     * @throws \Exception
     */
    private function validateParams(array $params, array $rules)
    {
        // 创建验证器
        $validator = Validator::make($params, $rules);

        // 检查验证结果
        if ($validator->fails()) {
            throw new \Exception($validator->errors());
        }
    }

    /**
     * @param array $params
     * @return mixed
     */
    private function handleAndSaveParams(array $params)
    {
        // 打印日志
        $json      = json_encode($params);
        $timestamp = now();
        Log::info("Array: {$json} | Timestamp: {$timestamp}");
        // 暂时直接返回
        return [];

        /*// 获取参数值
        $deviceCode = $params['deviceCode'];

        // 获取 detail 参数值
        $params['time']    = date('Y-m-d H:i:s', $params['time']);
        $params['content'] = $params['content'] ?? '';
        $detail            = $params['detail'];
        unset($params['detail'], $params['deviceCode']);

        $newParams = array_merge($detail, $params);

        // 插入数据库
        return DB::transaction(function () use ($newParams, $deviceCode) {
            $data = WlyDevice::updateOrCreate(
                ['device_code' => $deviceCode],
                $newParams
            );
            WlyDeviceChangeLogs::create(array_merge($newParams, ['device_code' => $deviceCode]));
            return $data->toArray();
        }, 3);*/
    }
}
