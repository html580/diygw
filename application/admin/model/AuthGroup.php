<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;
/**
 * 用户组模型类
 * Class AuthGroupModel
 */
class AuthGroup extends Model {
    const type_admin                = 1;                   // 管理员用户组类型标识
    const member                    = 'member';
    const ucenter_member            = 'ucenter_member';
    const auth_group_access         = 'auth_group_access'; // 关系表表名
    const auth_extend               = 'auth_extend';       // 动态权限扩展信息表
    const auth_group                = 'auth_group';        // 用户组表名
    const auth_extend_category_type = 1;              // 分类权限标识
    const auth_extend_model_type    = 2; //分类权限标识

    protected $autoWriteTimestamp = false;


    /**
     * 返回用户组列表
     * 默认返回正常状态的管理员用户组列表
     * @param array $where   查询条件,供where()方法使用
     */
    public function getGroups($where=array()){
        $map = array('status'=>1,'type'=>self::type_admin,'module'=>'admin');
        $map = array_merge($map,$where);
        return $this->where($map)->select();
    }

    /**
     * 把用户添加到用户组,支持批量添加用户到用户组
     * 示例: 把uid=1的用户添加到group_id为1,2的组 `AuthGroupModel->addToGroup(1,'1,2');`
     */
    public function addToGroup($uid,$gid){
        $uid = is_array($uid)?implode(',',$uid):trim($uid,',');
        $gid = is_array($gid)?$gid:explode( ',',trim($gid,',') );

        $Access = \think\Db::name(self::auth_group_access);

        $uid_arr = explode(',',$uid);
	    $uid_arr = array_diff($uid_arr,array(config('user_administrator')));

	    $add = array();
          foreach ($uid_arr as $u){
            	//先删除旧数据
            	$Access->where( array('uid'=>array('in',$u)) )->delete();
            	//判断用户id是否合法
            	if(\think\Db::name('Member')->getFieldByUid($u,'uid') == false){
            		$this->error = "编号为{$u}的用户不存在！";
            		return false;
            	}
                foreach ($gid as $g){
                    if( is_numeric($u) && is_numeric($g) ){
                        $add[] = array('group_id'=>$g,'uid'=>$u);
                    }
                }
           }
        if( \think\Db::name(self::auth_group_access)->insertAll($add)){

        	return true;
        }else{
        	$Access->error = '添加失败';
        	return false;
        }
    }

    /**
     * 返回用户所属用户组信息
     * @param  int    $uid 用户id
     * @return array  用户所属的用户组 array(
     *  array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     */
    static public function getUserGroup($uid){

        static $groups = array();
        if (isset($groups[$uid]))
            return $groups[$uid];
        $prefix = config('database.prefix');
        $user_groups = \think\Db::table($prefix.self::auth_group_access)
        ->alias('a')
        ->field('uid,group_id,title,description,rules')
        ->join ($prefix.self::auth_group." g"," a.group_id=g.id")
        ->where("a.uid='$uid' and g.status='1'")
        ->select();
        $groups[$uid]=$user_groups?$user_groups:array();
        return $groups[$uid];
    }

    /**
     * 返回用户拥有管理权限的扩展数据id列表
     *
     * @param int     $uid  用户id
     * @param int     $type 扩展数据标识
     * @param int     $session  结果缓存标识
     * @return array
     *  array(2,4,8,13)
     */
    static public function getAuthExtend($uid,$type,$session){
        if ( !$type ) {
            return false;
        }
        if ( $session ) {
            $result = session($session);
        }
        if ( $uid == UID && !empty($result) ) {
            return $result;
        }
        $prefix = config('database.prefix');

        $result = \think\Db::table($prefix.self::auth_group_access.' g')
            ->join($prefix.self::auth_extend.' c','g.group_id=c.group_id')
            ->where("g.uid='$uid' and c.type='$type' and !isnull(extend_id)")
            ->column('extend_id');
        if ( $uid == UID && $session ) {
            session($session,$result);
        }
        return $result;
    }

    /**
     * 返回用户拥有管理权限的分类id列表
     *
     * @param int     $uid  用户id
     * @return array
     *
     *  array(2,4,8,13)
     */
    static public function getAuthCategories($uid){
        return self::getAuthExtend($uid,self::auth_extend_category_type,'auth_category');
    }



    /**
     * 获取用户组授权的扩展信息数据
     *
     * @param int     $gid  用户组id
     * @return array
     *  array(2,4,8,13)
     */
    static public function getExtendOfGroup($gid,$type){
        if ( !is_numeric($type) ) {
            return false;
        }
        return \think\Db::name(self::auth_extend)->where( array('group_id'=>$gid,'type'=>$type) )->column('extend_id');
    }

    /**
     * 获取用户组授权的分类id列表
     *
     * @param int     $gid  用户组id
     * @return array
     *  array(2,4,8,13)
     */
    static public function getCategoryOfGroup($gid){
        return self::getExtendOfGroup($gid,self::auth_extend_category_type);
    }


    /**
     * 批量设置用户组可管理的扩展权限数据
     *
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     */
    static public function addToExtend($gid,$cid,$type){
        $gid = is_array($gid)?implode(',',$gid):trim($gid,',');
        $cid = is_array($cid)?$cid:explode( ',',trim($cid,',') );

        $Access = \think\Db::name(self::auth_extend);
        $del = $Access->where( array('group_id'=>array('in',$gid),'type'=>$type) )->delete();

        $gid = explode(',',$gid);
        $add = array();
        if( $del!==false ){
            foreach ($gid as $g){
                foreach ($cid as $c){
                    if( is_numeric($g) && is_numeric($c) ){
                        $add[] = array('group_id'=>$g,'extend_id'=>$c,'type'=>$type);
                    }
                }
            }
            if(empty($add)){
            	return true;
            }
            if(!$Access->insertAll($add)){
            	return false;
            }
        }
        return true;
    }

    /**
     * 批量设置用户组可管理的分类
     *
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     */
    static public function addToCategory($gid,$cid){
        return self::addToExtend($gid,$cid,self::auth_extend_category_type);
    }


    /**
     * 将用户从用户组中移除
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     */
    public function removeFromGroup($uid,$gid){
        return db(self::auth_group_access)->where( array( 'uid'=>$uid,'group_id'=>$gid) )->delete();
    }

    /**
     * 获取某个用户组的用户列表
     * @param int $group_id   用户组id
     */
    static public function memberInGroup($group_id){
        $prefix   = config('database.prefix');
        $l_table  = $prefix.self::member;
        $r_table  = $prefix.self::auth_group_access;
        $r_table2 = $prefix.self::ucenter_member;
        $list     = \think\Db::table($l_table.' m')
                       ->field('m.uid,u.username,m.last_login_time,m.last_login_ip,m.status')
                       ->join($r_table.' a ON m.uid=a.uid')
                       ->join($r_table2.' u ON m.uid=u.id')
                       ->where(array('a.group_id'=>$group_id))
                       ->select();
        return $list;
    }

    /**
     * 检查id是否全部存在
     * @param array|string $gid  用户组id列表
     */
    public function checkId($modelname,$mid,$msg = '以下id不存在:'){

        if(is_array($mid)){
            $count = count($mid);
            $ids   = implode(',',$mid);
        }else{
            $mid   = explode(',',$mid);
            $count = count($mid);
            $ids   = $mid;
        }

        $s = \think\Db::name($modelname)->where(array('id'=>array('IN',$ids)))->column('id');

        if(count($s)===$count){
            return true;
        }else{
            $diff = implode(',',array_diff($mid,$s));
            $this->error = $msg.$diff;
            return false;
        }
    }

    /**
     * 检查用户组是否全部存在
     * @param array|string $gid  用户组id列表
     */
    public function checkGroupId($gid){
        return $this->checkId('AuthGroup',$gid, '以下用户组id不存在:');
    }

    /**
     * 检查分类是否全部存在
     * @param array|string $cid  栏目分类id列表
     */
    public function checkCategoryId($cid){
        return $this->checkId('Category',$cid, '以下分类id不存在:');
    }


}
