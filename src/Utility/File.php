<?php
// +----------------------------------------------------------------------
// | 文件操作封装
// +----------------------------------------------------------------------
// | Author: qinghecao@outlook.com
// +----------------------------------------------------------------------
namespace Kelp\Utility;

use Exception;

class File
{
    /**
     * 创建文件
     *
     * @param string $filePath
     * @param boolean $overwrite
     * @return void
     */
    public static function create(string $filePath, bool $overwrite = true)
    {
        if (is_file($filePath) && $overwrite == false) {
            return false;
        } elseif (is_file($filePath) && $overwrite == true) {
            if(false == self::delete($filePath)){
                return false;
            }
        }
        if (false == Directory::create(dirname($filePath))) {
            return false;
        }
        try{
            return touch($filePath);
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 复制文件
     *
     * @param string $filePath
     * @param string $targetPath
     * @param boolean $overwrite
     * @return void
     */
    public static function copy(string $filePath, string $targetPath, bool $overwrite = true)
    {
        if (false == is_file($filePath)) {
            return false;
        }
        if (is_file($targetPath) && $overwrite == false) {
            return false;
        } elseif (is_file($targetPath) && $overwrite == true) {
            if(false == self::delete($targetPath)){
                return false;
            }
        }
        if (false == Directory::create(dirname($targetPath))) {
            return false;
        }
        try{
            return copy($filePath, $targetPath);
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 移动文件
     *
     * @param string $filePath
     * @param string $targetPath
     * @param boolean $overwrite
     * @return void
     */
    public static function move(string $filePath, string $targetPath, bool $overwrite = true)
    {
        try{
            if(self::copy($filePath, $targetPath, $overwrite)){
                return self::delete($filePath);
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 删除文件
     *
     * @param string $filePath
     * @return void
     */
    static function delete(string $filePath)
    {
        try{
            unlink($filePath);
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}