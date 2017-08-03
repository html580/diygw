<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +---------------------------------------------------------------------- 

namespace app\admin\controller;
use app\admin\model\AuthGroup;

/**
 * 模型管理控制器
 * @author 艺品网络  <twothink.cn>
 */
class Model  extends Admin {

    /**
     * 模型管理首页
     * @author 艺品网络  <twothink.cn>
     */
    public function index(){
        $map = array('status'=>array('gt',-1));
        $list = $this->lists('Model',$map,'id desc'); 
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->assign('meta_title','模型管理');
        return $this->fetch();
    }

    /**
     * 新增页面初始化
     * @author 艺品网络  <twothink.cn>
     */
    public function add(){
        //获取所有的模型
        $models = \think\Db::name('Model')->where(array('extend'=>0))->field('id,title')->select(); 
        $this->assign('models', $models);
        $this->assign('meta_title','新增模型');
        return $this->fetch();
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
        $Model = \think\Db::name('Model');
        $data = $Model->field(true)->find($id); 
        if(!$data){
            $this->error($Model->getError());
        }
        $data['attribute_list'] = empty($data['attribute_list']) ? '' : explode(",", $data['attribute_list']);
        $fields = \think\Db::name('Attribute')->where(array('model_id'=>$data['id']))->column('id,name,title,is_show'); 
        $fields = empty($fields) ? array() : $fields;
        // 是否继承了其他模型
        if($data['extend'] != 0){
            $extend_fields  = \think\Db::name('Attribute')->where(array('model_id'=>$data['extend']))->column('id,name,title,is_show');
            $fields        += $extend_fields;
        }
        
        // 梳理属性的可见性
        foreach ($fields as $key=>$field){
            if (!empty($data['attribute_list']) && !in_array($field['id'], $data['attribute_list'])) {
                $fields[$key]['is_show'] = 0;
            }
        }
        
        // 获取模型排序字段
        $field_sort = json_decode($data['field_sort'], true);
        if(!empty($field_sort)){
            foreach($field_sort as $group => $ids){
                foreach($ids as $key => $value){
                    $fields[$value]['group']  =  $group;
                    $fields[$value]['sort']   =  $key;
                }
            }
        } 
        //获取所有的模型
        $models = \think\Db::name('Model')->where(array('extend'=>0))->field('id,title')->select(); 
        $this->assign('models', $models);
        // 模型字段列表排序
        $fields = list_sort_by($fields,"sort");    
        $this->assign('fields', $fields);
        $this->assign('info', $data);
        $this->assign('meta_title', '编辑模型');
        return $this->fetch();
    }

    /**
     * 删除一条数据
     * @author 艺品网络  <twothink.cn>
     */
    public function del(){
        $ids = input('ids'); 
        empty($ids) && $this->error('参数不能为空！');
        $ids = explode(',', $ids);
        foreach ($ids as $value){
            $res = model('Modelmodel')->del($value);
            if(!$res){
                break;
            }
        }
        if(!$res){
            $this->error('删除模型失败,只支持删除文档模型和独立模型');
        }else{
            $this->success('删除模型成功！');
        }
    }

    /**
     * 更新一条数据 
     */
    public function update(){ 
    	$validate = validate('Model');
    	if(!$validate->check(request()->Post())){
    		return $this->error($validate->getError());
    	}
        $res = model('Modelmodel')->updates(); 
        if(!$res){
            $this->error(model('Model')->getError());
        }else{
            $this->success(isset($res['id'])?'更新成功':'新增成功', Cookie('__forward__'));
        }
    } 
    /**
     * 生成一个模型 
     */
    public function generate(){
        if(!request()->isPost()){
            //获取所有的数据表
            $tables = model('Modelmodel')->getTables();

            $this->assign('tables', $tables);
            $this->assign('meta_title','生成模型');
            return $this->fetch();
        }else{ 
            $data = input('param.'); 
            $table = $data['table']; 
            empty($table) && $this->error('请选择要生成的数据表！');
            $validate = validate('Model');
            if(!$validate->check(request()->Post())){
            	return $this->error($validate->getError());
            }
            $res = model('Modelmodel')->generate($table,$data['name'],$data['title']);
            if($res){
            	if($data['copy'] == 2){//复制表
            		db()->query('CREATE TABLE '.config('database.prefix').$data['name'].' LIKE '.$data['table']);
            		$this->success('复制表并生成模型成功！', url('index'));
            	}
                $this->success('生成模型成功！', url('index'));
            }else{
                $this->error(model('Modelmodel')->getError());
            }
        }
    }
}
