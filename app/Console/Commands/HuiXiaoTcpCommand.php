<?php

namespace App\Console\Commands;

use App\Utils\Apis\Aep_device_command;
use App\Utils\Tools;
use Exception;
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
    }

    public static $connections = [];

    public static $connectionIdRelation = [];

    public static $excuteArr = [];

    public $successCommand = [
        '0d0a8Eaa0d0a',
        '0d0a8caa0d0a',
        '0D0A9FAA0D0A'
    ];

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

            // 当客户端发来数据时
            $tcpWorker->onMessage = function (TcpConnection $connection, $data) {
                $data = bin2hex($data);
                $connectionId = $connection->id;

                if(!isset(self::$connections[$connectionId])) {
                    self::$connections[$connectionId] = $connection;
                    echo '添加关系id:' . $connectionId . "\n";
//                    echo '关联关系:' . Tools::jsonEncode($connections) . "\r\n";
                }


                Tools::writeLog('data','tcp',$data);
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

                $analyzeData = $this->analyze($data);


                if(!$analyzeData){
                    return false;
                }
                echo '解析数据:' . Tools::jsonEncode($analyzeData) . "\r\n";

                if(isset($analyzeData['type']) && $analyzeData['type'] == 'heartbeat'){
                    if(!isset(self::$connectionIdRelation[$analyzeData['id']])){
                        self::$connectionIdRelation[$analyzeData['id']] = $connectionId;
                        echo '添加设备关系id:' . $analyzeData['id'] . "\n";
                        echo '设备关联关系:' . Tools::jsonEncode(self::$connectionIdRelation) . "\r\n";
                    }
                    elseif(self::$connectionIdRelation[$analyzeData['id']] != $connectionId){
                        self::$connectionIdRelation[$analyzeData['id']] = $connectionId;
                        echo '修改设备关系id:' . $analyzeData['id'] . "\n";
                        echo '设备关联关系:' . Tools::jsonEncode(self::$connectionIdRelation) . "\r\n";
                    }

                }

                if(isset($analyzeData['type']) && $analyzeData['type'] == 'execute'){
                    echo '关联关系:' . Tools::jsonEncode(self::$connectionIdRelation) . "\r\n";
                    if(!isset(self::$connectionIdRelation[$analyzeData['id']])){
                        return false;
                    }
                    $sendConnectionId = self::$connectionIdRelation[$analyzeData['id']];
                    echo '关联id:' . $sendConnectionId . "\r\n";
                    if(!isset(self::$connections[$sendConnectionId])){
                        return false;
                    }
//                    echo '执行111:' . "\r\n";
                    if(!isset(self::$excuteArr[$connectionId])){
                        self::$excuteArr[$connectionId] = $connection;
                    }

                    $sendConnection = self::$connections[$sendConnectionId];
                    echo '向设备id:' . $analyzeData['id'] . ' 发送消息：' . $analyzeData['str'] . "\r\n";
                    $sendConnection->send(hex2bin($analyzeData['str']));

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

    public function analyze($str)
    {
        $start = substr($str, 0, 4);
        if ($start == '7a7a') {
            $id = substr($str, 4, 10);
            echo '用传id : ' . $id . "\r\n";
            return [
                'type' => 'heartbeat',
                'id' => $id,
            ];
        }


        if($start == '7c7c'){
            $id = substr($str, 4, 10);
            return [
                'type' => 'execute',
                'id' => $id,
                'str' => substr($str, 14),
            ];
        }
        return false;
        $start = substr($str, 0, 6);
        if($start == '7b7b7b'){

        }
    }
}
