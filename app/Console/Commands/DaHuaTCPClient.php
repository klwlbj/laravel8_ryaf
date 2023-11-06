<?php

namespace App\Console\Commands;

use App\Utils\DaHua;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\InvalidMessageException;
use PhpMqtt\Client\Exceptions\ProtocolViolationException;

class DaHuaTCPClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dahua-tcp-client:start';

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
        $host = '0.0.0.0';
        $port = 8082;

        // 创建 TCP/IP 协议的 Socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        // 绑定 IP 地址和端口
        if (!socket_bind($socket, $host, $port)) {
            echo '绑定 IP 地址和端口失败：' . socket_strerror(socket_last_error()) . PHP_EOL;
            exit();
        }

        // 监听连接
        if (!socket_listen($socket)) {
            echo '监听连接失败：' . socket_strerror(socket_last_error()) . PHP_EOL;
            exit();
        }

        echo '等待客户端连接...' . PHP_EOL;
        while (true) {
            $util = new DaHua();
            // 接受客户端连接
            $clientSocket = socket_accept($socket);

            // 从客户端接收消息
            $message = socket_read($clientSocket, 1024);

            $data = bin2hex($message);
            echo date('H:i:s') . '收到客户端消息：' . $data . PHP_EOL;

            // 解析，并保存日志
            $array = $util->parseString($data);
            Log::info("收到客户端消息:".date('H:i:s').':'.$data);

            Log::info("Received dahua message:".json_encode($array));

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

            $string = $no . '0102' . $time . '64000000000028249c330000000003';// 协议版本号，暂写死0102，地址也写死
            // $string = $no . '0102' . $time . '000000000000000000000000000003';

            // 处理请求
            $response = strtolower('4040' . $string . $util->checkSum($string) . '2323');

            echo date('H:i:s') . '应答：' . $response . PHP_EOL;
            // echo '应答2：' . hex2bin($response) . PHP_EOL;

            // sleep(1);
            unset($util);
            // 向客户端发送响应
            socket_write($clientSocket, hex2bin($response));

            // 关闭客户端连接
            socket_close($clientSocket);
        }

        // 关闭服务器
        socket_close($socket);
    }
}
