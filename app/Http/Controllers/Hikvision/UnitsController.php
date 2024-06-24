<?php

namespace App\Http\Controllers\Hikvision;
use App\Http\Controllers\Controller;
use App\Http\Server\Hikvision\Response;
use App\Http\Server\Hikvision\UnitsServer;
use App\Utils\Tools;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public function add(Request $request){
        $params = $request->all();
        Tools::writeLog('units add param','haikan',$params);
        $validate = Validator::make($params, [
            'id' => 'required',
            'unitName' => 'required',
            'regionCode' => 'required',
            'address' => 'required',
            'unitType' => 'required',
            'unitNature' => 'required',
            'mapType' => 'required',
            'phoneNum' => 'required',
            'pointX' => 'required',
            'pointY' => 'required',
        ],[
            'id.required' => 'ID 不得为空',
            'unitName.required' => 'unitName 不得为空',
            'regionCode.required' => 'regionCode 不得为空',
            'address.required' => 'address 不得为空',
            'unitType.required' => 'unitType 不得为空',
            'unitNature.required' => 'unitNature 不得为空',
            'mapType.required' => 'mapType 不得为空',
            'phoneNum.required' => 'ID 不得为空',
            'pointX.required' => 'ID 不得为空',
            'pointY.required' => 'ID 不得为空',
        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = UnitsServer::getInstance()->add($params);

        return Response::returnJson($res);
    }

    public function update(Request $request){
        $params = $request->all();
        Tools::writeLog('units update param','haikan',$params);
        $validate = Validator::make($params, [
            'id' => 'required',
            'unitName' => 'required',
            'regionCode' => 'required',
            'address' => 'required',
            'unitType' => 'required',
            'unitNature' => 'required',
            'mapType' => 'required',
            'phoneNum' => 'required',
            'pointX' => 'required',
            'pointY' => 'required',
        ],[
            'id.required' => 'ID 不得为空',
            'unitName.required' => 'unitName 不得为空',
            'regionCode.required' => 'regionCode 不得为空',
            'address.required' => 'address 不得为空',
            'unitType.required' => 'unitType 不得为空',
            'unitNature.required' => 'unitNature 不得为空',
            'mapType.required' => 'mapType 不得为空',
            'phoneNum.required' => 'ID 不得为空',
            'pointX.required' => 'ID 不得为空',
            'pointY.required' => 'ID 不得为空',
        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = UnitsServer::getInstance()->update($params);

        return Response::returnJson($res);
    }

    public function delete(Request $request)
    {
        $params = $request->all();
        Tools::writeLog('units delete param','haikan',$params);
        $validate = Validator::make($params, [
            'id' => 'required',
        ],[
            'id.required' => 'ID 不得为空',
        ]);

        if($validate->fails())
        {
            return Response::returnJson(['code' => -1,'message' => $validate->errors()->first(),'date' => []]);
        }

        $res = UnitsServer::getInstance()->delete($params);

        return Response::returnJson($res);
    }


}
