<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;

/**
 * 权限管理控制器
 * Class AuthManagerController
 * @author 艺品网络 <twothink.cn>
 */
class AuthManager extends Admin{

    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务 * @author 艺品网络 <twothink.cn>
     */
    public function updateRules(){
        //需要新增的节点必然位于$nodes
        $nodes    = $this->returnNodes(false);

        $AuthRule = model('AuthRule');
        $map      = array('module'=>'admin','type'=>array('in','1,2'));//status全部取出,以进行更新
        //需要更新和删除的节点必然位于$rules
        $rules    = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data     = array();//保存需要插入和更新的新节点
        foreach ($nodes as $value){
            $temp['name']   = $value['url'];
            $temp['title']  = $value['title'];
            $temp['module'] = 'admin';
            if($value['pid'] >0){
                $temp['type'] = AuthRule::rule_url;
            }else{
                $temp['type'] = AuthRule::rule_main;
            }
            $temp['status']   = 1;
            $data[strtolower($temp['name'].$temp['module'].$temp['type'])] = $temp;//去除重复项
        }

        $update = array();//保存需要更新的节点
        $ids    = array();//保存需要删除的节点的id
        foreach ($rules as $index=>$rule){
            $key = strtolower($rule['name'].$rule['module'].$rule['type']);
            if ( isset($data[$key]) ) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']]=$rule;
            }elseif($rule['status']==1){
                $ids[] = $rule['id'];
            }
        }
        if ( count($update) ) {
            foreach ($update as $k=>$row){
                if ( $row!=$diff[$row['id']] ) {
                    $AuthRule->where(array('id'=>$row['id']))->update($row);
                }
            }
        }
        if ( count($ids) ) {
            $AuthRule->where( array( 'id'=>array('IN',implode(',',$ids)) ) )->update(array('status'=>-1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if( count($data) ){
            $AuthRule->insertAll(array_values($data));
        }
        if ( $AuthRule->getError() ) {
            trace('['.__METHOD__.']:'.$AuthRule->getError());
            return false;
        }else{
            return true;
        }
    }


    /**
     * 权限管理首页
	 * @author 艺品网络 <twothink.cn>
     */
    public function index(){
        $list = $this->lists('AuthGroup',array('module'=>'admin'),'id asc');
        $list = int_to_string($list);
        $this->assign( '_list', $list );
        $this->assign( '_use_tip', true );
        $this->assign('meta_title','权限管理');
        return $this->fetch();
    }

    /**
     * 创建管理员用户组
     */
    public function createGroup(){
        if ( empty($this->auth_group) ) {
            $this->assign('auth_group',array('title'=>null,'id'=>null,'description'=>null,'rules'=>null,));//排除notice信息
        }
        $this->assign('meta_title','新增用户组');
        return $this->fetch('editgroup');
    }

    /**
     * 编辑管理员用户组
	 * @author 艺品网络 <twothink.cn>
     */
    public function editGroup(){
        $auth_group = \think\Db::name('AuthGroup')->where( array('module'=>'admin','type'=>AuthGroup::type_admin) )
                                    ->find( (int)input('id') );
        $this->assign('auth_group',$auth_group);
        $this->assign('meta_title','编辑用户组');
        return $this->fetch();
    }


    /**
     * 访问授权页面
	 * @author 艺品网络 <twothink.cn>
     */
    public function access(){
        $this->updateRules();
        $auth_group = \think\Db::name('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>AuthGroup::type_admin) )
                                    ->column('id,id,title,rules');
        $node_list   = $this->returnNodes();
        $map         = array('module'=>'admin','type'=>AuthRule::rule_main,'status'=>1);
        $main_rules  = \think\Db::name('AuthRule')->where($map)->column('name,id');
        $map         = array('module'=>'admin','type'=>AuthRule::rule_url,'status'=>1);
        $child_rules = \think\Db::name('AuthRule')->where($map)->column('name,id');

        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list',  $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)input('group_id')]);
        $this->assign('meta_title','访问授权');
        return $this->fetch('managergroup');
    }

    /**
     * 管理员用户组数据写入/更新
	 * @author 艺品网络 <twothink.cn>
     */
    public function writeGroup(){
    	$data=input();
        if(isset($data['rules'])){
            sort($data['rules']);
            $data['rules']  = implode( ',' , array_unique($data['rules']));
        }
        $data['module'] =  'admin';
        $data['type']   =  AuthGroup::type_admin;
        $AuthGroup       =  model('auth_group');


        if(isset($data['id'])){
        	$r = $AuthGroup->update($data);
        }else{
        	$validate = validate('auth_group');
        	if(!$validate->check($data)){
        		return $this->error($validate->getError());
        	}
        	$r = $AuthGroup->save($data);
        }

        if($r===false){
            $this->error('操作失败'.$AuthGroup->getError());
        } else{
            $this->success('操作成功!',url('index'));
        }

    }

    /**
     * 状态修改
	 * @author 艺品网络 <twothink.cn>
     */
    public function changeStatus($method=null){
    	$id=input('id/a');
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        switch ( strtolower($method) ){
            case 'forbidgroup':
                $this->forbid('auth_group');
                break;
            case 'resumegroup':
                $this->resume('auth_group');
                break;
            case 'deletegroup':
                $this->delete('auth_group');
                break;
            default:
                $this->error($method.'参数非法');
        }
    }

    /**
     * 用户组授权用户列表
	 * @author 艺品网络 <twothink.cn>
     */
    public function user($group_id){
        if(empty($group_id)){
            $this->error('参数错误');
        }

        $auth_group = \think\Db::name('auth_group')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>AuthGroup::type_admin) )
            ->column('id,id,title,rules');
        $prefix   = config('database.prefix');
        $l_table  = AuthGroup::member;//$prefix.(AuthGroup::MEMBER);
        $r_table  = AuthGroup::auth_group_access;//$prefix.(AuthGroup::AUTH_GROUP_ACCESS);

        $model    = db($l_table)->alias('m')->join ( $prefix.$r_table.' a','m.uid=a.uid' );
		$where=array('a.group_id'=>$group_id,'m.status'=>array('egt',0));
        $field='m.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status';

        $listRows = config('list_rows') > 0 ? config('list_rows') : 10;

        // 分页查询
        $list = $model->where($where)->order('m.uid asc')->field($field)->paginate($listRows);
        // 获取分页显示
        $page = $list->render();
        $list = $list->toArray();
        $list = $list['data'];
        int_to_string($list);
        $this->assign('_page', $page);
        $this->assign( '_list',     $list );
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)input('group_id')]);
        $this->assign('meta_title' , '成员授权');
        return $this->fetch();
    }

    /**
     * 将分类添加到用户组的编辑页面
	 * @author 艺品网络 <twothink.cn>
     */
    public function category(){
        $auth_group     =   \think\Db::name('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>AuthGroup::type_admin) )
            ->column('id,id,title,rules');
        $group_list     =   model('Category')->getTree();
        $authed_group   =   AuthGroup::getCategoryOfGroup(input('group_id'));
        $this->assign('authed_group',   implode(',',(array)$authed_group));
        $this->assign('group_list',     $group_list);
        $this->assign('auth_group',     $auth_group);
        $this->assign('this_group',     $auth_group[(int)input('group_id')]);
        $this->assign('meta_title' , '分类授权');
        return $this->fetch();
    }

    public function tree($tree = null){
        $this->assign('tree', $tree);
       return $this->fetch('tree');
    }

    /**
     * 将用户添加到用户组的编辑页面
     * @author 艺品网络 <twothink.cn>
     */
    public function group(){
        $uid            =   input('uid');
        $auth_groups    =   model('AuthGroup')->getGroups();

        $user_groups    =   AuthGroup::getUserGroup($uid);
        $ids = array();
        foreach ($user_groups as $value){
            $ids[]      =   $value['group_id'];
        }
        $nickname       =   model('Member')->getNickName($uid);
        $this->assign('nickname',   $nickname);
        $this->assign('auth_groups',$auth_groups);
        $this->assign('user_groups',implode(',',$ids));
        $this->assign('meta_title','用户组授权');
        return $this->fetch();

    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     * @author 艺品网络 <twothink.cn>
     */
    public function addToGroup(){
        $uid = input('uid');
        $gid = input('group_id/a');
        if( empty($uid) ){
            $this->error('参数有误');
        }
        $AuthGroup = model('AuthGroup');
        if(is_numeric($uid)){
            if ( is_administrator($uid) ) {
                $this->error('该用户为超级管理员');
            }
            if( !\think\Db::name('Member',[],false)->where(array('uid'=>$uid))->find() ){
                $this->error('用户不存在');
            }
        }
        if( $gid && !$AuthGroup->checkGroupId($gid)){
            $this->error($AuthGroup->getError());
        }
        if ( $AuthGroup->addToGroup($uid,$gid) ){
            $this->success('操作成功');
        }else{
            $this->error($AuthGroup->getError());
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     * @author 艺品网络 <twothink.cn>
     */
    public function removeFromGroup(){
        $uid = input('uid');
        $gid = input('group_id');
        if( $uid==UID ){
            $this->error('不允许解除自身授权');
        }
        if( empty($uid) || empty($gid) ){
            $this->error('参数有误');
        }
        $AuthGroup = model('AuthGroup');
        if( !$AuthGroup->find($gid)){
            $this->error('用户组不存在');
        }
        if ( $AuthGroup->removeFromGroup($uid,$gid) ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 将分类添加到用户组  入参:cid,group_id
     * @author 艺品网络 <twothink.cn>
     */
    public function addToCategory(){
        $cid = input('cid/a');
        $gid = input('group_id');
        if( empty($gid) ){
            $this->error('参数有误');
        }
        $AuthGroup = model('auth_group');
        if( !$AuthGroup->find($gid)){
            $this->error('用户组不存在');
        }

        if( $cid && !$AuthGroup->checkCategoryId($cid)){
             $this->error($AuthGroup->getError());
        }
        if ( $data=$AuthGroup->addToCategory($gid,$cid) ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 将模型添加到用户组  入参:mid,group_id
     * @author 艺品网络  <twothink.cn>
     */
    public function addToModel(){
        $mid = input('id');
        $gid = input('get.group_id');
        if( empty($gid) ){
            $this->error('参数有误');
        }
        $AuthGroup = model('AuthGroup');
        if( !$AuthGroup->find($gid)){
            $this->error('用户组不存在');
        }
        if( $mid && !$AuthGroup->checkModelId($mid)){
            $this->error($AuthGroup->error);
        }
        if ( $AuthGroup->addToModel($gid,$mid) ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

}
