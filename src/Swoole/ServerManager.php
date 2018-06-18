<?php
// +----------------------------------------------------------------------
// | 服务管理器
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Swoole;

use Exception;
use Kelp\Standard\Singleton;

class ServerManager
{
    use Singleton;

    private $servList = [];

    public function start()
    {
        $servs = Config::instance()->get('server');
        foreach ($servs as $servTag => $servConf) {
            $this->createServer($servTag, $servConf)->start();
        }
    }


    private function createServer(string $tag, array $conf)
    {
        $host = $conf['host'];
        $port = $conf['port'];
        $mode = $conf['run_mode'];
        $sock = $conf['sock_type'];
        switch ($conf['serv_type']) {
            case 'tcp' : 
                $serv = new \Swoole\Server($host, $port, $mode, $sock);
                break;
            case 'http': 
                $serv = new \Swoole\Http\Server($host, $port, $mode, $sock);
                break;
            case 'ws' : 
                $serv = new \Swoole\Websocket\Server($host, $port, $mode, $sock);
                break;
            default : 
                throw new Exception('Unknown Server Type : ' . $conf['serv_type']);
                break;
        }
        $serv->set($conf['setting']);
        $serv->
        

        $this->servList[$tag] = $serv;
        return $serv;
    }



    public function addServer(string $serverName, int $port, int $type = SWOOLE_TCP, string $host = '0.0.0.0',array $setting = [
        "open_eof_check"=>false,
    ]):EventRegister
    {
        $eventRegister = new EventRegister();
        $this->serverList[$serverName] = [
            'port'=>$port,
            'host'=>$host,
            'type'=>$type,
            'setting'=>$setting,
            'eventRegister'=>$eventRegister
        ];
        return $eventRegister;
    }

    public function isStart():bool
    {
        return $this->isStart;
    }

    


    private function attachListener():void
    {
        $mainServer = $this->getServer();
        foreach ($this->serverList as $serverName => $server){
            $subPort = $mainServer->addlistener($server['host'],$server['port'],$server['type']);
            if($subPort){
                $this->serverList[$serverName] = $subPort;
                if(is_array($server['setting'])){
                    $subPort->set($server['setting']);
                }
                $events = $server['eventRegister']->all();
                foreach ($events as $event => $callback){
                    $subPort->on($event, function () use ($callback) {
                        $ret = [];
                        $args = func_get_args();
                        foreach ($callback as $item) {
                            array_push($ret,Invoker::callUserFuncArray($item, $args));
                        }
                        if(count($ret) > 1){
                            return $ret;
                        }
                        return array_shift($ret);
                    });
                }
            }else{
                Trigger::throwable(new \Exception("addListener with server name:{$serverName} at host:{$server['host']} port:{$server['port']} fail"));
            }
        }
    }

    

    public function getServer($serverName = null):?\swoole_server
    {
         if($this->mainServer){
             if($serverName === null){
                 return $this->mainServer;
             }else{
                 if(isset($this->serverList[$serverName])){
                     return $this->serverList[$serverName];
                 }
                 return null;
             }
         }else{
             return null;
         }
    }

    public function coroutineId():?int
    {
        if(class_exists('Swoole\Coroutine')){
            //进程错误或不在协程中的时候返回-1
            $ret =  Coroutine::getuid();
            if($ret >= 0){
                return $ret;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public function isCoroutine():bool
    {
        if($this->coroutineId() !== null){
            return true;
        }else{
            return false;
        }
    }


    private function finalHook(EventRegister $register)
    {
        //实例化对象池管理
        PoolManager::getInstance();
        $register->add($register::onWorkerStart,function (\swoole_server $server,int $workerId){
            PoolManager::getInstance()->__workerStartHook($workerId);
            $workerNum = Config::getInstance()->getConf('MAIN_SERVER.SETTING.worker_num');
            $name = Config::getInstance()->getConf('SERVER_NAME');
            if(PHP_OS != 'Darwin'){
                if($workerId <= ($workerNum -1)){
                    $name = "{$name}_Worker_".$workerId;
                }else{
                    $name = "{$name}_Task_Worker_".$workerId;
                }
                cli_set_process_title($name);
            }
        });
        EventHelper::registerDefaultOnTask($register);
        EventHelper::registerDefaultOnFinish($register);
        EventHelper::registerDefaultOnPipeMessage($register);
        $conf = Config::getInstance()->getConf("MAIN_SERVER");
        if($conf['SERVER_TYPE'] == self::TYPE_WEB_SERVER || $conf['SERVER_TYPE'] == self::TYPE_WEB_SOCKET_SERVER){
            if(!$register->get($register::onRequest)){
                EventHelper::registerDefaultOnRequest($register);
            }
        }
    }
}