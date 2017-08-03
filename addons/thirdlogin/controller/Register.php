<?php
/**
 *@author: izhang
 *@date:   2014-1-15 上午02:57:47 
 *@第三方帐号集成  注册绑定 模块
 * */
namespace addons\thirdlogin\controller;
use home\controller\Addons; 
use user\api\UserApi;

class Register extends Addons{
	
	/**
	 * 第三方帐号集成 - 绑定本地帐号
	 * @return void
	 */
	public function dobind(){
		$email = $this->t($_POST['email']);
		$password = trim($_POST['password']);
		//根据邮箱地址和密码判断是否存在该用户
		$user = new UserApi;
	    if(preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$email )) {
           	$uid = $user->login($email, $password,2);
        }	
        
        if($uid > 0 ) {
        	//注册来源-第三方帐号绑定
			if(isset($_POST['other_type'])){
				$other['type'] = $this->t($_POST['other_type']);
				$other['type_uid'] = $this->t($_POST['other_uid']);	
				$other['oauth_token'] = $this->t($_POST['oauth_token']);
				$other['oauth_token_secret'] = $this->t($_POST['oauth_token_secret']);
				$other['uid'] = $uid;
				
				\think\Db::name('Login')->add($other);
			}else{
				$this->error('绑定失败，第三方信息不正确');	
			}      
			/* 登录用户 */  
			$Member = model('Member');
			if($Member->login($uid)){			
				$this->assign('jumpUrl', url('member/clientarea/index'));
				$this->success('恭喜您，绑定成功');
			}else{
				$this->error($Member->getError());
			}
			return ;	
        }else{
        	$this->error('绑定失败，请确认帐号密码正确');			// 注册失败
        } 
        
	}	
	
	/**
	 * 第三方帐号集成 - 注册新账号
	 * @return void
	 */
	public function doregister(){		
		$email = $this->t($_POST['email']);
		$username = $this->t($_POST['uname']);
		$password = $this->t($_POST['password']);
		$repassword = $this->t($_POST['repassword']);

		//验证昵称
		if(empty($username)){
			$this->error('请输入昵称！');
		}
		if(strlen($username) < 3){
			$this->error('昵称长度不能小于3个字符');
		}
		
		$User = new UserApi;
		
		//验证邮箱格式
		 if(preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$email )) {
		 	//判断数据库是否已存在该邮箱地址
		 	//if($User->checkEmail($email)){
		 	//	$this->error('您输入的邮箱地址已存在');
		 	//}
		 }else{
		 	$this->error('您输入的邮箱地址格式不对！');
		 }

		/* 检测密码 */
		if($password != $repassword){
			$this->error('密码和重复密码不一致！');
		}
		
		$uid    =   $User->register($username, $password, $email);
		if(0 < $uid){ 
		    $user = array('uid' => $uid, 'nickname' => $username, 'status' => 1);
            if(!model('Member')->add($user)){
                $this->error('用户添加失败！');
            } else {
                //添加到用户组
                \think\Db::name('auth_group_access')->add(array('uid'=>$uid,'group_id'=>'1')) ;
                // 注册来源-第三方帐号绑定
            	if(isset($_POST['other_type'])){
					$other['type'] = $this->t($_POST['other_type']);
					$other['type_uid'] = $this->t($_POST['other_uid']);	
					$other['oauth_token'] = $this->t($_POST['oauth_token']);
					$other['oauth_token_secret'] = $this->t($_POST['oauth_token_secret']);
					$other['uid'] = $uid;
					\think\Db::name('login')->add($other);
				}    
            	/* 登录用户 */  
				$Member = model('Member');
				if($Member->login($uid)){			
					$this->assign('jumpUrl', url('member/Clientarea/index'));
					$this->success('恭喜您，注册成功');	
				}else{
					$this->error($Member->getError());
				}				            
            }			
		}else{
			$this->error($this->showRegError($uid)); //注册失败
		}
	}

	
    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }	
	
	private function t($text){
	    $text = nl2br($text);
	    $text = real_strip_tags($text);
	    $text = addslashes($text);
	    $text = trim($text);
	    return $text;
	}	
} 

