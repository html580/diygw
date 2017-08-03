<?php

namespace addons\thirdlogin\lib;

class Qzone extends ThinkOauth{
	/**
	 * 获取requestCode的api接口
	 * @var string
	 */
	protected $GetRequestCodeURL = 'https://graph.qq.com/oauth2.0/authorize';
	
	/**
	 * 获取access_token的api接口
	 * @var string
	 */
	protected $GetAccessTokenURL = 'https://graph.qq.com/oauth2.0/token';
	
	/**
	 * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
	 * @var srting
	 */
	protected $Authorize = 'scope=get_user_info,add_share';

	/**
	 * API根路径
	 * @var string
	 */
	protected $ApiBase = 'https://graph.qq.com/';

	/**
	 * 组装接口调用参数 并调用接口
	 * @param  string $api    微博API
	 * @param  string $param  调用API的额外参数
	 * @param  string $method HTTP请求方法 默认为GET
	 * @return json
	 */
	public function call($api, $param = '', $method = 'GET', $multi = false){
		/* 腾讯QQ调用公共参数 */
		$params = array(
			'oauth_consumer_key' => $this->AppKey,
			'access_token'       => $this->Token['access_token'],
			'openid'             => $this->openid(),
			'format'             => 'json'
		);
		
		$data = $this->http($this->url($api), $this->param($params, $param), $method);
		return json_decode($data, true);
	}
	
	/**
	 * 解析access_token方法请求后的返回值 
	 * @param string $result 获取access_token的方法的返回值
	 */
	protected function parseToken($result, $extend){
		parse_str($result, $data);
		if($data['access_token'] && $data['expires_in']){
			$this->Token    = $data;
			$data['openid'] = $this->openid();
			return $data;
		} else
			throw_exception("获取腾讯QQ ACCESS_TOKEN 出错：{$result}");
	}
	
	/**
	 * 获取当前授权应用的openid
	 * @return string
	 */
	public function openid(){
		$data = $this->Token;
		if(isset($data['openid']))
			return $data['openid'];
		elseif($data['access_token']){
			$data = $this->http($this->url('oauth2.0/me'), array('access_token' => $data['access_token']));
			$data = json_decode(trim(substr($data, 9), " );\n"), true);
			if(isset($data['openid']))
				return $data['openid'];
			else
				throw_exception("获取用户openid出错：{$data['error_description']}");
		} else {
			throw_exception('没有获取到openid！');
		}
	}
	
	//用户资料
	public function userInfo(){
		$get_user_info = "https://graph.qq.com/user/get_user_info?"
		        . "access_token=" . $_SESSION['qzone']['access_token']['oauth_token']
		        . "&oauth_consumer_key=" . $this->AppKey
		        . "&openid=" . $_SESSION['qzone']["openid"]
		        . "&format=json";	
		      
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$get_user_info);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$info = curl_exec($ch);
	    $me = json_decode($info);
		$user['id']         =  $_SESSION['qzone']["openid"];
		$user['uname']       = $me->nickname;
		$user['province']    = 0;
		$user['city']        = 0;
		$user['location']    = '';
		$user['userface']    = $me->figureurl_2;
		$user['sex']         = 0;
		//print_r($user);
		return $user;
	}
	//验证用户
	public function checkUser(){
		if($_REQUEST['code'] && $_REQUEST['state'] == $_SESSION['state']){
			$token_url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id='.$this->AppKey.'&code='.$_REQUEST['code'].'&client_secret='.$this->AppSecret.'&redirect_uri='.$this->Callback;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$token_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
	        if (strpos($response, "callback") !== false)
	        {
	            $lpos = strpos($response, "(");
	            $rpos = strrpos($response, ")");
	            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
	            $msg = json_decode($response);
	            if (isset($msg->error))
	            {
	                return false;
	                //echo "<h3>error:</h3>" . $msg->error;
	                //echo "<h3>msg  :</h3>" . $msg->error_description;
	                //exit;
	            }
	        }
	       //
	        $params = array();
	        parse_str($response, $params);
	        	    
	        $access_token = $params['access_token'];
			$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
			curl_setopt($ch, CURLOPT_URL,$graph_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch); 
		    if (strpos($response, "callback") !== false)
		    {
		        $lpos = strpos($response, "(");
		        $rpos = strrpos($response, ")");
		        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
		    }
		    $user = json_decode($response);
		    if (isset($user->error))
		    {
		        echo "<h3>error:</h3>" . $user->error;
		        echo "<h3>msg  :</h3>" . $user->error_description;
		        exit;
		    }else{
		    	$_SESSION['qzone']['access_token']['oauth_token']  = $access_token;
				$_SESSION['qzone']['access_token']['oauth_token_secret'] = $user->openid;
				$_SESSION['qzone']['isSync'] = 1;
				$_SESSION['qzone']["openid"] = $user->openid;
				//$_SESSION['qzone']['uid'] = $user->openid;
				//$_SESSION['qzone']['uname'] = $res['user']['name'];
				$_SESSION['open_platform_type'] = 'qzone';
		    }
		}else{
			return false;
		}
		return true;
	}	
}