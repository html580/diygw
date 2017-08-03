<?php

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 定义__ROOT__
if (!defined('__ROOT__')) {
    $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
    define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
}
define ( 'BIND_MODULE','install');
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

