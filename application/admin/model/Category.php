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
 * 分类模型 
 */
class Category extends Model{ 
    /* 用户模型自动完成 */
    //新增及更新
    protected $auto = ['model','model_sub','type','reply_model','extend'];
    // 新增
    protected $insert = ['status'=>1];   
    protected function setModelAttr($value, $data)
    {
           if(isset($data['model'])){ return arr2str($data['model']);  }else{ return true;  } 
    }
    protected function setModelSubAttr($value, $data)
    {
           if(isset($data['model_sub'])){  return arr2str($data['model_sub']);  }else{ return true; } 
    } 
    protected function setTypeAttr($value, $data)
    {
           if(isset($data['type'])){  return arr2str($data['type']);  }else{ return true; } 
    }
    protected function setReplyModelAttr($value, $data)
    {
           if(isset($data['reply_model'])){  return arr2str($data['reply_model']);  }else{ return true; } 
    } 
    protected function setExtendAttr($value, $data)
    {
           if(isset($data['extend'])){  return arr2str($data['extend']);  }else{ return true; } 
    }  
    // birthday读取器
    protected function getModelSubAttr($value,$data)
    {
        if(isset($data['model_sub']))
            return explode(',', $data['model_sub']);
    }
     /* 分割模型 */
    protected function getModelAttr($value,$data){
        if(!empty($data['model'])){
            return explode(',', $data['model']);
        } 
    }

    protected function getTypeAttr($value,$data){
        /* 分割文档类型 */
        if(!empty($data['type'])){
            return explode(',', $data['type']);
        }
    }

    protected function getReplyModelAttr($value,$data){
        /* 分割模型 */
        if(!empty($data['reply_model'])){
            return explode(',', $data['reply_model']);
        }
    }

    protected function getReplyTypeAttr($value,$data){
        /* 分割文档类型 */
        if(!empty($data['reply_type'])){
            return explode(',', $data['reply_type']);
        }
    }

    protected function getExtendAttr($value,$data){
        /* 还原扩展数据 */
        if(!empty($data['extend'])){
            return json_decode($data['extend'], true);
        }
    }
    /**
     * 获取分类详细信息
     * @param  milit   $id 分类ID或标识
     * @param  boolean $field 查询字段
     * @return array     分类信息 
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find()->toArray();
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树 
     */
    public function getTree($id = 0, $field = true){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有分类 */
        $map  = array('status' => array('gt', -1));
        $list = $this->field($field)->where($map)->order('sort')->select();
        foreach ($list as $key => $value) {
            $list[$key]=$value->toArray();
        } 
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);

        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }

    /**
     * 获取指定分类子分类ID
     * @param  string $cate 分类ID
     * @return string       id列表 
     */
    public function getChildrenId($cate) {
        $field    = 'id,name,pid,title,link_id';
        $category = $this->getTree($cate, $field);
        $ids[]    = $cate;
        foreach ($category['_'] as $key => $value) {
            $ids[] = $value['id'];
        }
        return implode(',', $ids);
    }

    /**
     * 获取指定分类的同级分类
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array 
     */
    public function getSameLevel($id, $field = true){
        $info = $this->info($id, 'pid');
        $map = array('pid' => $info['pid'], 'status' => 1);
        return $this->field($field)->where($map)->order('sort')->select();
    }

    /**
     * 更新分类信息
     * @return boolean 更新状态 
     */
    public function updates($data){ 
        if(!$data){ //数据对象创建错误
            return false;
        } 
        $validate = validate('Category');
        /* 添加或更新数据 */
        if(empty($data['id'])){
        	if(!$validate->check($data)){
        		return $this->error=$validate->getError();
        	}
            $res = $this->insert($data);
        }else{ 
        	if(!$validate->scene('edit')->check($data)){
        		return $this->error=$validate->getError();
        	}
            $res = $this->update($data);
        }

        //更新分类缓存
        cache('sys_category_list', null);

        //记录行为
        action_log('update_category', 'category', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

     

}
