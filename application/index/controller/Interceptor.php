<?php
namespace app\index\controller;
// +----------------------------------------------------------------------
// | Diygw
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 DIY官网 [ http://www.diygw.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.diygw.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/html580/diygw
// +----------------------------------------------------------------------
use think\Controller;
use think\Db;
use service\WechatService;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Interceptor extends Base
{


    /**
     *  公众号ID
     */
    public $mpid;
    /**
     *  公众号信息
     */
    public $wechatInfo;

    public $dashboardid;

    public $uid;

    public  $title="";
    //是否登录
    public $isLogin = false;
    //是否微信登录
    public $isWechaLogin = false;

    public function initialize()
    {
        $this->assign("title",$this->title);
        $flag = $this->init();
        if(!$flag){
            exit();
        }
    }

    private function getDashboard(){
        $this->dashboardid =session("dashboardid");
        $this->mpid =session("mpid");
        if(empty($this->dashboardid)||empty($this->mpid)){
            $this->dashboardid = $this->request->request("dashboardid");
            $this->mpid = $this->request->request("mpid");
        }
    }

    private function getMpid(){

    }

    private function init()
    {
        $this->getDashboard();

        if(empty($this->dashboardid)||empty($this->mpid)){
            $isajax = $this->request->request('__isajax__');
            $isxcx = $this->request->request('__isxcx__');
            if($isajax=='true'||$isxcx=='true'||$isxcx=='1'){
                return json_encode(['status'=>401,'message'=>'登录超时，请重新登录']);
            }else{
                $this->assign('title','温馨提示');
                $this->assign('message',"登录超时，请重新登录");
                return  $this->fetch("login/perror");
            }
            return false;
        }

        $this->assign('mpid',$this->mpid);
        $this->assign('dashboardid',$this->dashboardid);

        if(empty($this->mpid)||empty($this->wechatInfo)){
            $wechatDefault = Db::name("SystemConfig")->where(['name'=>'wechat_default'])->find();
            $mpid = $wechatDefault['value'];
            $wechatInfo = Db::name('wechat')->where(['id' => $mpid])->find();
            if(empty($wechatInfo)){
                $wechatInfo = Db::name('wechat')->find();
            }
            session('wechatInfo',$wechatInfo);
            session('mpid',$wechatInfo['id']);

            $this->mpid= $wechatInfo['id'];
            $this->wechatInfo =$wechatInfo;
            $this->assign('wechatInfo',$this->wechatInfo);
            $this->assign('mpid',$this->mpid);
        }
        $login = $this->request->request('login');
        if(!empty($login)&&$login=="1"){
            $this->isLogin =true;
        }
        $isxcx = $this->request->request('__isxcx__');
        $token =  $this->request->header('Authorization');
        if($this->isLogin){//是否登录
            if(($isxcx=='true'||$isxcx=='1')&&!empty($token)){
                $this->uid = cache($token)['uid'];
                if(empty($this->uid)){
                    return json_encode(['status'=>401,'message'=>'登录超时，请重新登录']);
                }
            }else{
                $this->uid=session("uid".$this->mpid);
                if(empty($this->uid)){
                    $url = loginCheck("",true);
                    if(!empty($url)){
                        $this->redirect($url);
                    }
                }
            }
        }
        $extendValues = $this->getDashboardExtends();
        $appStyleConfig = $extendValues['appStyleConfig'];
        $appStyle = [];
        if(!empty($appStyleConfig)){
            $appStyle = @json_decode($appStyleConfig,true);
        }
        $this->assign("appStyle",$appStyle);
        $this->assign("extendValues",$extendValues);
        if($this->isWechaLogin){//是否微信登录
            if(($isxcx=='true'||$isxcx=='1')&&!empty($token)) {//微信小程序

            }else{
                $this->getFans();
            }
            /*$fans = WechatService::webOauth($this->request->url(true), 1);
            session('fans',$fans);
            $this->assign('fans',$fans);*/
        }

        return true;
    }

    public function getFans(){
        $fans = session('fans');
        if(empty($fans)){
            $fans = WechatService::webOauth($this->request->url(true), 1);
            session('fans',$fans);
        }
        return $fans;
    }

    public function getOpenId(){
        $isxcx = $this->request->request('__isxcx__');
        $token =  $this->request->header('Authorization');
        if(($isxcx=='true'||$isxcx=='1')&&!empty($token)) {
            return cache($token)['openid'];
        }else{
            $fans = $this->getFans();
            return $fans['fansinfo']['openid'];
        }
    }
    
    public function getUid(){
        if(empty($this->uid)){
            $isxcx = $this->request->request('__isxcx__');
            $token =  $this->request->header('Authorization');
            if(($isxcx=='true'||$isxcx=='1')&&!empty($token)){
                $this->uid = cache($token)['uid'];
            }else{
                $this->uid=  session("uid".$this->mpid);
            }
        }
        return $this->uid;
    }

    public function data()
    {
        return parent::data(); // TODO: Change the autogenerated stub
    }
}