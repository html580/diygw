<?php
 
namespace app\admin\validate;
use think\Validate; 

class Channel extends Validate{
     
    protected $rule = [ 
        ['title', 'require', '标题不能为空'],
        ['url', 'require', 'URL不能为空'], 
    ];   
}