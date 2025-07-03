<?php

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = array(
        'os' => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php' => array('PHP版本', '8.0', 'Sodium+', PHP_VERSION, 'success'),
        'upload' => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd' => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'sodium' => array('SODIUM库', '2.0', '2.0+', '未知', 'success'),
    );

    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
    }

    //附件上传检测
    if (@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }

    if (!extension_loaded('sodium')) {
        $items['sodium'][3] = '未开启';
        $items['sodium'][4] = 'error';
    } else {
        $items['sodium'][3] = '已开启';
    }

    unset($tmp);
    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = array(
        array('dir', '可写', 'success', '../app'),
        array('dir', '可写', 'success', '../config'),
        array('dir', '可写', 'success', 'static'),
    );
    foreach ($items as &$val) {
        if ('dir' == $val[0]) {
            if (!is_writable($val[3])) {
                if (is_dir($val[3])) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($val[3])) {
                if (!is_writable($val[3])) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($val[3]))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func()
{
    $items = array(
        array('pdo','支持','success','类'),
        array('pdo_mysql','支持','success','模块'),
        array('fileinfo','支持','success','模块'),
        array('file_get_contents', '支持', 'success','函数'),
        array('mb_strlen', '支持', 'success','函数'),
        array('pathinfo', '支持', 'success','函数'),
        array('curl','支持','success','模块'),
    );
    foreach ($items as &$val) {
        if(('类'==$val[3] && !class_exists($val[0]))
            || ('模块'==$val[3] && !extension_loaded($val[0]))
            || ('函数'==$val[3] && !function_exists($val[0]))
        ){
            $val[1] = '不支持';
            $val[2] = 'error';
        }
    }

    return $items;
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function install_show_msg($msg, $class = true)
{
    if ($class) {
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\")</script>";
    } else {
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"error\")</script>";
        exit;
    }
}

/**
 * 过滤数据
 * @param  array $data 过滤数据
 */
function filter_string($data)
{
    if ($data === NULL) {
        return false;
    }
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = filter_string($v);
        }
        return $data;
    } else {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * 写入配置文件
 * @param $config
 * @return array 配置信息
 */
function write_config($config){
    if(is_array($config)){
        
        //读取配置内容
        $conf = file_get_contents(root_path() . 'app/install/data/env.tpl');

        $secret  = md5(uniqid().time().rand(0, 60));
        $config['secret'] = $secret;
        // 替换配置项
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }

        //写入应用配置文件
        if(file_put_contents(root_path() . '.env', $conf)){
            install_show_msg('配置文件写入成功');
        } else {
            install_show_msg(root_path() . '.env'.'配置文件写入失败！', 'error');
        }

        return '';
    }
}












