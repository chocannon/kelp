<?php
// +----------------------------------------------------------------------
// | 单例
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Standard;

trait Singleton
{
    private static $instance;

    public static function instance(...$args) {
        if(!isset(self::$instance)) {
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}