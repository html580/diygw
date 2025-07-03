<?php

namespace app\api\controller;
use app\BaseController;
use app\diy\model\UserModel;
use EasyWeChat\MiniApp\Application;
use thans\jwt\facade\JWTAuth;
use think\App;

/*
 * 微信小程序
 */

class WexcxController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = true;
    public $isModel = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];
    public $wexcxApp = null;

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $minConfig = config('wechat.mini_program');
        $this->wexcxApp = new Application($minConfig);

    }

    /**
     * 获取用户登录信息
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login()
    {
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
                    log_record($this->model->error,'error');
                    return $this->error($this->isDebug?$this->model->error:'登录失败');
                }
                $userId = $data['id'];
            }
            $token = "bearer" . JWTAuth::builder(['uid' => $userId]);
            $opendata['token'] = $token;
            $data = array_merge($data, $opendata);
            return $this->successData($data);
        } else {
            return $this->errorData($opendata, '登录失败');
        }
    }
}
