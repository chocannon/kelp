<?php
// +----------------------------------------------------------------------
// | 注入容器
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Component;

use ReflectionClass;
use Kelp\Common\Singleton;

class DI
{
    use Singleton;

    private $container = [];


    /**
     * 获取容器对象
     *
     * @param string $key
     * @return void
     */
    public function get(string $key) 
    {
        if (!isset($this->container[$key])) {
            return null;
        }
        $object = $this->container[$key]['object'];
        $params = $this->container[$key]['params'];
        if (is_string($object) && class_exists($object)) {
            $reflection = new ReflectionClass($object);
            $this->container[$key]['object'] = $reflection->newInstanceArgs($params);
            return $this->container[$key]['object'];
        } else {
            return $object;
        }
    }


    /**
     * 添加容器对象
     *
     * @param string $key
     * @param [type] $obj
     * @param [type] ...$args
     * @return void
     */
    public function set(string $key, $obj, ...$args)
    {
        if(count($args) == 1 && is_array($args[0])){
            $args = $args[0];
        }
        $this->container[$key] = [
            'object' => $obj,
            'params' => $args,
        ];
    }


    /**
     * 删除容器对象
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key = '')
    {
        if ('' === $key) {
            $this->container = [];
        } else {
            unset($this->container[$key]);
        }
    }


    /**
     * 清空容器
     *
     * @return void
     */
    public function clear()
    {
        $this->container = [];
    }    
}