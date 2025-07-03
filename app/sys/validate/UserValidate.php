<?php
declare (strict_types = 1);

namespace app\sys\validate;

use app\common\validate\BaseValidate;
use app\sys\model\UserModel;

class UserValidate extends BaseValidate
{
    protected $rule =   [
        'username'  => 'require|checkUsername',
        'email' => 'email',
    ];

    protected $message  =   [
        'username.require' => '用户登录名不能为空'
    ];

    // 自定义验证规则
    public function checkUsername($value,$rule,$data=[])
    {
        $user = UserModel::where(['username' => $value])->find();
        $param = \request()->param();
        if (($user&&empty($param['userId']))||($user&&$param['userId']!=$user['userId'])) {
            return '用户登录名已存在';
        }
        return true;
    }
}
