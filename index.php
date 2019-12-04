<?php
// +----------------------------------------------------------------------
// | Diygw
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 DIY官网 [ http://www.diygw.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.diygw.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/html580/diygw
// +----------------------------------------------------------------------

namespace think;

define('IN_DIYGW_COM', true);
define('DS', DIRECTORY_SEPARATOR);
defined('THINK_PATH') or define('THINK_PATH', __DIR__ . DS.'thinkphp'.DS);
define('LIB_PATH', THINK_PATH . 'library' . DS);
define('CORE_PATH', LIB_PATH . 'think' . DS);
define('TRAIT_PATH', LIB_PATH . 'traits' . DS);
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
defined('ROOT_PATH') or define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);

// 加载基础文件
require __DIR__ . '/thinkphp/base.php';
// think文件检查，防止TP目录计算异常
file_exists('think') || touch('think');

// 检查是否安装
if(!is_file('./config/install.lock')){
    define('IN_DIYGW_INSTALL', true);

    // 检测PHP环境
    if (version_compare(PHP_VERSION, '5.4.0', '<')) die('require PHP > 5.4.0 !');
    // 绑定模块
    Container::get('app', [__DIR__ . '/application/'])->bind("install")->run()->send();
}else{
    define('IN_DIYGW_INSTALL', false);
// 执行应用并响应
    Container::get('app', [__DIR__ . '/application/'])->run()->send();
}






