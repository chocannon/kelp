<?php
// +----------------------------------------------------------------------
// | SWOOLE事件注册
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Swoole;

use Kelp\Component\Event;
use Kelp\Standard\Singleton;

class SwooleEvent extends Event
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
    }
}