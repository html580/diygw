<?php
 
namespace app\admin\validate;
use think\Validate; 

class Menu extends Validate{
    // 验证规则
    protected $rule = [
        ['title', 'require', '标题必须填写'],
        ['url', 'require', '链接必须填写'],
        
    ];  

}