<?php
// +----------------------------------------------------------------------
// | 进程管理器
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Process;

use Kelp\Common\Singleton;

class Manager
{
    use Singleton;


    public static function setProcessName(string $processName)
    {
        if (PHP_OS == 'Darwin') {
            return;
        }
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($processName);
        } else {
            swoole_set_process_name($processName);
        }
    }
}