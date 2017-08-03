<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[data]'     => [
        ':action'         => ['data/data/:action']
    ],
    'login' => 'user/login/index',//插件执行路由
    'loginCheck' => 'admin/publics/loginCheck',//插件执行路由
    'page/:dashboardid/:page' => 'home/index/page',
    'adminpage/:dashboardid/:page' => 'admin/index/page',
    'home/addons/:_addons/:_controller/:_action' => 'home/addons/execute',//插件执行路由
];
