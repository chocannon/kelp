<?php
// +----------------------------------------------------------------------
// | 调用器
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Component;

use Closure;
use Throwable;
use \Swoole\Process;
use RuntimeException;

class Invoker
{
    public static function exec(callable $callable, int $timeout = 30000, ...$params)
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGALRM, function () {
            Process::alarm(-1);
            throw new RuntimeException('function timeout');
        });
        try {
            Process::alarm($timeout);
            $ret = self::callUserFunc($callable, ...$params);
            Process::alarm(-1);
            return $ret;
        } catch(Throwable $throwable) {
            throw $throwable;
        }
    }


    public static function callUserFunc(callable $callable, ...$params)
    {
        if($callable instanceof Closure){
            return $callable(...$params);
        }
        if(is_array($callable) && is_object($callable[0])) {
            $class  = $callable[0];
            $method = $callable[1];
            return $class->$method(...$params);
        }
        if(is_array($callable) && is_string($callable[0])) {
            $class  = $callable[0];
            $method = $callable[1];
            return $class::$method(...$params);
        }
        if(is_string($callable)) {
            return $callable(...$params);
        }
        return null;
    }

    public static function callUserFuncArray(callable $callable,array $params)
    {
        if($callable instanceof Closure){
            return $callable(...$params);
        }
        if(is_array($callable) && is_object($callable[0])){
            $class = $callable[0];
            $method = $callable[1];
            return $class->$method(...$params);
        }
        if(is_array($callable) && is_string($callable[0])){
            $class = $callable[0];
            $method = $callable[1];
            return $class::$method(...$params);
        }
        if(is_string($callable)){
            return $callable(...$params);
        }
        return null;
    }
}