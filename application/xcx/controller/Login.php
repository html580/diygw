<?php
namespace app\xcx\controller;
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
use WeMini\Crypt;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Login extends Controller
{

    public function wechatSignUp(){

    }

    public function wechatSignIn(){

    }


    public function decryptData(){

            $code = input("code", '', 'htmlspecialchars_decode');
            $rawData = input("rawData", '', 'htmlspecialchars_decode');
            $signature = input("signature", '', 'htmlspecialchars_decode');
            $encryptedData = input("encryptedData", '', 'htmlspecialchars_decode');
            $iv = input("iv", '', 'htmlspecialchars_decode');
            //dashboardId:13067
//            $encryptedData='/IbXIWgfk2AYfj7x4XGnBDv0MrPL/E6j5OT65WK1Xy1Brj9DPqIkCozFCtwbdXVFBDvj90fA2Gj9WmTWUd9uNG6R2itRUbkh9e9zbakX0Pur9BV80VKWg7qw3f1OS/o//elrvFiM1CAqOwaSf1iNri75HwzHRs14f5yoOlC/NpOVMnpGfAJECl23VMtkXfqtHPJX45n1eMuERhFSnZH+VgqIT7cEo5sUB5IzIV0in+9i7wEvxFSoWo+/++1U7Do3HnepzX4F3quJKTMgQntX6ag2/A1V4RtGy8LMOypYaNG/xdpLKaRJqzTcknl6y2vxts5Aff5UtnYRjdLAg+cEXn5m30XA+Cl2AhhGWqC5QbhmWQ1FXCP6fyHRGbXn4mlYDW83KGKZdfA7NEH+2QCdEqR12d/5DIFnT5XxKEdbpF8IlU0AYq8QIOGWg0qNJMBiVk9oIDCj0wj9wnl7ccgfWewkpWlyWgk+fEyW+RZaqM0=';
//            $iv='NSUL5PWCIznNYBLVGlx0Xg==';
//            $rawData='{"nickName":"lk","gender":1,"language":"zh_CN","city":"Guangzhou","province":"Guangdong","country":"China","avatarUrl":"https://wx.qlogo.cn/mmopen/vi_32/UPGlgUiaSPVG3PSicYIdcNHg62RECnz9mLLrLepfVhsBFfMYQD4dD4ZEDgWib7Eib2CT0icfkbsPN802vgrQpNic5NPQ/132"}';
//            $signature='50365d9da480424358edd2de04da59380c766def';
//            $code='033fLrUZ0M2J6X1nUpVZ0AstUZ0fLrUV';

            $mpid=input("mpid", '', 'htmlspecialchars_decode');
            $dashboarid =input("dashboardid", '', 'htmlspecialchars_decode');

            if(empty($mpid)){
                $mpid=1;//取得默认公众号ID
            }
            $result = Db::name('WechatConfig')->where(['name' => 'wxmin', 'mpid' =>$mpid, 'dashboard_id' =>$dashboarid])->find();
            if(empty($result)){
                $result = ['code'=>-1,'message'=>'未配置小程序密钥，请前往后台设置'];
                return json_encode($result);
            }else{
                $array = json_decode($result['value'], true);
                if(empty($array['appid'])||empty($array['appsecret'])){
                    $result = ['code'=>-1,'message'=>'未配置小程序密钥，请前往后台设置'];
                    return json_encode($result);
                }
                // 小程序配置
                $config =$array;
                $mini = new Crypt($config);
                $userInfo = $mini->userInfo($code,$iv, $encryptedData,$rawData,$signature);
                if($userInfo['code']==-1){
                    return json_encode($userInfo);
                }else{
                    $auth = Db::name('MemberAuth')->where(['openid' =>$userInfo['openId'], 'mpid' =>1])->find();
                    if(empty($auth)){
                        $userInfo['openid'] = $userInfo['openId'];
                        $rand = getRandChar(4);
                        $password = md5($userInfo['openId'] . $rand);
                        $data['password']=$password;
                        $data['username']=$userInfo['nickName'];
                        $data['nickname']=$userInfo['nickName'];
                        $data['headimgurl']=$userInfo['avatarUrl'];
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
                        $authdata['openid']=$userInfo['openId'];
                        $authdata['mpid']=$mpid;
                        $authdata['type']="2";//微信小程序
                        Db::name('MemberAuth')->insertGetId($authdata);
                        $token = create_guid();

                        $result = ['token'=>$token,'code'=>0,'username'=>$userInfo['username'],'nickName'=>$userInfo['nickname'],'avatarUrl'=>$userInfo['avatarUrl'],'sessionkey'=>$userInfo['session_key'],'openid'=>$userInfo['openid'],'uid'=>$id,'message'=>'登录成功 '];
                        cache($token,$result);
                        return json_encode($result);
                    }else{
                        $member= Db::name('Member')->where('uid',$auth['uid'])->field('username,nickname,headimgurl')->find();

                        $token = create_guid();
                        $result = ['token'=>$token,'code'=>0,'username'=>$member['username'],'nickName'=>$member['nickname'],'headimgurl'=>$member['headimgurl'],'sessionkey'=>$userInfo['session_key'],'uid'=>$auth['uid'],'openid'=>$auth['openid'],'message'=>'登录成功 '];
                        cache($token,$result);
                        return json_encode($result);
                    }

                }
            }

    }


    public function index()
    {
        if($this->request->isAjax()){
            $data= $this->request->request();
            $member = Db::name('Member')->where('username',$data['username'])->find();
            if(empty($member)){
                echo json_encode(['status'=>'error','message'=>'账号不存在!']);
            }
            $password = md5($data['password'] . $member['rand']);
            if($member['password']!=$password){
                echo json_encode(['status'=>'success','message'=>'用户名或密码错误，请重新登录!']);
            }
            session('uid'.session('mpid'),$member['uid']);
            cookie('uid'.session('mpid'),$member['uid']);
            unset($member['password']);
            cookie("user".session('mpid'),json_encode($member));
            if(session('login_pre_url')){
                echo json_encode(['status'=>'success','message'=>'登录成功!','redirecturl'=>$_SESSION['login_pre_url']]);
            }else{
                echo json_encode(['status'=>'success','message'=>'登录成功!']);
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
        unset($member['password']);
        cookie("user".session('mpid'),json_encode($member));
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
            cookie('uid'.$data('mpid'),$member['uid']);
            unset($member['password']);
            cookie("user".$data('mpid'),json_encode($member));
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