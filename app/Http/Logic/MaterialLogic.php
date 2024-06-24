<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class MaterialLogic extends BaseLogic
{
    public function getList($params)
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;
        $point = ($page - 1) * $pageSize;

        $query = DB::connection('admin')->table('material')
            ->leftJoin('material_manufacturer','material.mate_manufacturer_id','=','material_manufacturer.mama_id')
            ->leftJoin('material_category','material.mate_category_id','=','material_category.maca_id')
            ->leftJoin('material_specification','material.mate_specification_id','=','material_specification.masp_id')
        ;

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('mate_name','like','%'.$params['keyword'].'%');
        }

        if(isset($params['category_id']) && $params['category_id']){
            $query->where(['material.mate_category_id' => $params['category_id']]);
        }

        if(isset($params['manufacturer_id']) && $params['manufacturer_id']){
            $query->where(['material.mate_manufacturer_id' => $params['manufacturer_id']]);
        }

        if(isset($params['specification_id']) && $params['specification_id']){
            $query->where(['material.mate_specification_id' => $params['specification_id']]);
        }

        $total = $query->count();

        $list = $query
            ->select([
                'material.*',
                'material_manufacturer.mama_name as mate_manufacturer_name',
                'material_category.maca_name as mate_category_name',
                'material_specification.masp_name as mate_specification_name'
            ])
            ->orderBy('mate_sort','desc')
            ->orderBy('mate_id','desc')
            ->offset($point)->limit($pageSize)->get()->toArray();

        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public function getAllList($params)
    {
        $query = DB::connection('admin')->table('material');

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('mate_id_name','like','%'.$params['keyword'].'%');
        }

        return $query
            ->orderBy('mate_sort','desc')
            ->orderBy('mate_id','desc')
            ->get()->toArray();
    }

    public function getInfo($params)
    {
        $data = DB::connection('admin')->table('material')->where(['mate_id' => $params['id']])->first();

        if(!$data){
            ResponseLogic::setMsg('记录不存在');
            return false;
        }

        return $data;
    }

    public function add($params)
    {
        $insertData = [
            'mate_manufacturer_id' => $params['manufacturer_id'],
            'mate_category_id' => $params['category_id'],
            'mate_specification_id' => $params['specification_id'],
            'mate_name' => $params['name'],
            'mate_number' => $params['number'] ?? 0,
            'mate_unit' => $params['unit'],
            'mate_warning' => $params['warning'] ?? 0,
            'mate_image' => $params['image'] ?? '',
            'mate_remark' => $params['remark'] ?? '',
            'mate_sort' => $params['sort'] ?? 0,
            'mate_status' => $params['status'] ?? 1,
            'mate_operator_id' => 2, #操作id  默认写死
        ];

        if(DB::connection('admin')->table('material')->where(['mate_name' => $params['name']])->exists()){
            ResponseLogic::setMsg('厂家名称已存在');
            return false;
        }

        $id = DB::connection('admin')->table('material')->insertGetId($insertData);
        if($id === false){
            ResponseLogic::setMsg('添加失败');
            return false;
        }

        return ['id' => $id];
    }

    public function update($params)
    {
        $insertData = [
            'mate_manufacturer_id' => $params['manufacturer_id'],
            'mate_category_id' => $params['category_id'],
            'mate_specification_id' => $params['specification_id'],
            'mate_name' => $params['name'],
            'mate_unit' => $params['unit'],
            'mate_warning' => $params['warning'],
            'mate_image' => $params['image'] ?? '',
            'mate_remark' => $params['remark'] ?? '',
            'mate_sort' => $params['sort'] ?? 0,
            'mate_status' => $params['status'] ?? 1,
            'mate_operator_id' => 2, #操作id  默认写死
        ];

        if(DB::connection('admin')->table('material')->where('mate_id','<>',$params['id'])->where(['mate_name' => $params['name']])->exists()){
            ResponseLogic::setMsg('厂家名称已存在');
            return false;
        }

        if(DB::connection('admin')->table('material')->where(['mate_id' => $params['id']])->update($insertData) === false){
            ResponseLogic::setMsg('更新失败');
            return false;
        }

        return [];
    }

    public function delete($params)
    {
        if(DB::connection('admin')->table('material')->where(['mate_id' => $params['id']])->where('number','>',0)->exists()){
            ResponseLogic::setMsg('该物品存在库存，请把库存出库后再删除');
            return false;
        }

        DB::connection('admin')->table('material')->where(['mate_id' => $params['id']])->delete();
        return [];
    }
}
