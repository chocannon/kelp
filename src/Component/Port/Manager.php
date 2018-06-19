<?php
// +----------------------------------------------------------------------
// | 端口管理器
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Component\Port;

use RuntimeException;
use Kelp\Server\Event;
use Kelp\Utility\Config;

class Manager
{
    const SOCK_HTTP       = 'http';
    const SOCK_WEB_SOCKET = 'websocket';
    const SOCK_TCP        = 'tcp';


    public static function certInvalid(array $item)
    {
        $setting = Config::instance()->get('server.setting');
        $args    = SWOOLE_SOCK_TCP;
        if (true === $item['socket_ssl'] 
            && is_file($setting['ssl_key_file'] ?? null) 
            && is_file($setting['ssl_cert_file'] ?? null)) 
        {
            $args = SWOOLE_SOCK_TCP | SWOOLE_SSL;
        }
        return $args;
    }


    public static function bind(\Swoole\Server $server, string $default = 'http')
    {
        $ports = Config::instance()->get('port.ports');
        foreach ($ports as $kind => $item) {
            if ($default === $kind) {
                continue;
            }
            $args   = self::certInvalid($item);
            $binder = $server->listen($item['socket_host'], $item['socket_port'], $args);
            if (false === $binder) {
                throw new RuntimeException('监听端口失败:' . $item['socket_port']);
            }
            if (self::SOCK_HTTP === $item['socket_type']) {
                $binder->set(['open_http_protocol' => true]);
                $events = Event::instance()->fire(self::SOCK_HTTP)->get();
                foreach ($events as $event => $callback){
                    $binder->on($event, $callback);
                }
                continue;
            }
            if (self::SOCK_WEB_SOCKET === $item['socket_type']) {
                $binder->set(['open_http_protocol' => true, 'open_websocket_protocol' => true]);
                $events = Event::instance()->fire(self::SOCK_WEB_SOCKET)->get();
                foreach ($events as $event => $callback){
                    $binder->on($event, $callback);
                }
                continue;
            }
            if (self::SOCK_TCP === $item['socket_type']) {
                $events = Event::instance()->fire(self::SOCK_TCP)->get();
                foreach ($events as $event => $callback){
                    $binder->on($event, $callback);
                }
                continue;
            }
        }
    }
}