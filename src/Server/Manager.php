<?php
// +----------------------------------------------------------------------
// | 服务管理器
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Server;

use Exception;
use Kelp\Component\DI;
use Kelp\Utility\Config;
use Kelp\Common\Singleton;
use Kelp\Component\Invoker;
use Kelp\Component\Port\Manager as PortManager;

class Manager
{
    use Singleton;

    public $server = null;


    public function start()
    {
        $this->createServer();
    }

    
    private function createServer()
    {
        $setting = Config::instance()->get('server.setting');
        $default = Config::instance()->get('port.default', 'tcp');
        $running = Config::instance()->get('port.ports.' . $default);
        // 是否开启SSL
        $args = PortManager::certInvalid($running);
        // 实例化SwooleServer
        $this->server = new \Swoole\Server($running['socket_host'], $running['socket_port'], SWOOLE_PROCESS, $args);
        // 加载配置
        $this->loadConfig($setting);
        // 注册回调事件
        $events = Event::instance()->get();
        foreach ($events as $event => $callback){
            $this->server->on($event, $callback);
        }
        // 绑定多端口
        PortManager::bind($this->server, $default);
        // 开启服务
        $this->server->start();
    }


    private function loadConfig(array $setting)
    {
        DI::instance()->set('WORKER_NUM', $setting['worker_num']);
        DI::instance()->set('TASKER_NUM', $setting['task_worker_num']);
        $this->server->set($setting);
    }
}