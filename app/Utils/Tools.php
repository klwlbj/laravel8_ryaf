<?php

namespace App\Utils;

use DateTime;
use DateTimeZone;
use Monolog\Logger;
use Illuminate\Support\Str;
use Ramsey\Uuid\Nonstandard\Uuid;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Support\Facades\Validator;

class Tools
{
    /**解析json字符串
     * @param $data
     * @return mixed
     */
    public static function jsonDecode($data)
    {
        if (is_array($data)) {
            return $data;
        }

        $newData = json_decode($data, true);
        if (is_array($newData)) {
            return $newData;
        }

        return $data;
    }

    /**
     * 和校验
     * @param $string
     * @return string
     */
    public static function checkSum($string): string
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

    /**
     * @param $data
     * @param int $option
     * @return string
     */
    public static function jsonEncode($data, $option = 256)
    {
        if (!is_array($data)) {
            return $data;
        }

        return json_encode($data, $option);
    }

    /**打印日志
     * @param $msg
     * @param null $path
     * @param array $data
     */
    public static function writeLog($msg, $path = null, $data = [], $filename = '', $output = null)
    {
        if (empty($path)) {
            $actions = explode('\\', \Route::current()->getActionName());
            $func    = explode('@', $actions[count($actions) - 1]);
            $path    = $func[0] . '-' . $func[1];
        }

        if (empty($filename)) {
            $filename = $path;
        }

        $path = 'logs/' . $path . '/' . $filename . '.log';

        if (!is_array($data) && !empty($data)) {
            $data = ['data' => $data];
        }

        if (empty($data)) {
            $data = array_merge($_GET, $_POST);
        }

        if (!is_array($data)) {
            $data = self::jsonDecode($data);
        }

        //        if(!empty($output)){
        //            $output = "%message%%context% %extra%\n";
        //        }

        (new Logger('local', [], [], new DateTimeZone('Asia/Shanghai')))
            ->pushHandler((new RotatingFileHandler(storage_path($path), 14))
                ->setFilenameFormat('{filename}{date}', 'ymd')
                ->setFormatter(new LineFormatter($output, null, true, true)))
            ->info($msg, $data);
    }

    public static function snake($data): array
    {
        return collect($data)->mapWithKeys(function ($value, $key) {
            return [Str::snake($key) => $value];
        })->toArray();
    }

    public static function camel($data): array
    {
        return collect($data)->mapWithKeys(function ($v, $k) {
            return [Str::camel($k) => $v];
        })->toArray();
    }

    public static function getISO8601Date($dateTime = 'now')
    {
        $date = new DateTime($dateTime);

        // 设置时区为东八区（亚洲/上海）
        $date->setTimezone(new DateTimeZone('Asia/Shanghai'));

        // 格式化时间，'Y-m-d\TH:i:s.uP' 表示：
        // Y - 四位年份
        // m - 两位月份
        // d - 两位日期
        // T - 字符T
        // H - 两位小时（24小时制）
        // i - 两位分钟
        // s - 两位秒
        // u - 微秒（这里将被截断为毫秒）
        // P - 时区偏移量
        $formattedDate = $date->format('Y-m-d\TH:i:s.u');

        // 截断微秒，只保留前三位
        $formattedDate = substr($formattedDate, 0, -3);
        return $formattedDate . '+08:00';
    }

    public static function deviceLog($type, $imei, $path, $data)
    {
        $logFileName = date('YmdHis') . '-' . $type . '-' . $imei . '-' . md5(Uuid::uuid4()->toString());
        Tools::writeLog('', $path, $data, $logFileName, '%message%%context% %extra%');
    }

    public static function validateParams($request, $rules, &$input)
    {
        // 进行验证
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
    }
}
