<?php
// +----------------------------------------------------------------------
// | 运行服务配置
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
return [
    'main'=>[
        'host'      => '0.0.0.0',
        'port'      => 9501,
        'serv_name' => 'KelpServer',
        'serv_type' => 'http',
        'daemonize' => false,
        'sock_type' => SWOOLE_TCP,
        'run_mode'  => SWOOLE_PROCESS,
        'setting'   => [
            'task_worker_num'  => 1,
            'task_max_request' => 10,
            'max_request'      => 5000,
            'worker_num'       => 1,
        ],
    ],
];