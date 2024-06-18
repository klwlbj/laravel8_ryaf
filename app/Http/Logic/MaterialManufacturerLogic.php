<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class MaterialManufacturerLogic extends BaseLogic
{
    public function getList($params)
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;
        $point = ($page - 1) * $pageSize;

        $query = DB::connection('admin')->table('material_manufacturer');

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('mama_name','like','%'.$params['keyword'].'%');
        }

        $total = $query->count();

        $list = $query
//            ->orderBy('sort','desc')
            ->orderBy('mama_id','desc')
            ->offset($point)->limit($pageSize)->get()->toArray();

        return [
            'total' => $total,
            'list' => $list,
        ];
    }

//    public function getAllList($params)
//    {
//        $query = Manufacturer::query();
//
//        if(isset($params['keyword']) && $params['keyword']){
//            $query->where('name','like','%'.$params['keyword'].'%');
//        }
//
//        return $query
//            ->orderBy('id','desc')
//            ->get()->toArray();
//    }
//
//    public function getInfo($params)
//    {
//        $data = Manufacturer::query()->where(['id' => $params['id']])->first();
//
//        if(!$data){
//            ResponseLogic::setMsg('记录不存在');
//            return false;
//        }
//
//        return $data->toArray();
//    }
//
//    public function add($params)
//    {
//        $insertData = [
//            'name' => $params['name'],
//            'remark' => $params['remark'] ?? '',
//            'status' => $params['status'] ?? 1,
//            'created_by' => UserLogic::getUserInfo()['id']
//        ];
//
//        if(Manufacturer::query()->where(['name' => $params['name']])->exists()){
//            ResponseLogic::setMsg('厂家名称已存在');
//            return false;
//        }
//
//        $id = Manufacturer::query()->insertGetId($insertData);
//        if($id === false){
//            ResponseLogic::setMsg('添加失败');
//            return false;
//        }
//
//        return ['id' => $id];
//    }
//
//    public function update($params)
//    {
//        $insertData = [
//            'name' => $params['name'],
//            'remark' => $params['remark'] ?? '',
//            'status' => $params['status'] ?? 1,
//            'created_by' => UserLogic::getUserInfo()['id']
//        ];
//
//        if(Manufacturer::query()->where('id','<>',$params['id'])->where(['name' => $params['name']])->exists()){
//            ResponseLogic::setMsg('厂家名称已存在');
//            return false;
//        }
//
//        if(Manufacturer::query()->where(['id' => $params['id']])->update($insertData) === false){
//            ResponseLogic::setMsg('更新失败');
//            return false;
//        }
//
//        return [];
//    }
//
//    public function delete($params)
//    {
//        Manufacturer::query()->where(['id' => $params['id']])->delete();
//        return [];
//    }
}
