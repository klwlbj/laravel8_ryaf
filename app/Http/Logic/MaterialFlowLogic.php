<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class MaterialFlowLogic extends BaseLogic
{
    public function getList($params)
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;
        $point = ($page - 1) * $pageSize;

        $query = DB::connection('admin')->table('material_flow')
            ->leftJoin('material','material_flow.mafl_material_id','=','material.mate_id')
            ->leftJoin('admin as receive_user','material_flow.mafl_receive_user_id','=','receive_user.admin_id')
            ->leftJoin('admin','material_flow.mafl_operator_id','=','admin.admin_id')
        ;

        if(isset($params['material_id']) && $params['material_id']){
            $query->where(['material_flow.mafl_material_id' => $params['material_id']]);
        }

        if(isset($params['type']) && $params['type']){
            $query->where(['material_flow.mafl_type' => $params['type']]);
        }

        $total = $query->count();

        $list = $query
            ->select([
                'material_flow.*',
                'material.mate_name as mafl_material_name',
                'admin.admin_name as mafl_created_user',
                'receive_user.admin_name as mafl_receive_user',
            ])
            ->orderBy('material_flow.mafl_id','desc')
            ->offset($point)->limit($pageSize)->get()->toArray();

        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public function inComing($params)
    {
        $materialData = DB::connection('admin')->table('material')
            ->where(['mate_id' => $params['material_id']])->select(['mate_id','mate_number'])->first();

        if(!$materialData){
            ResponseLogic::setMsg('物品数据不存在');
            return false;
        }

        $incomingData = [
            'mafl_material_id'  => $params['material_id'],
            'mafl_type' => 1,
            'mafl_number' => $params['number'],
            'mafl_production_date' => $params['production_date'],
            'mafl_expire_date' => $params['expire_date'],
            'mafl_date' => $params['date'],
            'mafl_remark' => $params['remark'] ?? '',
            'mafl_operator_id' => 2 #操作人 默认写死2
        ];

        DB::connection('admin')->beginTransaction();

        #插入库存流水
        $flowId = DB::connection('admin')->table('material_flow')->insertGetId($incomingData);
        #变更物品库存数量
        DB::connection('admin')->table('material')->where(['mate_id' => $params['material_id']])->update(['mate_number' => DB::raw("mate_number+".$params['number'])]);

        $detailInsert = [];

        for ($i = 0; $i < $params['number']; $i++) {
            $detailInsert[] = [
                'made_material_id' => $params['material_id'],
                'made_in_id' => $flowId,
                'made_production_date' => $params['production_date'],
                'made_expire_date' => $params['expire_date'],
                'made_date' => $params['date'],
                'made_status' => 1,
            ];
        }

        DB::connection('admin')->table('material_detail')->insert($detailInsert);

        DB::connection('admin')->commit();

        return ['id' => $flowId];
    }

    public function outComing($params)
    {
        $materialData = DB::connection('admin')->table('material')
            ->where(['mate_id' => $params['material_id']])->select(['mate_id','mate_number'])->first();

        if(!$materialData){
            ResponseLogic::setMsg('物品数据不存在');
            return false;
        }


        if($materialData->mate_number < $params['number']){
            ResponseLogic::setMsg('库存不足，当前库存：' . $materialData->mate_number);
            return false;
        }

        $outComingData = [
            'mafl_material_id'  => $params['material_id'],
            'mafl_type' => 2,
            'mafl_number' => $params['number'],
            'mafl_purpose' => $params['purpose'],
            'mafl_receive_user_id' => $params['receive_user_id'],
            'mafl_approve_image' => $params['approve_image'] ?? '',
            'mafl_date' => $params['date'],
            'mafl_remark' => $params['remark'] ?? '',
            'mafl_operator_id' => 2 #操作人 默认写死2
        ];

        DB::connection('admin')->beginTransaction();

        #插入库存流水
        $flowId = DB::connection('admin')->table('material_flow')->insertGetId($outComingData);
        #变更物品库存数量
        DB::connection('admin')->table('material')->where(['mate_id' => $params['material_id']])->update(['mate_number' => DB::raw("mate_number-".$params['number'])]);
        #把物品变更成出库状态
        DB::connection('admin')->table('material_detail')
            ->where(['made_material_id' => $params['material_id']])
            ->orderBy('made_id','asc')
            ->limit($params['number'])
            ->update(['made_out_id' => $flowId,'made_status' => 2]);


        DB::connection('admin')->commit();

        return [];
    }
}
