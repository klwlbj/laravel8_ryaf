<?php

namespace App\Http\Controllers\Platform;

use Illuminate\Http\Request;
use App\Http\Server\Platform\Auth;
use App\Http\Server\Platform\Response;

class AuthController
{
    public function operatorAPIToken(Request $request)
    {
        $token = Auth::getInstance()->getToken();

        return Response::apiResult(200, '请求成功!', $token);
    }
}
