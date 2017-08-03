<?php
 
namespace app\admin\validate;
use think\Validate; 

class AuthGroup extends Validate{
    // 验证规则
    protected $rule = [
        ['title', 'require', '必须设置用户组标题'],
        ['description', 'length:0,80', '描述最多80字符'], 
    ];   

}