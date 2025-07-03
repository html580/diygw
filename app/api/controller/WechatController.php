<?php
namespace app\api\controller;
use app\BaseController;
use app\diy\model\UserModel;
use EasyWeChat\OfficialAccount\Application;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use thans\jwt\facade\JWTAuth;
use think\App;

/*
 * 公众号
 */
class WechatController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = true;
    public $isModel = false;
    //判断不需要登录的方法
    public $notNeedLogin = ['getAppid','getAccesstoken','getSignPackage'];
    public $wechatApp = null;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $minConfig = config('wechat.official_account');
        $this->wechatApp =new Application($minConfig);

    }

    /**
     * 获取公众号ID
     * @return \think\response\Json
     */
    public function getAppid(){
        $minConfig = config('wechat.official_account');
        return $this->successData(['appid'=>$minConfig['app_id']]);
    }

    /**
     * 获取用户登录信息
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function  login(){
        $code = $this->request->param('code');
        try {
            $wechatuser =  $this->wechatApp->getOAuth()->userFromCode($code);
            if($wechatuser->getId()){
                $openid = $wechatuser->getId();
                $type = 'wechat';
                $model = new UserModel();
                //查找获取微信小程序用户
                $user = $model->where('openid',$openid)->where('type',$type)->find();
                $data['openid'] = $openid;
                $data['type'] = $type;
                $data['nickname'] = $wechatuser->getNickname();
                $data['avatar'] = $wechatuser->getAvatar();
                $data['country'] = $wechatuser->getOriginal()['country'];
                $data['province'] = $wechatuser->getOriginal()['province'];
                $data['gender'] = $wechatuser->getOriginal()['sex'];
                if($user){
                    $userId =  $user->toArray()['id'];
                    $data['id'] = $userId;
                    $user->edit($data);
                }else{
                    $model = new UserModel();
                    $model->add($data);
                    $userId = $data['id'];
                }
                $token = "bearer".JWTAuth::builder(['uid' => $userId]);
                $opendata['token'] = $token;
                $opendata['access_token'] = $wechatuser->getAccessToken();
                $opendata['refresh_token'] = $wechatuser->getRefreshToken();
                $data = array_merge($data,$opendata);
                return $this->successData($data);
            }else{
                return $this->errorData($wechatuser,'登录失败');
            }
        }catch (Throwable | Exception| AuthorizeFailedException $e){
            return $this->error("获取用户失败，请重试");
        }
    }

    /**
     * 服务端签名，获取操作权限
     */
    public function  getSignPackage(){
        $url = $this->request->param('url');
        try {
            return $this->successData($this->wechatApp->jssdk->buildConfig([
                'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'hideMenuItems',
                'showMenuItems',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'translateVoice',
                'startRecord',
                'stopRecord',
                'onRecordEnd',
                'playVoice',
                'pauseVoice',
                'stopVoice',
                'uploadVoice',
                'downloadVoice',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage',
                'getNetworkType',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'closeWindow',
                'scanQRCode',
                'chooseWXPay',
                'openProductSpecificView',
                'addCard',
                'chooseCard',
                'openCard'
            ],false,false,false,['wx-open-launch-weapp'],'https://php.diygw.com/pay/index.html'));
        }catch (Throwable | Exception $e){
            return $this->error("获取用户失败，请重试");
        }
    }

}
