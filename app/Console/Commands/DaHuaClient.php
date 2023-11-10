<?php

namespace App\Console\Commands;

use App\Utils\DaHua;
use Workerman\Worker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Workerman\Connection\TcpConnection;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\InvalidMessageException;
use PhpMqtt\Client\Exceptions\ProtocolViolationException;

class DaHuaClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dahua-client:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dahua TCP client';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws DataTransferException
     * @throws InvalidMessageException
     * @throws MqttClientException
     * @throws ProtocolViolationException
     * @throws RepositoryException
     */
    public function handle()
    {
        // 创建一个Worker监听2347端口，不使用任何应用层协议
        $tcp_worker = new Worker("tcp://0.0.0.0:8080");
        $util       = new DaHua();

        // 启动4个进程对外提供服务
        $tcp_worker->count = 4;

        // 当客户端发来数据时
        $tcp_worker->onMessage = function (TcpConnection $connection, $data) use ($util) {
            $data = bin2hex($data);
            echo date('H:i:s') . '收到客户端消息：' . $data . PHP_EOL;

            // 解析，并保存日志
            $array = $util->parseString($data);
            if ($array) {
                Log::info("收到客户端消息:" . date('H:i:s') . ':' . $data);
                // Log::info("Received dahua message:" . json_encode($array));
                $log_file = "storage/logs/dahua/" . ($array['from_address'] ?? '') . microtime(true) . ".log";
                file_put_contents($log_file, json_encode($array));

                // 业务流水号
                $no = substr($data, 4, 4);

                $year   = date('y');
                $month  = date('m');
                $day    = date('d');
                $hour   = date('H');
                $minute = date('i');
                $second = date('s');

                $time = sprintf("%02s", dechex($second)) . sprintf("%02s", dechex($minute)) . sprintf("%02s", dechex($hour)) . sprintf("%02s", dechex($day)) . sprintf("%02s", dechex($month)) . sprintf("%02s", dechex($year));
                // $time = substr($data, 12, 12);// 截取命令的时间

                $string = $no . '0102' . $time . $array['to_from_address'] . '000003';// 协议版本号，暂写死0102，地址也写死

                // 处理请求
                $response = strtolower('4040' . $string . $util->checkSum($string) . '2323');

                // 向客户端发送hello
                $connection->send(hex2bin($response));
            }
            unset($util);
        };

        // 运行worker
        Worker::runAll();
    }
}
