<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BaseController extends \Illuminate\Routing\Controller
{
    /**
     * 生成json格式响应结果
     *
     * @param array $data
     * @param string $msg
     * @param int $statusCode
     * @return JsonResponse
     */
    public function response($data = [], $msg = 'success', $code = 0, $statusCode = 200)
    {
        return new JsonResponse(
            ['data' => $data, 'msg' => $msg, 'code' => $code],
            $statusCode,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * 和校验
     * @param $string
     * @return string
     */
    protected function checkSum($string): string
    {
        // 将字符串按两个字符分割成数组元素
        $hexArray = str_split($string, 2);

        // 将每个数组元素从十六进制转换为十进制
        $decArray = array_map('hexdec', $hexArray);

        // 对数组中的所有元素求和
        $sum = array_sum($decArray);

        // 取和的低8位（和对256取模）
        $checksum = $sum % 256;

        return strtoupper(str_pad(dechex($checksum), 2, '0', STR_PAD_LEFT));
    }

    protected function validateParams($request, $rules, &$input)
    {
        // 进行验证
        $rules     = [];// todo 待删
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
    }

    /**
     * 验签
     * @param string $msg
     * @param string $nonce
     * @param string $signature
     * @return bool
     */
    protected function checkSign(string $msg, string $nonce, string $signature): bool
    {
        $token = env('NB_TOKEN');

        $sign = base64_encode(md5($token . $nonce . $msg, true));

        Log::info('sign:' . $sign);
        Log::info('signature:' . $signature);

        // 验证token
        if ($signature === $sign) {
            return true;
        }
        return false;
    }

    protected function aesDecrypt($encryptedData)
    {
        return openssl_decrypt($encryptedData, 'AES-128-CBC', env('NB_KEY'), OPENSSL_RAW_DATA, env('NB_KEY'));
    }
}
