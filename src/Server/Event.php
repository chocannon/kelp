<?php
// +----------------------------------------------------------------------
// | SWOOLE事件注册
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Server;

use Swoole\Server;
use Kelp\Component\DI;
use Kelp\Common\Singleton;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Kelp\Component\Event as BaseEvent;
use Kelp\Process\Manager as ProcessManager;
use Kelp\Component\Port\Manager as PortManager;

class Event extends BaseEvent
{
    use Singleton;

    function __construct(array $allowKeys = null)
    {
        parent::__construct([
            'start','shutdown','workerStart','workerStop','workerExit','timer',
            'connect','receive','packet','close','bufferFull','bufferEmpty','task',
            'finish','pipeMessage','workerError','managerStart','managerStop',
            'request','handShake','message','open'
        ]);

        $this->set('start',        self::onStart());
        $this->set('managerStart', self::onManagerStart());
        $this->set('workerStart',  self::onWorkerStart());
        $this->set('task',         self::onTask());
        $this->set('finish',       self::onFinish());
        // $this->set('request',      self::onRequest());
        $this->set('connect',      self::onConnect());
        $this->set('receive',      self::onReceive());
        $this->set('close',        self::onClose());
    }


    public function fire(string $kind)
    {
        $this->delete();
        if (PortManager::SOCK_HTTP === $kind) {
            $this->set('request', self::onRequest());
        }
        if (PortManager::SOCK_WEB_SOCKET === $kind) {
            $this->set('open',      self::onOpen());
            $this->set('message',   self::onMessage());
            $this->set('close',     self::onClose());
            $this->set('handshake', self::onHandShake());
        }
        if (PortManager::SOCK_TCP === $kind) {
            $this->set('connect',      self::onConnect());
            $this->set('receive',      self::onReceive());
            $this->set('close',        self::onClose());
        }
        return $this;
    }


    public static function onStart()
    {
        return function (Server $server) {
            $serverName = DI::instance()->get('SERVER_TITLE');
            ProcessManager::instance()->setProcessName($serverName . ':master');
        };
    }


    public static function onClose()
    {
        return function (Server $server, $fd, $reactorId) {
            echo $reactorId;
        };
    }


    public static function onManagerStart()
    {
        return function (Server $server) {
            $serverName = DI::instance()->get('SERVER_TITLE');
            ProcessManager::instance()->setProcessName($serverName . ':manager');
        };
    }


    public static function onWorkerStart()
    {
        return function (Server $server, int $workerId) {
            $serverName = DI::instance()->get('SERVER_TITLE');
            $workerNum  = DI::instance()->get('WORKER_NUM');
            if($workerId <= ($workerNum -1)){
                ProcessManager::instance()->setProcessName($serverName . ':worker_' . $workerId);
            }else{
                ProcessManager::instance()->setProcessName($serverName . ':tasker_' . $workerId);
            }
        };
    }


    public static function onConnect()
    {
        return function (Server $server, int $fd, int $reactorId) {
            echo $reactorId;
        };
    }


    public static function onReceive()
    {
        return function (Server $server, int $fd, int $reactorId, string $data) {
            echo $data;
        };
    }


    public static function onRequest()
    {
        return function (Request $request, Response $response) {
            $response->end('3333333');
        };
    }


    public static function onTask()
    {
        return function (Server $server, int $taskId, int $workId, string $data) {
            var_dump($data);
        };
    }


    public static function onFinish()
    {
        return function (Server $server, int $taskId, string $data) {
            var_dump($data);
        };
    }
}