<?php

namespace App\Models;

class Node extends BaseModel
{
    protected $table   = 'node';
    public $timestamps = null;
    protected $fillable = ['node_id', 'node_parent_id'];

    public $primaryKey = 'node_id';

    // 不允许批量赋值的字段
    protected $guarded = [];

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
