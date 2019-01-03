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
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Login extends Controller
{


    public function index()
    {

        if($this->request->isAjax()){
            $data= $this->request->request();
            $member = Db::name('Member')->where('username',$data['username'])->find();
            if(empty($member)){
                echo json_encode(['status'=>'error','message'=>'账号不存在!']);
                return;
            }
            $password = md5($data['password'] . $member['rand']);
            if($member['password']!=$password){
                echo json_encode(['status'=>'success','message'=>'用户名或密码错误，请重新登录!']);
                return;
            }
            session('uid'.session('mpid'),$member['uid']);
            cookie('uid'.session('mpid'),$member['uid']);
            if(session('login_pre_url')){
                echo json_encode(['status'=>'success','message'=>'登录成功!','redirecturl'=>$_SESSION['login_pre_url']]);
                return;
            }else{
                echo json_encode(['status'=>'success','message'=>'登录成功!']);
                return;
            }
        }else{

            if(session('uid'.session('mpid'))){
                $this->redirect('@index/home/index');
            }else{
                return $this->fetch();
            }
        }

    }

    public function login(){
        $data= $this->request->request();

        $member = Db::name('Member')->where('username',$data['username'])->where('mpid',session('mpid'))->find();
        if(empty($member)){
            echo json_encode(['status'=>'error','message'=>'账号不存在!']);
        }
        $password = md5($data['password'] . $member['rand']);
        if($member['password']!=$password){
            echo json_encode(['status'=>'success','message'=>'用户名或密码错误，请重新登录!']);
        }
        session('uid'.session('mpid'),$member['id']);
        cookie('uid'.session('mpid'),$member['uid']);
        echo json_encode(['status'=>'success','message'=>'登录册成功!']);
    }
    public function register()
    {
        $data= $this->request->request();
        $member = Db::name('Member')->where('username',$data['username'])->where('mpid',session('mpid'))->find();
        if($member){
            echo json_encode(['status'=>'error','message'=>'账号已存在!']);
        }else{
            unset($data['comfirm-password']);
            $data['rand']=1;

            $rand = getRandChar(4);
            $password = md5($data['password'] . $rand);
            $data['password']=$password;
            $data['rand']=$rand;
            $data['status']=1;
            $data['mpid'] = session('mpid');
            $data['register_ip']=$this->request->ip();
            $data['refresh_time']=time();
            $data['last_login_ip']=$this->request->ip();
            $data['last_login_time']=time();
            $data['status']=1;
            $id = Db::name('Member')->insertGetId($data);
            session('uid'.$data['mpid'],$id);
            cookie('uid'.$data('mpid'),$id);
            echo json_encode(['status'=>'success','message'=>'注册成功!']);
        }

    }

    public function logout(){
        session('uid'.session('mpid'),null);
        cookie('uid'.session('mpid'),null);
        $this->redirect('@index/home/index');
    }

    public function check()
    {

        header("Access-Control-Allow-Origin: *");
        $uid = session('uid'.session('mpid'));
        if(empty($uid)){
            $url = $this->request->server('HTTP_REFERER', $this->request->url(true), null);
            loginCheck($url);
            $uid = session('uid'.session('mpid'));
            if(empty($uid)){
                echo "0";
            }else{
                echo "1";
            }
        }else{
            echo "1";
        }

    }
}