<?php
namespace app\super\controller;
use app\BaseController;
use app\common\model\DiyUserModel;
use app\sys\model\UserModel;
use EasyWeChat\Factory;
use thans\jwt\facade\JWTAuth;

class LoginController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = true;
    public $isModel = false;
    public $tokenKey = 'superuid';

    /**
     * 登录用户
     */
    public function login(){
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        //获取用户模型
        $model = new UserModel();
        //查询用户
        $user = $model->where('username',$username)->find();
        if(empty($user)|| ($user && md5($password.$user->salt) != $user->password)){
            $params['username'] = $username;
            $params['status'] = '0';
            return $this->error("用户名或密码错误");
        }else{
            //创建登录用户TOKEN$user->toArray()
            $token = "bearer".JWTAuth::builder([$this->tokenKey => $user->userId]);
            $data = $user->toArray();
            $data['token'] = $token;
            $roles=['admin'];
            $auths=[];
            $data['roles'] = $roles;
            $data['auths'] = $auths;
            unset($data['password']);
            unset($data['salt']);
            $params['username'] = $username;
            $params['status'] = '1';
            return $this->successData($data,"登录成功");
        }
    }

}
