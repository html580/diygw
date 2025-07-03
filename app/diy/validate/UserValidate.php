<?php
declare (strict_types = 1);

namespace app\diy\validate;

use app\common\validate\BaseValidate;
use app\diy\model\UserModel;

class UserValidate extends BaseValidate
{
    protected $rule =   [
        'username'  => 'checkUsername',
        'email' => 'email',
    ];

    protected $message  =   [
        'username.require' => '用户登录名不能为空'
    ];

    // 自定义验证规则
    public function checkUsername($value,$rule,$data=[])
    {
        $param = \request()->param();
        //由于来源于微信小程序登录时，没有username,所以不检验
        if(!empty($value)&&empty($param['id'])){
            $user = UserModel::where(['username' => $value])->find();
            if (($user&&empty($param['userId']))||($user&&$param['userId']!=$user['userId'])) {
                return '用户登录名已存在';
            }
        }
        return true;
    }
}
