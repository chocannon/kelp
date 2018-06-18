<?php
// +----------------------------------------------------------------------
// | 事件
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Component;

use Throwable;
use Kelp\Standard\Singleton;

class Event
{
    use Singleton;

    private $container = [];
    private $allowKeys = [];

    public function __construct(array $allowKeys = [])
    {
        $this->allowKeys = $allowKeys;
    }


    /**
     * 获取事件
     *
     * @param string $key
     * @return void
     */
    public function get(string $key = '')
    {
        if ('' == $key) {
            return $this->container;
        }
        return $this->container[$key] ?? null;
    }


    /**
     * 设置事件
     *
     * @param string $key
     * @param [type] $item
     * @return void
     */
    public function set(string $key, $item)
    {
        if(in_array($key, $this->allowKeys) && is_callable($item)) {
            $this->container[$key][] = $item;
            return true;
        }
        return false;
    }


    /**
     * 删除事件
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key)
    {
        if(isset($this->container[$key])) {
            unset($this->container[$key]);
            return true;
        }
        return false;
    }


    /**
     * 执行事件
     *
     * @param [type] $event
     * @param [type] ...$args
     * @return void
     */
    public function hook($event, ...$args)
    {
        $calls = $this->get($event);
        if(is_array($calls)){
            foreach ($calls as $call){
                try{
                    Invoker::callUserFunc($call, ...$args);
                }catch (Throwable $throwable){
                    throw $throwable;
                }
            }
        }
    }
}