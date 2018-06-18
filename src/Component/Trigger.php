<?php
// +----------------------------------------------------------------------
// | 异常捕获
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Component;

use Throwable;
use Kelp\Utility\Logger;

class Trigger
{
    const LOG_FORMAT = "\r\ncode:{code}\r\nfile:{file}\r\nline:{line}\r\nmessage:{message}\r\n";


    /**
     * 记录错误日志
     *
     * @param [type] $msg
     * @param [type] $file
     * @param [type] $line
     * @param [type] $code
     * @return void
     */
    public static function error($code = E_USER_ERROR, $msg, $file = null, $line = null)
    {
        if(null == $file){
            $btrace = debug_backtrace();
            $caller = array_shift($btrace);
            $file   = $caller['file'];
            $line   = $caller['line'];
        }
        $context = [
            'code'    => $code,
            'file'    => $file,
            'line'    => $line,
            'message' => $msg,
        ];
        Logger::debug(self::LOG_FORMAT, $context);
    }


    /**
     * 记录异常
     *
     * @param Throwable $throwable
     * @return void
     */
    public static function throwable(Throwable $throwable)
    {
        $context = [
            'code'    => $throwable->getCode(),
            'file'    => $throwable->getFile(),
            'line'    => $throwable->getLine(),
            'message' => $throwable->getMessage(),
        ];
        Logger::debug(self::LOG_FORMAT, $context);
    }
}