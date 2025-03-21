<?php

namespace App\Models;

class Node extends BaseModel
{
    protected $table   = 'node';
    public $timestamps = null;
    protected $fillable = ['node_id', 'node_parent_id'];

    public $primaryKey = 'node_id';
    public static function getChildList($parentId = 0,$type = null)
    {
        $list = self::query()->select(['node_parent_id','node_type','node_id','node_name'])
            ->get()->toArray();

        $arr = [];

        foreach ($list as $key => $value){
            if($parentId != $value['node_parent_id']){
                continue;
            }

            $arr = self::getChild($list,$value['node_id'],$type,$arr);
        }

        return $arr;
    }

    public static function getChild($list,$parentId = 0, $type = null, $arr = [])
    {
        foreach ($list as $key => $value){
            if($parentId == $value['node_parent_id']){
                if($type == $value['node_type']){
                    $arr[] = $value;
                }

                self::getChild($list,$value['node_id'],$type,$arr);
            }
        }

        return $arr;
    }

    public static function getNodeParent($id)
    {
        $list = self::query()
//            ->where(['node_enabled' => 1])
            ->select(['node_parent_id','node_id'])->get()->pluck('node_parent_id','node_id')->toArray();

        $arr = [$id];

        $arr = self::getParents($list,$id,$arr);

        return array_reverse($arr);
    }

    public static function getParents($list,$id,$arr){
        $parentId = $list[$id] ?? 0;
        if($parentId <= 0){
            return $arr;
        }
        $arr[] = $parentId;
        $arr = self::getParents($list,$parentId,$arr);

        return $arr;
    }

    /**
     * 递归查找所有上级 node_parent_id
     *
     * @param int $id
     * @return string
     */
    public static function getParentIds($id)
    {
        // 查找当前节点
        $node = self::find($id);

        // 如果找到了节点并且它有 node_parent_id
        if ($node && $node->node_parent_id) {
            // 递归查找父级节点，并将当前节点的 node_parent_id 与递归结果拼接
            return self::getParentIds($node->node_parent_id) . ',' . $node->node_parent_id;
        }

        // 如果没有父节点，返回空字符串
        return '';
    }
}
