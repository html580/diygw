<?php
namespace app\common\util;
class Dir {

    private static $_values = array();
    public $error = "";

    /**
     * 架构函数
     * @param string $path  目录路径
     */
    public function __construct($path = '', $pattern = '*') {
        if (!$path) return false;
        if (substr($path, -1) != "/") $path .= "/";
        $this->listFile($path, $pattern);
    }


    /**
     * 生成目录
     * @param  string  $path 目录
     * @param  integer $mode 权限
     * @return boolean
     */
    public static function create($path, $mode = 0755) {
      if(is_dir($path)) return TRUE;
      $path = str_replace("\\", "/", $path);
      if(substr($path, -1) != '/') $path = $path.'/';
      $temp = explode('/', $path);
      $cur_dir = '';
      $max = count($temp) - 1;
      for($i=0; $i<$max; $i++) {
        $cur_dir .= $temp[$i].'/';
        if (@is_dir($cur_dir)) continue;
        @mkdir($cur_dir, $mode, true);
        @chmod($cur_dir, $mode);
      }
      return is_dir($path);
    }


    /**
     * 取得目录下面的文件信息
     * @param mixed $pathname 路径
     */
    public static function listFile($pathname, $pattern = '*') {

    }

    /**
     * 返回数组中的当前元素（单元）
     * @return array
     */
    public static function current($arr) {
        if (!is_array($arr)) {
            return false;
        }
        return current($arr);
    }



    /**
     * 取得目录中的结构信息
     * @return void
     */
    public static function getList($directory,$type="") {
        $scandir = scandir($directory);
        $dir = [];
        foreach ($scandir as $k => $v) {
            if ($v == '.' || $v == '..') {
                continue;
            }
            if(empty($type)){
                $dir[] = $v;
            }else if(!empty($type)&&pathinfo($v, PATHINFO_EXTENSION)==$type){
                $dir[] = $v;
            }

        }
        return $dir;
    }

    /**
     * 删除目录（包括下面的文件）
     * @return void
     */
    public static function delDir($directory, $subdir = true) {
        if (is_dir($directory) == false) {
            return false;
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {

            if ($file != "." && $file != "..") {
                is_dir("$directory/$file") ?
                                Dir::delDir("$directory/$file") :
                                @unlink("$directory/$file");
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            rmdir($directory);

        }
    }

    /**
     * 删除目录下面的所有文件，但不删除目录
     * @return void
     */
    public static function del($directory) {
        if (is_dir($directory) == false) {
            return false;
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != ".." && is_file("$directory/$file")) {
                unlink("$directory/$file");
            }
        }
        closedir($handle);
    }

    /**
     * 复制目录
     * @return void
     */
    public static function copyDir($source, $destination) {
        if (is_dir($source) == false) {
            return false;
        }
        if (is_dir($destination) == false) {
            mkdir($destination, 0755);
        }
        $handle = opendir($source);
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir("$source/$file")) {
                    Dir::copyDir("$source/$file", "$destination/$file");
                } else {
                    copy("$source/$file", "$destination/$file");
                }
            }
        }
        closedir($handle);
    }

}

?>