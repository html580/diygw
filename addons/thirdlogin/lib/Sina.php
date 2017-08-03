<?php

namespace addons\thirdlogin\lib;
use addons\thirdlogin\lib\sina\SaeTOAuthV2;

class Sina extends ThinkOauth{
	/**
	 * 获取requestCode的api接口
	 * @var string
	 */
	protected $GetRequestCodeURL = 'https://api.weibo.com/oauth2/authorize';

	/**
	 * 获取access_token的api接口
	 * @var string
	 */
	protected $GetAccessTokenURL = 'https://api.weibo.com/oauth2/access_token';

	/**
	 * API根路径
	 * @var string
	 */
	protected $ApiBase = 'https://api.weibo.com/2/';
	

	/**
	 * 组装接口调用参数 并调用接口
	 * @param  string $api    微博API
	 * @param  string $param  调用API的额外参数
	 * @param  string $method HTTP请求方法 默认为GET
	 * @return json
	 */
	public function call($api, $param = '', $method = 'GET', $multi = false){		
		/* 新浪微博调用公共参数 */
		$params = array(
			'access_token' => $this->Token['access_token'],
		);
		
		$vars = $this->param($params, $param);
		$data = $this->http($this->url($api, '.json'), $vars, $method, array(), $multi);
		return json_decode($data, true);
	}
	
	/**
	 * 解析access_token方法请求后的返回值
	 * @param string $result 获取access_token的方法的返回值
	 */
	protected function parseToken($result, $extend){
		$data = json_decode($result, true);
		if($data['access_token'] && $data['expires_in'] && $data['remind_in'] && $data['uid']){
			$data['openid'] = $data['uid'];
			unset($data['uid']);
			return $data;
		} else
			throw_exception("获取新浪微博ACCESS_TOKEN出错：{$data['error']}");
	}
	
	/**
	 * 获取当前授权应用的openid
	 * @return string
	 */
	public function openid(){
		$data = $this->Token;
		if(isset($data['openid']))
			return $data['openid'];
		else
			throw_exception('没有获取到新浪微博用户ID！');
	}
	
	//用户资料
	public function userInfo(){
		
		$sinauid = $this->doClient();//->get_uid()
		
		return ;
		$me = $this->doClient($opt)->show_user_by_id($sinauid['uid']);
		$user['id']          = $me['id'];
		$user['uname']       = $me['name'];
		$user['province']    = $me['province'];
		$user['city']        = $me['city'];
		$user['location']    = $me['location'];
		$user['userface']    = str_replace(  $user['id'].'/50/' , $user['id'].'/180/' ,$me['profile_image_url'] );
		$user['sex']         = ($me['gender']=='m')?1:0;
		print_r($user);return ;
		return $user;
	}
    private function doClient(){
    	if(isset($_SESSION['sina']['access_token'])){
	    	$access_token = $_SESSION['sina']['access_token']['oauth_token'];
	    	$refresh_token = $_SESSION['sina']['access_token']['oauth_token_secret'];
    	}else{
	    	$access_token = $opt["oauth_token"];
	    	$refresh_token = $opt["oauth_token_secret"];
    	}
    	
		return new SaeTClientV2( $this->AppKey , $this->AppSecret , $access_token , $refresh_token);
	}
	//验证用户
    public function checkUser(){
		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $this->Callback;
//			print_r($keys);return ;
			try {
				$sina = new SaeTOAuthV2( $this->AppKey , $this->AppSecret );
				$token = $sina ->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {
				$token = null;
			}
		}else{
			$keys = array();
			$keys['refresh_token'] = $_REQUEST['code'];
			try {
				$token = $this->_oauth->getAccessToken( 'token', $keys ) ;
			} catch (OAuthException $e) {
				$token = null;
			}
		}
		//print_r($token);return ;
		if ($token) {
			$sina = new SaeTOAuthV2( $this->AppKey , $this->AppSecret );
			setcookie( 'weibojs_'.$this->_oauth->client_id, http_build_query($token) );
			$_SESSION['sina']['access_token']['oauth_token'] = $token['access_token'];
			$_SESSION['sina']['access_token']['oauth_token_secret'] = $token['refresh_token'];
			$_SESSION['sina']['expire'] = time()+$token['expires_in'];
			$_SESSION['sina']['uid'] = $token['uid'];
			$_SESSION['open_platform_type'] = 'sina';
			return $token;
		}else{
			return false;
		}
	}	
}