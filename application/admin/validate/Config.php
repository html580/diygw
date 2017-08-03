<?php

namespace app\admin\validate;
use think\Validate;
/**
 *  配置验证模型
 */
class Config extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|unique:config', '标识不能为空|标识已经存在'],
        ['title', 'require', '名称不能为空']
    ];

}