<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace think\addons;

use think\facade\Session;
use think\Db;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use think\Exception;
use think\exception\PDOException;

/**
 * 插件服务
 * @Author: DIY官网  diygwcom@foxmail.com
 */
class Service{
    /*
     * 检测插件是否完整
     *
     * @param string $name 插件名称
     * @return boolean
     * @throws Exception
     */
    public static function check($name)
    {
        if (!$name || !is_dir(DIYGW_ADDON_PATH . $name))
        {
            throw new Exception('插件不存在');
        }
        $addonClass = get_addon_class($name);
        if (!$addonClass)
        {
            throw new Exception("插件主启动程序不存在");
        }
        $addon = new $addonClass();
        if (!$addon->info)
        {
            throw new Exception("插件信息缺失");
        }
        return true;
    }
    /*
     * 检测插件文件是否有冲突
     *
     * @param string $name 插件名称
     * @return boolean
     * @throws AddonException
     */
    public static function checkFiles($name)
    {
        // 检测冲突文件
        $list = self::getFilesList($name, true);
        if ($list)
        {
            //发现冲突文件，抛出异常
            throw new AddonsException("发现冲突文件", -2, ['conflictfile' => $list]);
        }
        return true;
    }
    /**
     * 需检测的全局文件夹目录
     * @return array
     */
    protected static function getCheckDirs()
    {
        return [
            'application',
            'public'
        ];
    }
    /*
     * 获取插件在全局的文件
     * @param string $name 插件名称
     * @param string $isTesting 是否检测文件
     * @return array
     */
    public static function getFilesList($name, $isTesting = false)
    {
        $list = [];
        $addonDir = DIYGW_ADDON_PATH . $name . DS;
        // 扫描插件目录是否有覆盖的文件
        foreach (self::getCheckDirs() as $k => $dir)
        {
            $checkDir = ROOT_PATH . DS . $dir . DS;

            if (!is_dir($checkDir))
                continue;
            //检测到存在插件外目录
            if (is_dir($addonDir . $dir))
            {
                //匹配出所有的文件
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($addonDir . $dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $fileinfo)
                {
                    if ($fileinfo->isFile())
                    {
                        $filePath = $fileinfo->getPathName();
                        $path = str_replace($addonDir, '', $filePath);
                        if ($isTesting)
                        {
                            if (is_file(ROOT_PATH . $path))
                            {
                                $list[] = $path;
                            }
                        }
                        else
                        {
                            $list[] = $path;
                        }
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 获取插件静态资源文件夹目录
     * @param string $name
     * @return string
     */
    protected static function getStaticDir($name)
    {
        return DIYGW_ADDON_PATH . $name . DS . 'static' . DS;
    }
    /**
     * 获取插件静态资源目标文件夹
     * @param string $name
     * @return string
     */
    protected static function getToStaticDir($name)
    {
        $staticDir = ROOT_PATH . str_replace("/", DS, "public/static/addons/{$name}/");
        if (!is_dir($staticDir))
        {
            mkdir($staticDir, 0755, true);
        }
        return $staticDir;
    }

    /*
     * 安装插件
     *
     * @param string $name 插件名称
     * @param boolean $iscover 是否覆盖
     * @return boolean
     * @throws Exception
     */
    public static function install($name, $iscover = false)
    {
        if (!$name || !is_dir(DIYGW_ADDON_PATH . $name))
        {
            throw new Exception('Addon not exists');
        }

        try
        {
            // 检测插件是否完整
            self::check($name);

            if (!$iscover)
            {
                self::checkFiles($name);
            }

            // 执行安装脚本
            Session::set ( 'addons_install_error', null );
            $class = get_addon_class($name);
            $addon = new $class();
            if (! $addon->install()) {
                throw new Exception('执行插件预安装操作失败' . Session::get ( 'addons_install_error' ));
            }

            //复制文件
            /*$sourceAssetsDir = self::getStaticDir($name);
            $destAssetsDir = self::getToStaticDir($name);
            if (is_dir($sourceAssetsDir)) {
                copydirs($sourceAssetsDir, $destAssetsDir);//插件资源文件
            }
            //复制全局文件
            $addonDir = DIYGW_ADDON_PATH . $name . DS;
            foreach (self::getCheckDirs() as $k => $dir)
            {
                if (is_dir($addonDir . $dir))
                {
                    File::copy_dir($addonDir . $dir, ROOT_PATH . $dir);
                }
            }*/
            // SQL导入
            self::execute_sql_file(DIYGW_ADDON_PATH . $name . DS . 'install.sql');
        }
        catch (AddonsException $e){
            throw new AddonsException($e->getMessage(), $e->getCode(), $e->getData());
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }

        return true;
    }
    /**
     * 卸载插件
     *
     * @param string $name
     * @param boolean $iscover 是否强制卸载
     * @return boolean
     * @throws Exception
     */
    public static function uninstall($name, $iscover = false)
    {
        if (!$name || !is_dir(DIYGW_ADDON_PATH . $name))
        {
            throw new Exception('Addon not exists');
        }

        if (!$iscover)
        {
            self::checkFiles($name);
        }
        // 执行卸载脚本
        try
        {
            Session::set ( 'addons_uninstall_error', null );
            $class = get_addon_class($name);
            $addon = new $class();
            if (! $addon->uninstall()) {
                throw new Exception('执行插件预卸载操作失败' . Session::get ( 'addons_uninstall_error' ));
            }

            // 移除插件基础资源目录
            /*$delstaticDir = self::getToStaticDir($name);
            if (is_dir($delstaticDir))
            {
                File::del_dir($delstaticDir);
            }*/

            // 移除插件全局资源文件
            if ($iscover)
            {
                $list = self::getFilesList($name);
                foreach ($list as $k => $v)
                {
                    @unlink(ROOT_PATH . $v);
                }
            }
            // SQL执行
            self::execute_sql_file(DIYGW_ADDON_PATH . $name . DS . 'uninstall.sql');
            // 移除插件目录
//        File::del_dir(DIYGW_ADDON_PATH . $name);
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }

        return true;
    }
    /**
     * 执行SQL文件
     * $sql_path sql文件路径
     * $prefix 替换表前缀
     * @auth diygw diygwcom@foxmail.com
     */
    public static function execute_sql_file($sql_path, $prefix = '')
    {
        if (is_file($sql_path))
        {
            try
            {
                //替换表前缀
                $orginal = config('database.prefix');
                if(empty($prefix))
                    $prefix = $orginal;

                $sql = str_replace(" `{$orginal}"," `{$prefix}", file_get_contents($sql_path));
                // 导入SQL
                Db::getPdo()->exec($sql);
            }
            catch (PDOException $e)
            {
                throw new Exception($e->getMessage());
            }
        }
        return true;
    }
}