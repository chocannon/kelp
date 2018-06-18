<?php
// +----------------------------------------------------------------------
// | 应用核心
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp;

use Kelp\Component\DI;
use Kelp\Component\Trigger;
use Kelp\Utility\Directory;
use Kelp\Standard\Singleton;
use Kelp\Swoole\ServerManager;

class App
{
    use Singleton;

    const VERSION = '1.0.0';

    public function __construct()
    {
        defined('SWOOLE_VERSION') or define('SWOOLE_VERSION', intval(phpversion('swoole')));
        defined('APPLICATION_ROOT') or define('APPLICATION_ROOT', realpath(getcwd()));
        $this->initialize();
    }


    /**
     * 启动swoole
     *
     * @return void
     */
    public function run()
    {
        ServerManager::instance()->start();
    }


    /**
     * 初始化操作
     *
     * @return void
     */
    private function initialize()
    {
        DI::instance()->set('VERSION', self::VERSION);
        $this->directoryInit();
        $this->errorHandle();
    }


    /**
     * 初始化目录
     *
     * @return void
     */
    private function directoryInit()
    {
        $tempPath = Config::instance()->get('app.dir.temp');
        if(empty($tempPath)){
            $tempPath = APPLICATION_ROOT . '/temp/';
            Config::instance()->set('app.dir.temp', $tempPath);
        }
        $logPath  = Config::instance()->get('app.dir.log');
        if(empty($logPath)){
            $logPath  = APPLICATION_ROOT . '/log/';
            Config::instance()->set('app.dir.log', $logPath);
        }
        $servName = Config::instance()->get('server.main.serv_name', 'main_swoole');
        Config::instance()->set('server.main.setting.pid_file', $tempPath . '/' . $servName . '.pid');
        Config::instance()->set('server.main.setting.log_file', $logPath . '/' . $servName . '.log');
    }


    /**
     * 处理异常与错误
     *
     * @return void
     */
    private function errorHandle()
    {
        $debug = Config::instance()->get('app.debug');
        if(false == $debug){
            return;
        }
        ini_set('display_errors', 'on');
        error_reporting(E_ALL | E_STRICT);
        set_error_handler(function($code, $msg, $file = null, $line = null) {
            Trigger::error($code, $msg, $file, $line);
        });
        register_shutdown_function(function () {
            $e = error_get_last();
            if(!empty($e)){
                Trigger::error($e['type'], $e['message'], $e['file'], $e['line']);
            }
        });
    }
}