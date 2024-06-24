<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class MaterialCategoryLogic extends BaseLogic
{
    public function getList($params)
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;
        $point = ($page - 1) * $pageSize;

        $query = DB::connection('admin')->table('material_category');

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('maca_name','like','%'.$params['keyword'].'%');
        }

        if(isset($params['is_deliver']) && $params['is_deliver']){
            $query->where(['maca_is_deliver' => $params['is_deliver']]);
        }

        $total = $query->count();

        $list = $query
            ->orderBy('maca_sort','desc')
            ->orderBy('maca_id','desc')
            ->offset($point)->limit($pageSize)->get()->toArray();

        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public function getAllList($params)
    {
        $query = DB::connection('admin')->table('material_category');

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('maca_name','like','%'.$params['keyword'].'%');
        }

        return $query
            ->orderBy('maca_id','desc')
            ->get()->toArray();
    }

    public function getInfo($params)
    {
        $data = DB::connection('admin')->table('material_category')->where(['maca_id' => $params['id']])->first();

        if(!$data){
            ResponseLogic::setMsg('记录不存在');
            return false;
        }

        return $data;
    }

    public function add($params)
    {
        $insertData = [
            'maca_name' => $params['name'],
            'maca_sort' => $params['sort'] ?? 0,
            'maca_remark' => $params['remark'] ?? '',
            'maca_status' => $params['status'] ?? 1,
        ];

        if(DB::connection('admin')->table('material_category')->where(['maca_name' => $params['name']])->exists()){
            ResponseLogic::setMsg('厂家名称已存在');
            return false;
        }

        $id = DB::connection('admin')->table('material_category')->insertGetId($insertData);
        if($id === false){
            ResponseLogic::setMsg('添加失败');
            return false;
        }

        return ['id' => $id];
    }

    public function update($params)
    {
        $insertData = [
            'maca_name' => $params['name'],
            'maca_sort' => $params['sort'] ?? 0,
            'maca_remark' => $params['remark'] ?? '',
            'maca_status' => $params['status'] ?? 1
        ];

        if(DB::connection('admin')->table('material_category')->where('maca_id','<>',$params['id'])->where(['maca_name' => $params['name']])->exists()){
            ResponseLogic::setMsg('厂家名称已存在');
            return false;
        }

        if(DB::connection('admin')->table('material_category')->where(['maca_id' => $params['id']])->update($insertData) === false){
            ResponseLogic::setMsg('更新失败');
            return false;
        }

        return [];
    }

    public function delete($params)
    {
        if(DB::connection('admin')->table('material')->where(['mate_category_id',$params['id']])->exists()){
            ResponseLogic::setMsg('该分类下存在物品，请删除物品后再删除分类');
            return false;
        }
        DB::connection('admin')->table('material_category')->where(['maca_id' => $params['id']])->delete();

        DB::connection('admin')->table('material_specification')->where(['masp_category_id' => $params['id']])->delete();
        return [];
    }
}
