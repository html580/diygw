<?php
/**
 * User: diygw.com
 * Info: 文件夹处理类
 */
namespace diygw;

class FileUtil
{

    /**
     * 创建目录
     * @param $dir  目录名
     * @return boolean true 成功， false 失败
     */
    public static  function mk_dirs($path) {
        if (!is_dir($path)) {
            self::mk_dirs(dirname($path));
            mkdir($path);
        }
        return is_dir($path);
    }

    /**
     * 创建目录
     *
     * @param $dir  目录名
     *
     * @return boolean true 成功， false 失败
     */
    public static function mk_dir($dir)
    {
        $dir = rtrim($dir, '/').'/';
        if ( ! is_dir($dir)) {
            if (mkdir($dir, 0700, true) == false) {
                return false;
            }
            return true;
        }

        return true;
    }


    /**
     * 删除目录.
     *
     * @param string $path
     *                      目录位置
     * @param bool   $clean
     *                      true: 不删除目录，仅删除目录内文件; false: 整个目录全部删除
     *
     * @return bool
     */
    public static function rm_dirs($path, $clean = false) {
        if (!is_dir($path)) {
            return false;
        }
        $files = glob($path . '/*');
        if ($files) {
            foreach ($files as $file) {
                is_dir($file) ? self::rm_dirs($file) : @unlink($file);
            }
        }

        return $clean ? true : @rmdir($path);
    }

    /**
     * 读取文件内容
     *
     * @param $filename  文件名
     *
     * @return string 文件内容
     */
    public static function read_file($filename)
    {
        $content = '';
        if (function_exists('file_get_contents')) {
            @$content = file_get_contents($filename);
        } else {
            if (@$fp = fopen($filename, 'r')) {
                @$content = fread($fp, filesize($filename));
                @fclose($fp);
            }
        }

        return $content;
    }

    /**
     * 写文件
     *
     * @param $filename  文件名
     * @param $writetext 文件内容
     * @param $openmod   打开方式
     *
     * @return boolean true 成功, false 失败
     */
    public static function write_file($filename, $writetext, $openmod = 'w')
    {
        if (@$fp = fopen($filename, $openmod)) {
            flock($fp, 2);
            fwrite($fp, $writetext);
            fclose($fp);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除目录
     *
     * @param $dirName      原目录
     *
     * @return boolean true 成功, false 失败
     */
    public static function del_dir($dirName)
    {
        if ( ! file_exists($dirName)) {
            return false;
        }

        $dir = opendir($dirName);
        while ($fileName = readdir($dir)) {
            $file = $dirName.'/'.$fileName;
            if ($fileName != '.' && $fileName != '..') {
                if (is_dir($file)) {
                    self::del_dir($file);
                } else {
                    unlink($file);
                }
            }
        }
        closedir($dir);

        return rmdir($dirName);
    }

    /**
     * 复制目录
     *
     * @param $surDir   原目录
     * @param $toDir    目标目录
     *
     * @return boolean true 成功, false 失败
     */
    public static function copy_dir($surDir, $toDir)
    {
        $surDir = rtrim($surDir, '/').'/';
        $toDir  = rtrim($toDir, '/').'/';
        if ( ! file_exists($surDir)) {
            return false;
        }

        if ( ! file_exists($toDir)) {
            self::mk_dirs($toDir);
        }
        $file = opendir($surDir);
        while ($fileName = readdir($file)) {
            $file1 = $surDir.'/'.$fileName;
            $file2 = $toDir.'/'.$fileName;
            if ($fileName != '.' && $fileName != '..') {
                if (is_dir($file1)) {
                    self::copy_dir($file1, $file2);
                } else {
                    copy($file1, $file2);
                }
            }
        }
        closedir($file);

        return true;
    }

    /**
     * 列出目录
     *
     * @param $dir  目录名
     *
     * @return 目录数组。列出文件夹下内容，返回数组 $dirArray['dir']:存文件夹；$dirArray['file']：存文件
     */
    public static function get_dirs($dir)
    {
        $dir          = rtrim($dir, '/').'/';
        $dirArray[][] = null;
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            $j = 0;
            while (false !== ($file = readdir($handle))) {
                if (is_dir($dir.$file)) {
                    //判断是否文件夹
                    $dirArray['dir'][$i] = $file;
                    $i++;
                } else {
                    $dirArray['file'][$j] = $file;
                    $j++;
                }
            }
            closedir($handle);
        }

        return $dirArray;
    }

    /**
     * 取得目录下面的文件信息
     * @access public
     *
     * @param mixed $pathname 路径
     */
    public static function listFile($pathname, $pattern = '*')
    {
        if (strpos($pattern, '|') !== false) {
            $patterns = explode('|', $pattern);
        } else {
            $patterns[0] = $pattern;
        }
        $i   = 0;
        $dir = [];
        foreach ($patterns as $pattern) {
            $list = glob($pathname.$pattern);
            if ($list !== false) {
                foreach ($list as $file) {
                    //$dir[$i]['filename']    = basename($file);
                    //basename取中文名出问题.改用此方法
                    //编码转换.把中文的调整一下.
                    $dir[$i]['filename'] = preg_replace('/^.+[\\\\\\/]/', '', $file);
//                    $dir[$i]['pathname'] = realpath($file);
                    $dir[$i]['owner']    = fileowner($file);
                    $dir[$i]['perms']    = fileperms($file);
                    $dir[$i]['inode']    = fileinode($file);
                    $dir[$i]['group']    = filegroup($file);
//                    $dir[$i]['path']     = dirname($file);
                    $dir[$i]['atime']    = fileatime($file);
                    $dir[$i]['ctime']    = filectime($file);
                    $dir[$i]['size']     = filesize($file);
                    $dir[$i]['type']     = filetype($file);
                    $dir[$i]['ext']      = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                    $dir[$i]['mtime']    = filemtime($file);
                    $dir[$i]['isDir']    = is_dir($file);
                    $dir[$i]['isFile']   = is_file($file);
                    $dir[$i]['isLink']   = is_link($file);
                    //$dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($file):'';
                    $dir[$i]['isReadable'] = is_readable($file);
                    $dir[$i]['isWritable'] = is_writable($file);
                    $i++;
                }
            }
        }
        // 对结果排序 保证目录在前面
        usort($dir, function ($a, $b) {
            if (($a["isDir"] && $b["isDir"]) || ( ! $a["isDir"] && ! $b["isDir"])) {
                return $a["filename"] > $b["filename"] ? 1 : -1;
            } else {
                if ($a["isDir"]) {
                    return -1;
                } elseif ($b["isDir"]) {
                    return 1;
                }
                if ($a["filename"] == $b["filename"]) {
                    return 0;
                }

                return $a["filename"] > $b["filename"] ? -1 : 1;
            }
        });

        return $dir;
    }

    /**
     * 统计文件夹大小
     *
     * @param $dir  目录名
     *
     * @return number 文件夹大小(单位 B)
     */
    public static function get_size($dir)
    {
        $dirlist = opendir($dir);
        $dirsize = 0;
        while (false !== ($folderorfile = readdir($dirlist))) {
            if ($folderorfile != "." && $folderorfile != "..") {
                if (is_dir("$dir/$folderorfile")) {
                    $dirsize += self::get_size("$dir/$folderorfile");
                } else {
                    $dirsize += filesize("$dir/$folderorfile");
                }
            }
        }
        closedir($dirlist);

        return $dirsize;
    }

    /**
     * 检测是否为空文件夹
     *
     * @param $dir  目录名
     *
     * @return boolean true 空， fasle 不为空
     */
    public static function empty_dir($dir)
    {
        return (($files = @scandir($dir)) && count($files) <= 2);
    }

    /**
     * 文件缓存与文件读取
     *
     * @param $name    文件名
     * @param $value   文件内容,为空则获取缓存
     * @param $path    文件所在目录,默认是当前应用的DATA目录
     * @param $cached  是否缓存结果,默认缓存
     *
     * @return 返回缓存内容
     */
    public function cache($name, $value = '', $path = DATA_PATH, $cached = true)
    {
        static $_cache = array();
        $filename = $path.$name.'.php';
        if ('' !== $value) {
            if (is_null($value)) {
                // 删除缓存
                return false !== strpos($name, '*') ? array_map("unlink", glob($filename)) : unlink($filename);
            } else {
                // 缓存数据
                $dir = dirname($filename);
                // 目录不存在则创建
                if ( ! is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $_cache[$name] = $value;

                return file_put_contents($filename, strip_whitespace("<?php\treturn ".var_export($value, true).";?>"));
            }
        }
        if (isset($_cache[$name]) && $cached == true) {
            return $_cache[$name];
        }

        // 获取缓存数据
        if (is_file($filename)) {
            $value         = include $filename;
            $_cache[$name] = $value;
        } else {
            $value = false;
        }

        return $value;
    }

    /**
     * 判断文件或文件夹是否可写.
     *
     * @param string $file 文件或目录
     *
     * @return bool
     */
    public static function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/').'/'.md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);

            return true;
        } elseif ( ! is_file($file) or ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }
        fclose($fp);

        return true;
    }

}