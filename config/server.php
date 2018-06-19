<?php
// +----------------------------------------------------------------------
// | 运行服务配置
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
return [
    'name'    => 'KELP',
    'setting' => [
        'backlog'          => 128,
        'daemonize'        => false,
        'worker_num'       => 1,
        'max_request'      => 5000,
        'max_coro_num'     => 1000,
        'task_worker_num'  => 1,
        'task_max_request' => 1000,
        // 'ssl_key_file'     => '',
        // 'ssl_cert_file'    => '',
    ],
];