<?php
if (!(defined('IN_DIYGW_COM')))
{
    exit('Access Denied');
}
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
use think\Db;
use service\WechatService;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-12-04
 * Time: 下午 7:57
 */
function getFans($preurl="",$redirect=false){
    $fans = session('fans');
    if(empty($fans)){
        if(empty($preurl)){
            $url = app('request')->url(true);
        }else{
            $url = $preurl;
        }
        $fans = WechatService::webOauth($url, 1,$redirect);
        session('fans',$fans);
    }
    return $fans;
}

function loginCheck($preurl="",$redirect=false){
    $mpid= session("mpid");
    $uid=session("uid".$mpid);
    if(empty($uid)){
        $wechatInfo = session('wechatInfo');
        if(empty($wechatInfo)){
            $wechatInfo = Db::name('wechat')->where(['id' => $mpid])->find();
            if(empty($wechatInfo)){
                $wechatInfo = Db::name('wechat')->find();
            }
            session('wechatInfo',$wechatInfo);
            session('mpid',$wechatInfo['id']);
        }
        if($wechatInfo['autologin']){
            $fans =  getFans($preurl,$redirect);
            $auth = Db::name("MemberAuth")->where(['mpid'=>$mpid,'openid'=>$fans['openid']])->find();
            if(empty($auth)){
                $rand = getRandChar(4);
                $password = md5($fans['openid'] . $rand);
                $data['password']=$password;
                $data['username']=$fans['fansinfo']['nickname'];
                $data['nickname']=$fans['fansinfo']['nickname'];
                $data['headimgurl']=$fans['fansinfo']['headimgurl'];
                $data['rand']=$rand;
                $data['status']=1;
                $data['mpid'] = $mpid;
                $data['register_ip']=app('request')->ip();
                $data['refresh_time']=time();
                $data['last_login_ip']=app('request')->ip();
                $data['last_login_time']=time();
                $data['status']=1;
                $id = Db::name('Member')->insertGetId($data);

                $authdata['uid']=$id;
                $authdata['openid']=$fans['openid'];
                $authdata['mpid']=$mpid;
                $authdata['type']="1";
                Db::name('MemberAuth')->insertGetId($authdata);
                session('uid'.$mpid,$id);
                cookie('uid'.$mpid,$id);
            }else{
                session('uid'.$mpid,$auth['uid']);
                cookie('uid'.$mpid,$auth['uid']);
            }
        }else{
            if(empty($preurl)){
                $url = url();
            }else{
                $url = $preurl;
            }
            session('login_pre_url',$url);
            $resultUrl = '@index/login/index';
            return $resultUrl;
        }
    }
    return null;
}