<?php

namespace App\Console\Commands;

use App\Utils\Apis\Aep_device_command;
use App\Utils\HuiXiao;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;

class HuiXiaoTcpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:huixiao-server {action} {--daemonize}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'huixiao tcp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        self::$client   = new Client(['verify' => false]);
    }

    public static $connections = [];

    public static $connectionIdRelation = [];

    public static $excuteArr = [];

    public $successCommand = [
        '0d0a8Eaa0d0a',
        '0d0a8caa0d0a',
        '0d0a9faa0d0a'
    ];

    public static $client = null;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {
            // 创建一个TCP连接监听端口，不使用任何应用层协议
            $tcpWorker = new Worker("tcp://0.0.0.0:8888");

            // 启动4个进程对外提供服务
            $tcpWorker->count = 1;

            $tcpWorker->onConnect = function($connection) {
                $ip = $connection->getRemoteIp();
                echo '远程连接ip : ' . $ip . "\n";
            };

            // 当客户端发来数据时
            $tcpWorker->onMessage = function (TcpConnection $connection, $data) {

                $data = bin2hex($data);
                $connectionId = $connection->id;

                if(!isset(self::$connections[$connectionId])) {
                    self::$connections[$connectionId] = $connection;
                    echo '添加关系id:' . $connectionId . "\n";
//                    echo '关联关系:' . Tools::jsonEncode($connections) . "\r\n";
                }



                echo '接收到数据:' . Tools::jsonEncode($data) . "\r\n";

                #接收到下发成功指令
                if(in_array($data,$this->successCommand)){
                    echo '发送确认消息';
                    if(!empty(self::$excuteArr)){
                        foreach(self::$excuteArr as $k=>$v){
                            $v->send(hex2bin($data));
                        }
                    }


                    return true;
                }

                //如果是执行命令
                if(str_starts_with($data, '7c7c')){
                    $deviceId = substr($data, 4,10);
                    $executeStr = substr($data, 14);

                    echo '关联关系:' . Tools::jsonEncode(self::$connectionIdRelation) . "\r\n";
                    if(!isset(self::$connectionIdRelation[$deviceId])){
                        return false;
                    }

                    $sendConnectionId = self::$connectionIdRelation[$deviceId];
                    echo '关联id:' . $sendConnectionId . "\r\n";
                    if(!isset(self::$connections[$sendConnectionId])){
                        return false;
                    }
//                    echo '执行111:' . "\r\n";
                    if(!isset(self::$excuteArr[$connectionId])){
                        self::$excuteArr[$connectionId] = $connection;
                    }

                    $sendConnection = self::$connections[$sendConnectionId];
                    echo '向设备id:' . $deviceId . ' 发送消息：' . $executeStr . "\r\n";
                    $sendConnection->send(hex2bin($executeStr));
                    return true;
                }

                $util = new HuiXiao();

                $analyzeList = $util->parseString($data);

                if(!$analyzeList){
                    #如果解析失败并且不在成功命令里
                    if(!in_array($data,$this->successCommand)){
                        $ip = $connection->getRemoteIp();
                        $connection->close();
                        echo '断开连接 ip:' . $ip . "\r\n";
                        return false;
                    }
                }
                Tools::writeLog('原始数据','tcp',$data);
                Tools::writeLog('解析后数据','tcp',$analyzeList);
                $pushApi = config('services.fire_alarm_panel.push_api');

                foreach ($analyzeList as $key => $analyzeData){
                    echo '解析数据:' . Tools::jsonEncode($analyzeData) . "\r\n";
//                    return false;
                    #如果心跳 则推送到接口
                    if(isset($analyzeData['type']) && $analyzeData['type']['value'] == 'heart_beat'){
                        $deviceId = $analyzeData['gateway_id']['value'];
                        if(!isset(self::$connectionIdRelation[$deviceId])){
                            self::$connectionIdRelation[$deviceId] = $connectionId;
                            echo '添加设备关系id:' . $deviceId . "\n";
                            echo '设备关联关系:' . Tools::jsonEncode(self::$connectionIdRelation) . "\r\n";
                        }
                        elseif(self::$connectionIdRelation[$deviceId] != $connectionId){
                            self::$connectionIdRelation[$deviceId] = $connectionId;
                            echo '修改设备关系id:' . $deviceId . "\n";
                            echo '设备关联关系:' . Tools::jsonEncode(self::$connectionIdRelation) . "\r\n";
                        }

                        $req = [
                            'id' =>   $deviceId,
                            'type' => 'heartbeat',
                            'data' => Tools::jsonEncode($analyzeData)
                        ];

                        $response = self::$client->post(
                            $pushApi,
                                [
                                'headers' => [

                                ],
                                'json'    => (object)$req,
                            ]);

                        echo '推送心跳包res：' . $response->getBody() . "\r\n";
                    }elseif(isset($analyzeData['type']) && $analyzeData['type']['value'] == 'alarm' && $analyzeData['info_type']['value'] == '火警'){

                        #如果火警 推送
                        $deviceId = $analyzeData['gateway_id']['value'];

                        $req = [
                            'id' =>   $deviceId,
                            'type' => 'fire_alert',
                            'data' => Tools::jsonEncode($analyzeData)
                        ];

                        $response = self::$client->post(
                            $pushApi,
                            [
                                'headers' => [

                                ],
                                'json'    => (object)$req,
                            ]);

                        echo '推送火警res：' . $response->getBody() . "\r\n";
                    }elseif(isset($analyzeData['type']) && $analyzeData['type']['value'] == 'alarm' && $analyzeData['info_type']['value'] == '故障'){
                        #如果故障 推送
                        $deviceId = $analyzeData['gateway_id']['value'];

                        $req = [
                            'id' => $deviceId,
                            'type' => 'malfunction',
                            'data' => Tools::jsonEncode($analyzeData)
                        ];

                        $response = self::$client->post(
                            $pushApi,
                            [
                                'headers' => [

                                ],
                                'json'    => (object)$req,
                            ]);

                        echo '推送故障res：' . $response->getBody() . "\r\n";
                    }else{
                        $deviceId = $analyzeData['gateway_id']['value'];

                        $req = [
                            'id' =>   $deviceId,
                            'type' => 'other',
                            'data' => Tools::jsonEncode($analyzeData)
                        ];

                        $response = self::$client->post(
                            $pushApi,
                            [
                                'headers' => [

                                ],
                                'json'    => (object)$req,
                            ]);

                        echo '推送其他消息res：' . $response->getBody() . "\r\n";
                    }
                }
            };

            $tcpWorker->onClose = function ($connection){
                $id = $connection->id;
                if(isset(self::$connections[$id])) {
                    unset(self::$connections[$id]);
                }

                if(isset(self::$excuteArr[$id])) {
                    unset(self::$excuteArr[$id]);
                }
                echo '连接关闭Id : ' . $id . "\r\n";
            };
            // 运行worker
            Worker::runAll();
        } catch (Exception $e) {
            // 捕获到异常后的处理逻辑
            $this->error('An error occurred: ' . $e->getMessage());
        }

    }
}
