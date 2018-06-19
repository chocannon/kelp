<?php
// +----------------------------------------------------------------------
// | 配置项获取
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Utility;

use Kelp\Common\Singleton;

class Config
{
    use Singleton;

    const EXT = '.php';

    protected $conf = [];


    public function __construct()
    {
        $path = APPLICATION_CONFIG;
        if(is_dir($path)){
            $this->loadPath($path);
        }
        if (is_file($path)) {
            $this->loadFile($path);
        }
    }


    /**
     * 获取配置项
     *
     * @param string $keyPath
     * @return void
     */
    public function get(string $keyPath = '', $default = null)
    {
        $path = explode('.', $keyPath);
        $temp = $this->conf;
        while ($key = array_shift($path)){
            if(isset($temp[$key])){
                $temp = $temp[$key];
            }else{
                return $default;
            }
        }
        return $temp;
    }


    /**
     * 设置配置项
     *
     * @param string $keyPath
     * @param [type] $value
     * @return void
     */
    public function set(string $keyPath, $value)
    {
        $path = explode('.', $keyPath);
        $temp = &$this->conf;
        while ($key = array_shift($path)) {
            $temp = &$temp[$key];
        }
        $temp = $value;
    }


    /**
     * 载入一个目录的所有文件
     *
     * @param string $confPath
     * @param array $except
     * @return void
     */
    public function loadPath(string $confPath, array $except = [])
    {
        $files = Directory::scan($confPath);
        foreach ($files as $file) {
            if (false == in_array(basename($file), $except)) {
                $this->loadFile($file);
            }
        }
    }


    /**
     * 载入一个文件的配置项
     *
     * @param string $filePath
     * @param boolean $merge
     * @return void
     */
    public function loadFile(string $filePath, bool $merge = false)
    {
        if (is_file($filePath)) {
            $confData = require_once $filePath;
            if (is_array($confData) && !empty($confData)) {
                $basename = strtolower(basename($filePath, self::EXT));
                if (true == $merge) {
                    $this->conf = array_merge($this->conf, $confData);
                } else {
                    $this->conf[$basename] = $confData;
                }
            }
        }
    }
}