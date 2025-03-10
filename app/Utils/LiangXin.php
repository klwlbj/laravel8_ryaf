<?php

namespace App\Utils;

use App\Models\AlertReceiver;
use App\Models\DeviceLastestData;
use App\Models\Node;
use App\Models\Place;
use App\Models\SmokeDetector;
use App\Models\ThirdpartyNode;
use App\Models\ThirdpartyNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class LiangXin
{
    public static $monitorId = '10008';

    public static $deviceId = '8';

    public static $deviceSecret = 'ryaf2025';

    public static $thplId = 8;

    public static $msg = '';
    public static $code = 10000;

    public static $typeArr = [
        '120001' => '医院',
        '120002' => '养老院',
        '120003' => '政府机构',
        '120004' => '车站',
        '120005' => '码头',
        '120006' => '企业',
        '120007' => '商店',
        '120008' => '宾馆',
        '120009' => '非盈利性机构',
        '120010' => '科研单位',
        '120011' => '住宅',
        '120012' => '体育场',
        '120013' => '工厂',
        '120014' => '其他',
    ];

    public static function setMsg($msg)
    {
        self::$msg = $msg;
    }

    public static function getMsg()
    {
        return self::$msg;
    }

    public static function successRes($data = [])
    {
        return response()->json([
            'success' => true,
            'content' => $data
        ],200);
    }

    public static function errorRes($msg = null)
    {
        return response()->json([
            'success' => false,
            'msg' => $msg ?: self::getMsg(),
            'code' => self::$code
        ], 200);
    }

    public static function checkSign()
    {
        $timestamp = Request::header('Timestamp');
        if(empty($timestamp)){
            self::setMsg('Timestamp不能为空');
            return false;
        }
        $monitorId = Request::header('MonitorId');
        if($monitorId != self::$monitorId){
            self::setMsg('MonitorId有误');
            return false;
        }

        $deviceId = Request::header('DeviceId');
        if($deviceId != self::$deviceId){
            self::setMsg('DeviceId有误');
            return false;
        }


        $urlPath = '/' . Request::path();

        $sign = md5($urlPath . $timestamp . self::$deviceSecret);

        $reqSign = Request::header('Signature');

//        print_r($sign);die;

        if($sign !== $reqSign){
            self::setMsg('签名有误');
            return false;
        }

        return true;
    }

    public function getTown($params)
    {
        $nodeList = ThirdpartyNode::query()
            ->where(['thno_thpl_id' => self::$thplId])
            ->select([
                'thno_third_street_id as value',
                'thno_third_street_name as label'
            ])->groupBy(['thno_third_street_id','thno_third_street_name'])->get()->toArray();

        return $nodeList;
    }

    public function getRust($params)
    {
        $nodeList = ThirdpartyNode::query()
            ->where(['thno_thpl_id' => self::$thplId,'thno_third_street_id' => $params['townId']])
            ->select([
                'thno_third_node_id as value',
                'thno_third_node_name as label'
            ])->get()->toArray();

        return $nodeList;
    }

    public function addUnit($params)
    {

        if(Place::query()->where(['plac_thpl_id' => self::$thplId,'plac_thpl_pk' => $params['oid']])->exists()){
            self::setMsg('点位数据已存在');
            return false;
        }

        $chargeUsers = Tools::jsonDecode($params['chargeUsers']);
//        print_r($chargeUsers);die;

        $nodeId = ThirdpartyNode::getNodeId(self::$thplId,$params['rustId']);

//        print_r($nodeId);die;
        $rawData = Tools::jsonEncode($params);
        $nodeParentArr = Node::getNodeParent($nodeId);

        if(!empty($params['parentId'])){
            $parentName = Place::query()->where(['plac_id' => $params['parentId']])->value('plac_name') ?: '';
            if(!empty($parentName)){
                $params['name'] = $parentName . ' ' . $params['name'];
            }
        }

        $placeInsert = [
            'plac_node_id' => $nodeId,
            'plac_node_ids' => ',' . implode(',',$nodeParentArr) . ',',
            'plac_name' => $params['name'],
            'plac_address' => $params['address'],
            'plac_lng' => $params['gpsLnt'],
            'plac_type' => self::$typeArr[$params['type']] ?? '',
            'plac_lat' => $params['gpsLat'],
            'plac_thpl_id' => self::$thplId,
            'plac_thpl_pk' => $params['oid'],
            'plac_thpl_raw' => $rawData
        ];

        DB::beginTransaction();

        #插入点位信息
        $placeId = Place::query()->insertGetId($placeInsert);

        if($placeId === false){
            DB::rollBack();
            self::setMsg('插入点位信息失败');
            return false;
        }

        #如果存在接警负责人  则插入
        if(!empty($chargeUsers)){
            $receiverInsert = [];
            foreach ($chargeUsers as $key => $value) {
                $receiverInsert[] = [
                    'alre_place_id' => $placeId,
                    'alre_name' => $value['name'],
                    'alre_mobile' => $value['mobile'],
                    'alre_remark' => '良信用电负责人',
                ];
            }

            AlertReceiver::query()->insert($receiverInsert);
        }

        DB::commit();

        return ['id' => $placeId];
    }

    public function updateUnit($params)
    {
        if(!Place::query()->where(['plac_id' => $params['unitId'],'plac_thpl_id' => self::$thplId])->exists()){
            self::setMsg('点位数据不存在');
            return false;
        }

        $updateField = [
            'rustId' => 'plac_node_id',
            'name' => 'plac_name',
            'type' => 'plac_type',
            'address' => 'plac_address',
            'gpsLnt' => 'plac_lng',
            'gpsLat' => 'plac_lat'
        ];
        $placeUpdate = [];
        foreach ($updateField as $key => $value){
            if(!isset($params[$key])){
                continue;
            }
            switch ($key){
                case 'rustId':
                    $nodeId = ThirdpartyNode::getNodeId(self::$thplId,$params['rustId']);
                    $nodeParentArr = Node::getNodeParent($nodeId);
                    $placeUpdate['plac_node_id'] = $nodeId;
                    $placeUpdate['plac_node_ids'] = ',' . implode(',',$nodeParentArr) . ',';
                    break;
                case 'type':
                    $placeUpdate['plac_type'] = self::$typeArr[$params['type']] ?? '';
                    break;
                case 'name':
                    if(!empty($params['parentId'])){
                        $parentName = Place::query()->where(['plac_id' => $params['parentId']])->value('plac_name') ?: '';
                        if(!empty($parentName)){
                            $placeUpdate['name'] = $parentName . ' ' . $params['name'];
                        }
                    }
                    break;
                case 'address':
                case 'gpsLnt':
                case 'gpsLat':
                    $placeUpdate[$value] = $params[$key];
                    break;
            }
        }

//        print_r($placeUpdate);die;
        DB::beginTransaction();

        #更新点位信息
        if($placeUpdate){
            if(Place::query()->where(['plac_id' => $params['unitId']])->update($placeUpdate) === false){
                DB::rollBack();
                self::setMsg('更新点位信息失败');
                return false;
            }
        }


        if(!empty($params['chargeUsers'])){
            $chargeUsers = Tools::jsonDecode($params['chargeUsers']);
            #全量删除
            AlertReceiver::query()->where(['alre_place_id' => $params['unitId']])->update(['alre_del' => 1]);

            $alertReceiverArr = AlertReceiver::query()->where(['alre_place_id' => $params['unitId']])
                ->select([
                    DB::raw("CONCAT(alre_name,'_',alre_mobile) as str"),
                    'alre_id'
                ])
                ->pluck('alre_id','str')->toArray();

//        print_r($alertReceiverArr);die;
            #如果存在接警负责人  则插入
            if(!empty($chargeUsers)){
                $receiverInsert = [];
                $updateAlertId = [];
                foreach ($chargeUsers as $key => $value) {
                    if(isset($alertReceiverArr[$value['name'] . '_' . $value['mobile']])){
                        $updateAlertId[] = $alertReceiverArr[$value['name'] . '_' . $value['mobile']];
                        continue;
                    }
                    $receiverInsert[] = [
                        'alre_place_id' => $params['unitId'],
                        'alre_name' => $value['name'],
                        'alre_mobile' => $value['mobile'],
                        'alre_remark' => '良信用电负责人',
                    ];
                }

                #如果存在就启用
                if(!empty($updateAlertId)){
                    AlertReceiver::query()->whereIn('alre_id',$updateAlertId)->update(['alre_del' => 0]);
                }

                #如果不存在就插入
                if(!empty($receiverInsert)){
                    AlertReceiver::query()->insert($receiverInsert);
                }

            }
        }


        DB::commit();

        return ['id' => $params['unitId']];
    }

    public function unregisterUnit($params)
    {
        if(!Place::query()->where(['plac_id' => $params['unitId'],'plac_thpl_id' => self::$thplId])->exists()){
            self::setMsg('点位数据不存在');
            return false;
        }

        if(SmokeDetector::query()->where(['smde_place_id' => $params['unitId']])->exists()){
            self::setMsg('该点位下存在设备，请先删除设备');
            return false;
        }

        DB::beginTransaction();

        #删除单位
        if(Place::query()->where(['plac_id' => $params['unitId']])->delete() == false){
            DB::rollBack();
            self::setMsg('删除点位信息失败');
            return false;
        }

        #删除接警人
        if(AlertReceiver::query()->where(['alre_place_id' => $params['unitId']])->update(['alre_del' => 1]) === false){
            DB::rollBack();
            self::setMsg('删除接警人失败');
            return false;
        }

        DB::commit();
        return "点位注销！";
    }

    public function addDevice($params)
    {

        if(SmokeDetector::query()->where(['smde_imei' => $params['uid']])->exists()){
            self::setMsg('设备数据已存在');
            return false;
        }

        $placeData = Place::query()->where(['plac_id' => $params['unitId'],'plac_thpl_id' => self::$thplId])->first();
        if(!$placeData){
            self::setMsg('点位数据不存在');
            return false;
        }

        $placeData = $placeData->toArray();

        $deviceInsert = [
            'smde_imei' => $params['uid'],
            'smde_type' => '智能空开',
            'smde_model_name' => $params['oidType'],
            'smde_place_id' => $params['unitId'],
            'smde_node_ids' => $placeData['plac_node_ids'],
            'smde_brand_name' => $params['provider'],
            'smde_lng' => $params['gpsLnt'],
            'smde_lat' => $params['gpsLat'],
            'smde_thpl_id' => self::$thplId,
            'smde_thpl_raw' => Tools::jsonEncode($params),
            'smde_thpl_plac_pk' => $placeData['plac_thpl_pk'],
            'smde_position' => $params['installLocation']
        ];

        $placeUpdate = [
            'plac_generic_address' => $params['address'],
            'plac_standard_address' => $params['standardAddress'],
            'plac_standard_address_room' => $params['addrRoom'],

        ];

        DB::beginTransaction();

        #插入设备表
        $deviceId = SmokeDetector::query()->insertGetId($deviceInsert);
        if($deviceId === false){
            DB::rollBack();
            self::setMsg('添加设备失败');
            return false;
        }

        #更新点位表
        if(Place::query()->where(['plac_id' => $placeData['plac_id']])->update($placeUpdate) === false){
            DB::rollBack();
            self::setMsg('更新点位标准地址信息失败');
            return false;
        }

        DB::commit();

        return ['id' => $deviceId];
    }

    public function updateDevice($params)
    {
        $placeId = SmokeDetector::query()->where(['smde_id' => $params['deviceId'],'smde_thpl_id' => self::$thplId])->select(['smde_place_id'])->value('smde_place_id') ?: 0;
        if(empty($placeId)){
            self::setMsg('设备数据不存在');
            return false;
        }

        $updateField = [
            'uid' => 'smde_imei',
            'oidType' => 'smde_model_name',
            'unitId' => 'smde_place_id',
            'installLocation' => 'smde_position',
            'provider' => 'smde_brand_name',
            'gpsLnt' => 'smde_lng',
            'gpsLat' => 'smde_lat',
            'address' => 'plac_generic_address',
            'standardAddress' => 'plac_standard_address',
            'addrRoom' => 'plac_standard_address_room'
        ];

        if(!empty($params['unitId'])){
            $placeId = $params['unitId'];
        }

        $placeData = Place::query()->where(['plac_id' => $placeId])->select(['plac_thpl_pk','plac_node_ids'])->first();
        if(!$placeData){
            self::setMsg('点位数据不存在');
            return false;
        }

        $placeData = $placeData->toArray();

        $placeUpdate = [];
        $deviceUpdate = [];
        foreach ($updateField as $key => $value){
            if(!isset($params[$key])){
                continue;
            }
            switch ($key){
                case 'address':
                case 'standardAddress':
                case 'addrRoom':
                    $placeUpdate[$value] = $params[$key];
                    break;
                case 'uid':
                case 'oidType':
                case 'gpsLnt':
                case 'gpsLat':
                case 'installLocation':
                case 'provider':
                    $deviceUpdate[$value] = $params[$key];
                    break;
                case 'unitId':
                    $deviceUpdate[$value] = $params[$key];
                    $deviceUpdate['smde_thpl_plac_pk'] = $placeData['plac_thpl_pk'];
                    $deviceUpdate['smde_node_ids'] = $placeData['plac_node_ids'];
                    break;
            }
        }

        DB::beginTransaction();

        if(!empty($deviceUpdate)){
            #更新设备表
            if(SmokeDetector::query()->where(['smde_id' => $params['deviceId']])->update($deviceUpdate) === false){
                DB::rollBack();
                self::setMsg('更新设备表失败');
                return false;
            }
        }

        if(!empty($placeUpdate)){
            #更新设备表
            if(Place::query()->where(['plac_id' => $placeId])->update($placeUpdate) === false){
                DB::rollBack();
                self::setMsg('更新点位标准地址失败');
                return false;
            }
        }

        DB::commit();

        return ['id' => $params['deviceId']];
    }

    public function unregisterDevice($params)
    {
        if(!SmokeDetector::query()->where(['smde_id' => $params['deviceId'],'smde_thpl_id' => self::$thplId])->exists()){
            self::setMsg('设备数据不存在');
            return false;
        }

        if(SmokeDetector::query()->where(['smde_id' => $params['deviceId']])->delete() === false){
            self::setMsg('删除数据失败');
            return false;
        }

        return '设备注销！';
    }

    public function notify($params)
    {
        $data = SmokeDetector::query()
            ->leftJoin('place','plac_id','=','smde_place_id')
            ->where(['smde_thpl_id' => self::$thplId,'smde_imei' => $params['keyCode']])
            ->select([
                'smde_id',
                'plac_node_id',
                'plac_node_ids'
            ])
            ->first();
        if(!$data){
            self::setMsg('设备数据不存在');
            return false;
        }

        $data = $data->toArray();

        if($params['current']['msgType'] == 'sampling'){
            #实时数据
            $insertData = [
                'dld_voltage' => $params['current']['data']['ua'] ?? 0,
                'dld_current' => $params['current']['data']['ia'] ?? 0,
                'dld_temperature' => $params['current']['data']['tempA'] ?? 0,
                'dld_leak_current' => $params['current']['data']['leakCurrent'] ?? 0,
                'dld_other_params' => Tools::jsonEncode($params['current']['data'])
            ];

            $lastDataId = DeviceLastestData::query()->where(['dld_smde_id' => $data['smde_id']])->select(['dld_id'])->value('dld_id') ?: 0;
            if(!empty($lastDataId)){
                DeviceLastestData::query()->where(['dld_id' => $lastDataId])->update($insertData);
            }else{
                $insertData['dld_smde_id'] = $data['smde_id'];
                DeviceLastestData::query()->insert($insertData);
            }

            SmokeDetector::query()->where(['smde_id' => $data['smde_id']])->update(['smde_last_heart_beat' => date('Y-m-d H:i:s')]);
        }elseif($params['current']['msgType'] == 'fault'){
            #报警
            $insertData = [
                'thno_thpl_id' => self::$thplId,
                'thno_imei' => $params['keyCode'],
                'thno_type' => $params['current']['data']['faultType'] ?? 0,
                'thno_alarm_status' => 0,
                'thno_raw' => Tools::jsonEncode($params),
                'thno_node_id' => $data['plac_node_id'],
                'thno_node_ids' => $data['plac_node_ids'],
            ];

            ThirdpartyNotification::query()->insert($insertData);
        }

        return [];
    }
}
