<?php

namespace app\controller;

use app\BaseController;
use app\diy\model\UserModel;
use app\diy\validate\UserValidate;
use app\shop\model\SettingModel;
use diygw\sms\Driver as SmsDriver;
use EasyWeChat\MiniApp\Application;
use thans\jwt\facade\JWTAuth;



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
            $data = $this->model->add($data);
            if ($data) {
                return $this->successUserData($data);
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
                return $this->successUserData($user->toArray());
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
                return $this->successUserData($user->toArray());
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
        $token = "bearer" . JWTAuth::builder([$this->tokenKey => $user['id']]);
        $data = $user;
        $data['token'] = $token;
        $username = $user['username'];
        //获取用户角色
        $roles = ['common'];
        $auths = [];
        $data['roles'] = $roles;
        $data['auths'] = $auths;
        unset($data['password']);
        unset($data['salt']);
        $params['username'] = $username;
        $params['status'] = '1';
        event('LoginLog', $params);
        return $this->successData($data, "登录成功");
    }


    /**
     * 小程序获取用户登录信息
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function xcxlogin()
    {
        $settingModel = new SettingModel();
        $setting = $settingModel->where('key','wechat')->find();
        if(empty($setting)){
            return $this->error('请先配置密钥');
        }
        $settingData = $setting->toArray();
        $minConfig = [
            'app_id'  =>  $settingData['appid'],         // AppID
            'secret'  =>  $settingData['secret'],     // AppSecret
        ];
        $this->wexcxApp = new Application($minConfig);
        $userInfo = json_decode($this->request->post('userInfo'), true);
        $code = $this->request->post('code');
        $utils = $this->wexcxApp->getUtils();
        $opendata = $utils->codeToSession($code);
        if (isset($opendata['openid'])) {
            $openid = $opendata['openid'];
            $type = 'weixcx';
            $model = new UserModel();
            //查找获取微信小程序用户
            $user = $model->where('openid', $openid)->where('type', $type)->find();
            $data['openid'] = $openid;
            $data['type'] = $type;
//            $data['nickname'] = $userInfo['nickName'];
//            $data['avatar'] = $userInfo['avatarUrl'];
            $data['country'] = $userInfo['country'];
            $data['province'] = $userInfo['province'];
            $data['gender'] = $userInfo['gender'];
            if ($user) {
                $userId = $user->toArray()['id'];
                $data['id'] = $userId;
                $user->edit($data);
                $data = $user->toArray();
                $data['id'] = $userId;
            } else {
                $model = new UserModel();
                $data = $model->add($data);
                if (!$data) {
                    log_record($this->model->error, 'error');
                    return $this->error($this->isDebug ? $this->model->error : '登录失败');
                }
            }
            return $this->successUserData($data);
        } else {
            return $this->errorData($opendata, '登录失败');
        }
    }
}
