<?php

namespace app\admin\controller;

use app\BaseController;
use app\sys\model\RoleModel;
use app\sys\model\UserModel;
use app\sys\validate\UserValidate;
use diygw\sms\Driver as SmsDriver;
use thans\jwt\facade\JWTAuth;
use think\facade\Db;


class LoginController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = true;
    public $isModel = false;

    /**
     * 注册用户
     */
    public function register()
    {
        $userValidate = new UserValidate();
        $data = $this->request->param();
        $this->model = new UserModel();
        if ($userValidate->checkData()) {
            if ($this->model->add($data)) {
                return $this->success('注册成功');
            } else {
                return $this->error('注册失败');
            }
        }
    }


    /**
     * 登录用户
     */
    public function login()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        //获取用户模型
        $model = new UserModel();
        //如果手机号码登录，增加验证码校验
        $phone = $this->request->param('phone');
        $code = $this->request->param('code');
        if(!empty($phone)&&!empty($code)){
            $user = $model->where('phone', $phone)->find();
            $check = checkSmsCode($phone,$code);
            if($check){
                return $this->successUserData($user);
            }else{
                return $this->error("验证码输入错误");
            }
        }else{
            //查询用户
            $user = $model->where('username', $username)->find();
            if (empty($user) || ($user && md5($password . $user->salt) != $user->password)) {
                $params['username'] = $username;
                $params['status'] = '0';
                event('LoginLog', $params);
                return $this->error("用户名或密码错误");
            } else {
                return $this->successUserData($user);
            }
        }
    }

    public function auth()
    {
        $type = $this->request->post('type');
        if (method_exists($this, $type)) {
            return $this->$type();
        } else {
            return $this->error('请实现' . $type . '相关登录方法');
        }
    }

    public function weixin()
    {
        return $this->error('此方法已过期，请参照api/wexcx/login相关实现');
    }

    /**
     * 发送登录验证码
     * @return \think\response\Json
     */
    public function code(){
        // 实例化存储驱动
        $sms = new SmsDriver();
        $phone = $this->request->param("phone");
        if(empty($phone)){
            return $this->error("手机号码不能为空");
        }
        //获取登录编码
        $templateCode = \config('sms.templates.login');
        $code = getCode($phone);
        if($code){
            return $this->error("不要重复发送，请耐心等待");
        }
        $code = getSmsCode($phone);
        $templateParam = ["code"=>$code];
        try {
            $sms->sendSms($phone,$templateCode,$templateParam);
        }catch (Exception $e){
            return $this->error("发送失败".$e->getMessage());
        }
        return $this->success("发送成功");
    }


    /**
     * @param mixed $user
     * @param mixed $username
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function successUserData(mixed $user)
    {
        //创建登录用户TOKEN$user->toArray()
        $token = "bearer" . JWTAuth::builder([$this->tokenKey => $user->userId]);
        $data = $user->toArray();
        $data['token'] = $token;
        $roles = [];
        $auths = [];
        //获取用户角色
        if (!empty($user['roleIds'])) {
            //如果角色为空时，不处理
            if(isset($data['roleIds'])&&!empty($data['roleIds'])){
                $roleModel = new RoleModel();
                $roleList = $roleModel->whereIn('role_id', explode(",", $data['roleIds']))->select()->toArray();
                foreach ($roleList as $role) {
                    $roles[] = $role['roleKey'];
                }
            }

            $username = $user['username'];
            if ($username == 'admin' || in_array('admin', $roles)) {
                $permission = Db::table('sys_menu')->alias('m')->where("m.permission IS NOT NULL AND m.permission != ''")->fieldRaw('GROUP_CONCAT( m.permission) permission')->find();
                $auths = explode(",", $permission['permission']);
            } else if(isset($data['roleIds'])&&!empty($data['roleIds'])) {
                $permission = Db::table('sys_menu')
                    ->alias('m')
                    ->join('sys_role_menu r', 'm.menu_id = r.menu_id ')
                    ->where("m.permission IS NOT NULL AND m.permission != ''")
                    ->whereIn('r.role_id', explode(",", $data['roleIds']))
                    ->fieldRaw('GROUP_CONCAT( m.permission) permission')->find();
                $auths = explode(",", $permission['permission']);
            }

        }
        if (empty($roles)) {
            $roles = ['common'];
        }
        $data['roles'] = $roles;
        $data['auths'] = $auths;
        unset($data['password']);
        unset($data['salt']);
        $params['username'] = $username;
        $params['status'] = '1';
        event('LoginLog', $params);
        return $this->successData($data, "登录成功");
    }
}
