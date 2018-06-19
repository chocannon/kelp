<?php
// +----------------------------------------------------------------------
// | 服务端口列表
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
use Kelp\Component\Port\Manager as PortManager;
return [
    'default' => 'tcp',
    'ports'   => [
        'http' => [
            'socket_type' => PortManager::SOCK_HTTP,
            'socket_host' => '0.0.0.0',
            'socket_port' => 9502,
            'socket_ssl'  => false,
        ],
        // 'websocket' => [
        //     'socket_type' => PortManager::SOCK_WEB_SOCKET,
        //     'socket_host' => '0.0.0.0',
        //     'socket_port' => 9502,
        //     'socket_ssl'  => true,
        // ],
        'tcp' => [
            'socket_type' => PortManager::SOCK_TCP,
            'socket_host' => '0.0.0.0',
            'socket_port' => 9501,
            'socket_ssl'  => false,
        ],
    ],
];