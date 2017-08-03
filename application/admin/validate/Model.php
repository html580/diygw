<?php
 
namespace app\admin\validate;
use think\Validate;
/**
*  模型验证模型
*/
class Model extends Validate{
     
    protected $rule = [
        ['name', 'require|/^[a-zA-Z]\w{0,39}$/|unique:Model', '标识不能为空|文档标识不合法|标识已经存在'],
        ['title', 'require|length:1,30', '模型名称不能为空|模型名称长度不能超过30个字符'],
        ['list_grid', 'checkListGrid', '列表定义不能为空'], 
    ];   
    /**
     * 检查列表定义
     * @param type $data
     */
    protected function checkListGrid($value,$data) { 
        return input("post.extend") != 0 || !empty($value);
    }

}