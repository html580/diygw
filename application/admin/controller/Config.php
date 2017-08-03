<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +---------------------------------------------------------------------- 

namespace app\admin\controller;

/**
 * 后台配置控制器
 * @author 艺品网络  <twothink.cn>
 */
class Config extends Admin {

    /**
     * 配置管理
     * @author 艺品网络  <twothink.cn>
     */
    public function index(){
        /* 查询条件初始化 */
        $map = array();
        $map  = array('status' => 1);
        $group=input('group');
        $name=input('name');
        if(isset($group)){
            $map['group']   =   input('group',0);
        }
        if(isset($name)){
            $map['name']    =   array('like', '%'.(string)input('name').'%');
        } 
        $list = $this->lists('Config', $map,'sort,id');  
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('group',config('config_group_list'));
        $this->assign('group_id',input('get.group',0));
        $this->assign('list', $list);
        $this->assign('meta_title' , '配置管理');
        return $this->fetch();
    }

    /**
     * 新增配置
     * @author 艺品网络  <twothink.cn>
     */
    public function add(){
        if($this->request->isPost()){
            $Config = \think\Loader::model('Config');
            $data  = $this->request->Post();
            $validate = \think\Loader::validate('config');
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            $data = $Config->create($data);
            if($data){ 
                cache('db_config_data',null);
                $this->success('新增成功', url('index')); 
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->assign('meta_title','新增配置');
            $this->assign('info',null);
            return $this->fetch('edit');
        }
    }

    /**
     * 编辑配置
     * @author 艺品网络  <twothink.cn>
     */
    public function edit($id = 0){
        if(request()->isPost()){
            $Config = \think\Loader::model('Config');
            $data  = $this->request->Post();
            $validate = \think\Loader::validate('config');
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            $update = $Config->allowField(true)->update($data); 
            if($update){ 
                cache('db_config_data',null);
                    //记录行为
                action_log('update_config','config',$data['id'],UID);
                $this->success('更新成功', Cookie('__forward__')); 
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = \think\Db::name('Config')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->assign('meta_title', '编辑配置');
            return $this->fetch();
        }
    }

    /**
     * 批量保存配置
     * @author 艺品网络  <twothink.cn>
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = \think\Db::name('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        cache('db_config_data',null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author 艺品网络  <twothink.cn>
     */
    public function del(){
        $id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Config')->where($map)->delete()){
            cache('db_config_data',null);
            //记录行为
            action_log('update_config','config',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
        $id     =   input('id',1);
        $type   =   config('config_group_list'); 
        $list   =   \think\Db::name("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->assign("meta_title", $type[$id].'设置');
        return $this->fetch();
    }

    /**
     * 配置排序
     * @author 艺品网络  <twothink.cn>
     */
    public function sort(){
    	if($this->request->isGet()){
            $ids = input('ids');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(input('group')){
                $map['group']	=	input('group');
            }
            $list = \think\Db::name('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->assign('meta_title', '配置排序');
            return $this->fetch();
        }elseif (request()->isPost()){
            $ids = input('ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = \think\Db::name('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}