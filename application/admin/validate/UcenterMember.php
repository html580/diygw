<?php
 
namespace app\admin\validate;
use think\Validate;
/**
*  UC验证模型
*/
class UcenterMember extends Validate{
    // 验证规则
    protected $rule = [
        ['username', 'require|unique:UcenterMember|length:6,30', '用户名必须|用户已存在|用户名长度6-30'],
        ['email', 'require|unique:UcenterMember|email|length:1,32', '邮箱必须|邮箱已存在|邮箱格式不正确|邮箱长度不合法'],
        ['mobile', 'unique', '手机号已存在'],
        ['password', 'require|length:6,30', '密码必须|密码长度6-30'],
    ]; 
    protected $scene = array(
        'admin'     => 'username,email,password',//后台管理员注册场景
        // 'password' => 'password,repassword'
    );

}