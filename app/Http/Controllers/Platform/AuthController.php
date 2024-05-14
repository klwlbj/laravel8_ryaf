<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\Response;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function operatorAPIToken(Request $request){
        $token = Auth::getInstance()->getToken();

        return Response::apiResult(200,'请求成功!',$token);
    }
}
