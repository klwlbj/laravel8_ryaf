<?php

namespace App\Models;

class ThirdpartyNode extends BaseModel
{
    protected $table = 'thirdparty_node';
    public $timestamps = null;

    public static function updateNodeId($data)
    {
        $nodeList = Node::query()->select(['node_id','node_name'])->get()->toArray();
        $nodeArr = [];

        $thirdNodeName = $data['thno_third_node_name'];
        $replaceArr = [
            '村委' => '社区',
        ];

        $replaceWordArr = [
            '五龙岗村委' => '五龙岗村',
            '祥景社区' => '祥景花园社区',
            '陈田村委' => '陈田第一社区',
            '黄石第一社区' => '黄石花园第一社区',
            '黄石第二社区' => '黄石花园第二社区',
        ];

        foreach ($nodeList as $key => $value){
            $nodeName = $value['node_name'];
            foreach ($replaceArr as $k => $v){
                $nodeName = str_replace($k,$v,$nodeName);
            }

            if($nodeName === '潭村联社'){
                $nodeArr['潭村北社区'] = $value['node_id'];
                $nodeArr['潭村南社区'] = $value['node_id'];
            }elseif(isset($replaceWordArr[$nodeName])){
                $nodeArr[$replaceWordArr[$nodeName]] = $value['node_id'];
            }else{
                $nodeArr[$nodeName] = $value['node_id'];
            }


        }

        if(isset($nodeArr[$thirdNodeName]) && $nodeArr[$thirdNodeName] != $data['thno_node_id']){
            self::query()->where(['thno_id' => $data['thno_id']])->update(['thno_node_id' => $nodeArr[$thirdNodeName]]);
            return $nodeArr[$thirdNodeName];
        }

        return $data['thno_node_id'];
    }

    public static function getNodeId($thplId,$thirdNodeId)
    {
        $data = self::query()->where(['thno_thpl_id' => $thplId,'thno_third_node_id' => $thirdNodeId])->first();
//        print_r($data);die;
        if(!$data){
            return 5;
        }
        $data = $data->toArray();

        return self::updateNodeId($data);
    }
}
