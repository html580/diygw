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
 * 属性控制器
 * @author 艺品网络  <twothink.cn>
 */
class Attribute extends Admin {

    /**
     * 属性列表
     * @author 艺品网络  <twothink.cn>
     */
    public function index(){
        $model_id = input('model_id');
        /* 查询条件初始化 */
        $map['model_id']    =   $model_id;

        $list = $this->lists('Attribute', $map); 
        int_to_string($list); 
        // 记录当前列表页的cookie
        Cookie('__forward__',       $_SERVER['REQUEST_URI']);
        $this->assign('_list',      $list);
        $this->assign('model_id',   $model_id);
        $this->assign('meta_title', '属性列表');
        return $this->fetch();
    }

    /**
     * 新增页面初始化
     * @author 艺品网络  <twothink.cn>
     */
    public function add(){ 
        $model_id   =   input('model_id');
        $model      =   \think\Db::name('Model')->field('title,name,field_group')->find($model_id);
        $this->assign('model',$model);
        $this->assign('info', array('model_id'=>$model_id));
        $this->assign('meta_title', '新增属性');
        return $this->fetch('edit');
    }

    /**
     * 编辑页面初始化
     * @author 艺品网络  <twothink.cn>
     */
    public function edit(){
        $id = input('id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }

        /*获取一条记录的详细数据*/
        $Model = \think\Db::name('Attribute');
        $data = $Model->field(true)->find($id);
        if(!$data){
            $this->error($Model->getError());
        }
        $model  =   \think\Db::name('Model')->field('title,name,field_group')->find($data['model_id']);
        $this->assign('model',$model);
        $this->assign('info', $data);
        $this->assign('meta_title' , '编辑属性');
        return $this->fetch();
    }

    /**
     * 更新一条数据
     * @author 艺品网络  <twothink.cn>
     */
    public function update(){ 
        $res = model('Attribute')->updates();
        if(!$res){
            $this->error(model('Attribute')->getError());
        }else{
            $this->success(isset($res['id'])?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }

    /**
     * 删除一条数据
     * @author 艺品网络  <twothink.cn>
     */
    public function remove(){
        $id = input('id');
        empty($id) && $this->error('参数错误！');

        $Model = model('Attribute');

        $info = $Model->getById($id);
        empty($info) && $this->error('该字段不存在！');  
        //删除属性数据
        $res = $Model->where(array('id'=>$id))->delete(); 
        //删除表字段
        $Model->deleteField($info);
        if(!$res){
            $this->error(model('Attribute')->getError());
        }else{
            //记录行为
            action_log('update_attribute', 'attribute', $id, UID);
            $this->success('删除成功', url('index','model_id='.$info['model_id']));
        }
    }
}
