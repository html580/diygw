<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    // 指令定义
    'commands' => [
        'diygw:table' => 'app\command\DiygwTableCommand',
        'diygw:tableandapi' => 'app\command\DiygwTableAndApiCommand',
        'diygw:controller' => 'app\command\DiygwControllerCommand',
        'diygw:command ' => 'app\command\DiygwCommand',
        'diygw:model' => 'app\command\DiygwModelCommand',
        'diygw:validate' => 'app\command\DiygwValidateCommand',
        'diygw:wechat' => 'app\command\DiygwWechatCommand',
    ],
];
