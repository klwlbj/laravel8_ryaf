<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;
use App\Utils\Tools;
use Illuminate\Support\Facades\Validator;

class UnitsServer extends BaseServer
{
    # 单位类型
    public $unitTypes = [
        1 => '一般单位',
        2 => '重点单位',
        3 => '九小单位',
        4 => '高层建筑',
        5 => '出租房',
        99 => '其他'
    ];

    #单位性质
    public $unitNature = [
        1 => '科研单位',
        2 => '农业建筑',
        3 => '机场',
        4 => '码头',
        5 => '企业',
        6 => '学校',
        7 => '商店',
        8 => '政府机关',
        9 => '车站',
        10 => '医院',
        11 => '养老院',
        12 => '宾馆',
        13 => '工厂',
        14 => '体育馆',
        15 => '住宅',
        16 => '非盈利机构',
        99 => '其它'
    ];

    public $resourceCode = '309000';

    /**统一社会信用代码
     * @param $id
     * @param $regionCode
     * @return string
     */
    public function getCreditCode($id): string
    {
        #生成统一社会信用代码  11 + 区域代码 + 单位表id（不足10位前面补0）
        return "11" . str_pad($id, 16, '0', STR_PAD_LEFT);
    }

    public function getUnitsId($creditCode)
    {
        #单位id  9423 + 资源码 + 00 + 生成统一社会信用代码
        return "9423" . $this->resourceCode . "00" . $creditCode;
    }

    public function add($params)
    {
        # 单位表id
        $id = '114529';
        # 白云区域代码
        $regionCode = '431';
        $creditCode = $this->getCreditCode($id);
        $unitId = $this->getUnitsId($creditCode);

        $params = [
            'unitId' => $unitId, //海康平台所需要的id格式 9423 + 资源码 + 00 + 生成统一社会信用代码
            'unitName' => '如约测试', //单位名称
            'address' => '钱大妈江高配送中心一楼C区', //单位地址
            'unitType' => '5', //单位类型 可看枚举
            'unitNature' => '99', //单位性质
            'mapType' => 1, //地图类型  1为高德
            'phoneNum' => '18680441997', //联系号码
            'pointX' => '113.22596', //经度
            'pointY' => '23.258282', //纬度
            'creditCode' => $creditCode, //统一社会信用代码  目前是11 + 16位我们平台单位表id（前面补0）
            'regionCode' => $regionCode, //区域编码 需要到村/社区级别
        ];


        $date = Tools::getISO8601Date();
        $params['createTime'] = $date;
        $params['updateTime'] = $date;
//        print_r($params);die;
        $data = [
            'units' => [
                $params
            ]
        ];
        Tools::writeLog('data','haikan',$data);
        return RequestServer::getInstance()->doRequest('fire/v1/units/add',$data);
    }

    public function update($params)
    {
        $id = '114111';
        $regionCode = '440111';
        $creditCode = $this->getCreditCode($id);
        $unitId = $this->getUnitsId($creditCode);

        $params = [
            'unitId' => $unitId,
            'unitName' => '白云区石井街道庆丰社区庆丰忠和里街75号101室',
            'address' => '白云区石井街道庆丰社区庆丰忠和里街75号101室',
            'unitType' => '5',
            'phoneNum' => '13902409266',
            'pointX' => '113.224015',
            'pointY' => '23.212011',
            'creditCode' => $creditCode,
            'regionCode' => $regionCode,
        ];


        $date = Tools::getISO8601Date();
        $params['createTime'] = $date;
        $params['updateTime'] = $date;
//        print_r($params);die;
        $data = [
            'units' => [
                $params
            ]
        ];
        return RequestServer::getInstance()->doRequest('fire/v1/units/update',$data);
    }

    public function delete($params)
    {
        $params['id'] = '114111';
        $regionCode = '440111';

        $creditCode = $this->getCreditCode($params['id']);
        $unitId = $this->getUnitsId($creditCode);
        $data = [
            'unitIds' => [
                $unitId
            ]
        ];
        Tools::writeLog('data','haikan',$data);
        return RequestServer::getInstance()->doRequest('fire/v1/units/delete',$data);
    }
}
