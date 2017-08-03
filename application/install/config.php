<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络  <http://www.twothink.cn>
// +----------------------------------------------------------------------

/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(
    'ORIGINAL_TABLE_PREFIX' => 'diygw_', //默认表前缀
    // +----------------------------------------------------------------------
    // | 模板替换
    // +----------------------------------------------------------------------
    'view_replace_str'  =>  [
    		'__PUBLIC__'=>__ROOT__.'/static',
    		'__STATIC__' => __ROOT__.'/static/static',
    		'__IMG__'    =>__ROOT__.'/static/install/images',
    		'__CSS__'    => __ROOT__.'/static/install/css',
    		'__JS__'     => __ROOT__.'/static/install/js',
    ],

    'app_init'=>[],

);
