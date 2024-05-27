<?php

namespace App\Http\Server\Hikvision;

use App\Http\Server\BaseServer;

class Response extends BaseServer
{
    protected static $status = 200;
    protected static $message = '';
    protected static $data;

    public static function setMsg($message){
        self::$message = $message;
    }

    public static function getMsg(): string
    {
        return self::$message;
    }

    public static function returnJson($result)
    {
        return response()->json($result, 200);
    }

    public static function apiResult($status = 200,$message = '',$data = []): \Illuminate\Http\JsonResponse
    {
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($result, 200);
    }

    public static function apiErrorResult($message = ''): \Illuminate\Http\JsonResponse
    {
        $result = [
            'status' => -1,
            'message' => $message,
            'data' => $message,
        ];
        return response()->json($result, 200);
    }
}
