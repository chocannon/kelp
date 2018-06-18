<?php
// +----------------------------------------------------------------------
// | 日志类
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Utility;

use Kelp\Standard\Singleton;
use BadFunctionCallException;

class Logger
{
    use Singleton;

    const LEVEL  = ['sql', 'error', 'warning', 'notice', 'info', 'debug'];
    const SUFFIX = '.log';

    private $logPath;


    public function __construct()
    {
        $this->logPath = Config::instance()->get('app.dir.log');
    }


    /**
     * 替换占位
     *
     * @param [type] $message
     * @param array $context
     * @return void
     */
    public function interpolate($message, array $context = [])
    {
        $replace = [];
        array_walk($context, function ($val, $key) use (&$replace) {
            return $replace['{' . $key . '}'] = is_string($val) ? $val : print_r($val, true);
        });
        return strtr($message, $replace);
    }


    /**
     * 统一写日志
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(string $level, string $message, array $context = [])
    {
        $message = '[' . date('Y-m-d H:i:s') . '] ' . $this->interpolate($message, $context) . "\r\n";
        if ('debug' === $level) {
            echo $message;
        }
        $logFile = $this->logPath . $level . '-' . date('Y-m-d') . self::SUFFIX;
        file_put_contents($logFile , $message, FILE_APPEND|LOCK_EX);
    }


    /**
     * 动态调用
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    public function __call(string $method, array $args = [])
    {
        if (!in_array($method, self::LEVEL)) {
            throw new BadFunctionCallException("Method {$method} Not Found In " . __CLASS__);
        }
        array_unshift($args, $method);
        return call_user_func_array([self::instance(), 'log'], $args);
    }


    /**
     * 静态调用
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    public static function __callStatic(string $method, array $args = [])
    {
        if (!in_array($method, self::LEVEL)) {
            throw new BadFunctionCallException("Method {$method} Not Found In " . __CLASS__);
        }
        array_unshift($args, $method);
        return call_user_func_array([self::instance(), 'log'], $args);
    }
}