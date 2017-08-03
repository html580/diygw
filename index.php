<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

// 定义__ROOT__
$_root = '/public/';
define('__ROOT__','/public');

if(!is_file(APP_PATH . 'database.php')){
	header('Location: ./public/install.php');
	exit;
}


// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';