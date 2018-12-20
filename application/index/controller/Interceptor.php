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

    private function init()
    {
        $dashboardid =session("dashboardid");
        $mpid =session("mpid");

        if(empty($dashboardid)||empty($mpid)){
            $isajax = $this->request->request('__isajax__');
            $isxcx = $this->request->request('__isxcx__');
            if($isajax=='true'||$isxcx=='true'){
                echo json_encode(['status'=>'401','message'=>'访问应用超时']);
            }else{
                $this->assign('title','温馨提示');
                $this->assign('message',"访问应用超时");
                return  $this->fetch("login/perror");
            }
            return false;
        }
        $this->mpid=$mpid;
        $this->dashboardid = $dashboardid;

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

        if($this->isLogin){//是否登录
            $this->uid=session("uid".$this->mpid);
            if(empty($this->uid)){
                $url = loginCheck("",true);
                if(!empty($url)){
                    $this->redirect($url);
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

            $fans = WechatService::webOauth($this->request->url(true), 1);
            session('fans',$fans);
            $this->assign('fans',$fans);
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
       // return 1;
        $fans = $this->getFans();
        return $fans['fansinfo']['openid'];
    }




    public function getUid(){
        return $this->uid;
    }
}