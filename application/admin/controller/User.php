<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\UcApi;
use app\admin\model\Member;

/**
 * 后台用户控制器
 * @author 艺品网络  <twothink.cn>
 */
class User extends Admin{

    /**
     * 用户管理首页
     */
    public function index(){
        $nickname       =   input('nickname');
        $map['status']  =   array('egt',0);
        if(is_numeric($nickname)){
            $map['uid|nickname']=   array('like','%'.$nickname.'%');
        }else{
            $map['nickname']    =   array('like', '%'.(string)$nickname.'%');
        }

        $list   = $this->lists('Member', $map);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('meta_title','用户信息');
        return $this->fetch();
    }

    /**
     * 修改昵称初始化
     */
    public function updateNickname(){
        $nickname = \think\Db::name('Member')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->assign('meta_title' , '修改昵称');
        return $this->fetch('updatenickname');
    }

    /**
     * 修改昵称提交
     */
    public function submitNickname(){
        //获取参数
        $nickname = input('post.nickname');
        $password = input('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        $User   =   new UcApi();
        $uid    =   $User->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');
        $Member =   model('Member');
        $res = $Member->save(['nickname'=>$nickname],['uid'=>$uid]);
        if($res){
            $user               =   session('user_auth');
            $user['username']   =   $nickname;
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        }else{
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author 艺品网络  <twothink.cn>
     */
    public function updatePassword(){
        $this->assign('meta_title','修改密码');
        return $this->fetch('updatepassword');
    }

    /**
     * 修改密码提交
     * @author 艺品网络  <twothink.cn>
     */
    public function submitPassword(){
        //获取参数
        $password   =   input('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = input('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = input('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($data['password'] !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api    =   new UcApi();
        $res    =   $Api->updateInfo(UID, $password, $data);
        if($res['status']){
            $this->success('修改密码成功！');
        }else{
            $this->error($res['info']);
        }
    }

    /**
     * 用户行为列表
     * @author 艺品网络  <twothink.cn>
     */
    public function action(){
        //获取列表数据
        $Action =   \think\Db::name('Action')->where(array('status'=>array('gt',-1)));
        $list   =   $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('_list', $list);
        $this->assign('meta_title','用户行为');
        return $this->fetch();
    }

    /**
     * 新增行为
     * @author 艺品网络  <twothink.cn>
     */
    public function addAction(){
        $this->assign('meta_title','新增行为');
        $this->assign('data',null);
        return $this->fetch('editaction');
    }

    /**
     * 编辑行为
     * @author 艺品网络  <twothink.cn>
     */
    public function editAction(){
        $id = input('id');
        empty($id) && $this->error('参数不能为空！');
        $data = \think\Db::name('Action')->field(true)->find($id);

        $this->assign('data',$data);
        $this->assign('meta_title', '编辑行为');
        return $this->fetch('editaction');
    }

    /**
     * 更新行为
     * @author 艺品网络  <twothink.cn>
     */
    public function saveAction(){
    	$validate = validate('action');
    	if(!$validate->check(input())){
    		return $this->error($validate->getError());
    	}
        $res = model('Action')->updates();
        if(!$res){
            $this->error(model('Action')->getError());
        }else{
            $this->success(isset($res['id'])?'更新成功！':'新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     */
    public function changeStatus($method=null){
        $data=input('id/a');
        $id = array_unique($data);
        if( in_array(config('user_administrator'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }

        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('member', $map );
                break;
            case 'resumeuser':
                $this->resume('member', $map );
                break;
            case 'deleteuser':
                $this->delete('member', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }

    public function add($username = '', $password = '', $repassword = '', $email = ''){
        if(request()->isPost()){
            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }

            /* 调用注册接口注册用户 */
            $User   =   new UcApi;
            $uid    =   $User->register($username, $password, $email,'','admin');
            if(0 < $uid){ //注册成功
                $user = array('uid' => $uid, 'nickname' => $username, 'status' => 1);
                if(!db('Member',[],false)->insert($user)){
                    $this->error('用户添加失败！');
                } else {
                    $this->success('用户添加成功！',url('index'));
                }
            } else { //注册失败，显示错误信息
                $this->error($uid);
            }
        } else {
            $this->assign('meta_title','新增用户') ;
            return $this->fetch();
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

}
