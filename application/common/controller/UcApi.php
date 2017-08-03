<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
/*
 * UC 客户端 用户登入
 */
namespace app\common\controller;
use app\common\model\UcenterMember;
use think\Exception;

define('UC_CLIENT_PATH', dirname(dirname(__FILE__)));
define('UC_APP_ID',\think\Config::get('uc_app_id'));
define('UC_API_TYPE',\think\Config::get('uc_api_type'));
define('UC_AUTH_KEY',\think\Config::get('uc_auth_key'));
//载入函数库文件
require(UC_CLIENT_PATH . '/common/uc_function.php');

class UcApi{
    protected $model;
    /**
     * 构造方法，检测相关配置
     */
    public function __construct(){
        //相关配置检测
        defined('UC_APP_ID') || throw_exception('UC配置错误：缺少UC_APP_ID');
        defined('UC_API_TYPE') || throw_exception('UC配置错误：缺少UC_APP_API_TYPE');
        defined('UC_AUTH_KEY') || throw_exception('UC配置错误：缺少UC_APP_AUTH_KEY');

        if(UC_API_TYPE != 'Model' && UC_API_TYPE != 'Service'){
            throw exception('UC配置错误：UC_API_TYPE只能为 Model 或 Service');
        }
        if(UC_API_TYPE == 'Service' && UC_AUTH_KEY == ''){
            throw exception('UC配置错误：Service方式调用Api时UC_AUTH_KEY不能为空');
        }
        $this->_init();
    }
    /**
     * 构造方法，实例化操作模型
     */
    protected function _init(){
        $this->model = new UcenterMember();
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @param  stting $scene  验证场景  admin 后台
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username, $password, $email, $mobile = '',$scene=''){
        return $this->model->register($username, $password, $email, $mobile,$scene);
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1){
        return $this->model->login($username, $password, $type);
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false){
        return $this->model->info($uid, $is_username);
    }

    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){
        return $this->model->checkField($username, 1);
    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){
        return $this->model->checkField($email, 2);
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){
        return $this->model->checkField($mobile, 3);
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author 艺品网络  <twothink.cn>
     */
    public function updateInfo($uid, $password, $data){
        if($this->model->updateUserFields($uid, $password, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }

}
