<?php
// +----------------------------------------------------------------------
// | 文件目录操作封装
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Utility;

use Exception;

class Directory
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * 创建目录
     *
     * @param string $dirPath
     * @return void
     */
    public static function create(string $dirPath)
    {
        if (true == is_dir($dirPath)) {
            return true;
        }
        try{
            return mkdir($dirPath, 0755, true);
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 删除目录
     *
     * @param string $dirPath
     * @return void
     */
    public static function delete(string $dirPath)
    {
        if (false == self::clean($dirPath)) {
            return false;
        }
        try{
            return rmdir($dirPath);
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 清空目录
     *
     * @param string $dirPath
     * @return void
     */
    public static function clean(string $dirPath)
    {
        if (false == is_dir($dirPath)) {
            return false;
        }
        try{
            $handler = opendir($dirPath);
            if(false == $handler){
                return false;
            }
            while (false !== ($file = readdir($handler))) {
                if ('.' == $file || '..' == $file) {
                    continue;
                }
                if (false == is_dir($dirPath . self::DS . $file)) {
                    if (false == File::delete($dirPath . self::DS . $file)) {
                        closedir($handler);
                        return false;
                    }
                } else {
                    if (false == self::delete($dirPath . self::DS . $file)){
                        closedir($handler);
                        return false;
                    }
                }
            }
            closedir($handler);
            return true;
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 扫描目录
     *
     * @param string $dirPath
     * @param boolean $recursion
     * @return void
     */
    public static function scan(string $dirPath, bool $recursion = false)
    {
        if (false == is_dir($dirPath)) {
            return null;
        }
        $ret = [];
        try{
            $handler = opendir($dirPath);
            if(false == $handler){
                return null;
            }
            while (false !== ($file = readdir($handler))) {
                if ('.' == $file || '..' == $file) {
                    continue;
                }
                $ret[] = $dirPath . self::DS . $file;
                if (false == $recursion) {
                    continue;
                }
                if (true == is_dir($dirPath . self::DS . $file)) {
                    $ret  = array_merge($ret, self::scan($dirPath . self::DS . $file, $recursion));
                }
            }
            closedir($handler);
            return $ret;
        }catch (Exception $e){
            return null;
        }
    }


    /**
     * 复制目录
     *
     * @param string $dirPath
     * @param string $targetPath
     * @return void
     */
    public static function copy(string $dirPath, string $targetPath)
    {
        if (false == is_dir($dirPath)) {
            return false;
        }
        if (false == file_exists($targetPath) && false == self::create($targetPath)) {
            return false;
        }
        try{
            $handler = opendir($dirPath);
            if(false == $handler){
                return false;
            }
            while (false !== ($file = readdir($handler))) {
                if ('.' == $file || '..' == $file) {
                    continue;
                }
                if (false == is_dir($dirPath . self::DS . $file)) {
                    if(false == File::copy($dirPath . self::DS . $file, $targetPath . self::DS . $file)){
                        closedir($handler);
                        return false;
                    }
                } else {
                    if(false == self::copy($dirPath . self::DS . $file, $targetPath . self::DS . $file)){
                        closedir($handler);
                        return false;
                    };
                }
            }
            closedir($handler);
            return true;
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 移动目录
     *
     * @param string $dirPath
     * @param string $targetPath
     * @return void
     */
    public static function move(string $dirPath, string $targetPath)
    {
        try{
            if(self::copy($dirPath, $targetPath)){
                return self::delete($dirPath);
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
}