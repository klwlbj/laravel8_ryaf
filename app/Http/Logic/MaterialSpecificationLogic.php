<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class MaterialSpecificationLogic extends BaseLogic
{
    public function getList($params)
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;
        $point = ($page - 1) * $pageSize;

        $query = DB::connection('admin')->table('material_specification')
            ->leftJoin('material_category','material_category.maca_id','=','material_specification.masp_category_id')
        ;

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('material_specification.masp_name','like','%'.$params['keyword'].'%');
        }

        if(isset($params['category_id']) && $params['category_id']){
            $query->where('material_specification.masp_category_id','=',$params['category_id']);
        }

        $total = $query->count();

        $list = $query
            ->select(['material_specification.*','material_category.maca_name as masp_category_name'])
            ->orderBy('material_specification.masp_sort','desc')
            ->orderBy('material_specification.masp_id','desc')
            ->offset($point)->limit($pageSize)->get()->toArray();

        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public function getAllList($params)
    {
        $query = DB::connection('admin')->table('material_specification');

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('masp_name','like','%'.$params['keyword'].'%');
        }

        if(isset($params['category_id']) && $params['category_id']){
            $query->where(['masp_category_id' => $params['category_id']]);
        }

        return $query
            ->orderBy('masp_sort','desc')
            ->orderBy('masp_id','desc')
            ->get()->toArray();
    }

    public function getInfo($params)
    {
        $data = DB::connection('admin')->table('material_specification')->where(['masp_id' => $params['id']])->first();

        if(!$data){
            ResponseLogic::setMsg('记录不存在');
            return false;
        }

        return $data;
    }

    public function add($params)
    {
        $insertData = [
            'masp_name' => $params['name'],
            'masp_category_id' => $params['category_id'],
            'masp_sort' => $params['sort'] ?? 0,
            'masp_status' => $params['status'] ?? 1,
        ];

        if(DB::connection('admin')->table('material_specification')->where(['masp_name' => $params['name']])->exists()){
            ResponseLogic::setMsg('厂家名称已存在');
            return false;
        }

        $id = DB::connection('admin')->table('material_specification')->insertGetId($insertData);
        if($id === false){
            ResponseLogic::setMsg('添加失败');
            return false;
        }

        return ['id' => $id];
    }

    public function update($params)
    {
        $insertData = [
            'masp_name' => $params['name'],
            'masp_category_id' => $params['category_id'],
            'masp_sort' => $params['sort'] ?? 0,
            'masp_status' => $params['status'] ?? 1,
        ];

        if(DB::connection('admin')->table('material_specification')->where('masp_id','<>',$params['id'])->where(['masp_name' => $params['name']])->exists()){
            ResponseLogic::setMsg('厂家名称已存在');
            return false;
        }

        if(DB::connection('admin')->table('material_specification')->where(['masp_id' => $params['id']])->update($insertData) === false){
            ResponseLogic::setMsg('更新失败');
            return false;
        }

        return [];
    }

    public function delete($params)
    {
        if(DB::connection('admin')->table('material')->where(['mate_specification_id',$params['id']])->exists()){
            ResponseLogic::setMsg('该规格下存在物品，请删除物品后再删除规格');
            return false;
        }

        DB::connection('admin')->table('material_specification')->where(['masp_id' => $params['id']])->delete();
        return [];
    }
}
