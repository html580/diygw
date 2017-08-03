<?php
 
namespace app\admin\validate;
use think\Validate;
/**
*  模型验证模型
*/
class Action extends Validate{
     
    protected $rule = [
        ['name', 'require|/^[a-zA-Z]\w{0,39}$/|unique:Action', '行为标识必须|标识不合法|标识已经存在'],
        ['title', 'require|length:1,80', '标题不能为空|标题长度不能超过80个字符'],
        ['remark', 'require|length:1,140', '行为描述不能超过140个字符'], 
    ];    
}