<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace diygw\helper;

use think\Exception;
/*
 * 数据排序
 * @Author: diygw  <diygwcom@foxmail.com>
 */
class Sort{
    //实例化对象
    protected $model;
    //表名或模型对象
    public $name;
    //主键名称
    public $pk = 'id';
    //关联字段
    private $pid = 'pid';
    //排序字段
    private $sort    = 'sort';

    public function __construct($param=''){
        if(!empty($param)){
            $this->setAttr($param);
        }
        //初始化模型对象
        if(is_object($this->name)){
            $this->model = $this->name;
        }else{
            $this->model = db($this->name);
        }
    }
    /*
     * 数据移动
     * @param array $where 查询表达式 ['id'=>100]
     * @param init  $pid   上级id
     */
    public function move($where,$pid){
        if($this->model->where($where)->setField($this->pid, $pid)){
            return true;
        }else{
            throw new Exception('数据移动失败');
        }
    }
    /*
     * 数据移动
     * @param array $where 查询表达式 ['pid'=>100]
     * @param init  $id    排序数据id
     * @param init  $sort  排序值
     */
    public function sort($where,$id,$sort){
        //排序数据
        $res_sort = $this->model->where($where)->order('sort')->column($this->pk);

        $key = array_search($id, $res_sort);
        if ($key !== false)
            array_splice($res_sort, $key, 1);
        $res_sort_new = [];
        foreach ($res_sort as $k=>$v){
            $res_sort_new[$k+1] = $v;
        }
        $new_key = array_search($sort,$res_sort_new);
        array_splice($res_sort_new,$new_key,0,$id);
        foreach ($res_sort_new as $k=>$v){
            if($this->model->where([$this->pk=>$v])->setField($this->sort, $k) === false){
                throw new Exception('排序失败');
            }
        }
        return true;
    }

    /**
     * 修改器 设置数据对象值
     * @access public
     * @param string(array) $name  属性名
     * @param mixed  $value 属性值
     * @return $this
     */
    protected function setAttr($name,$value=''){
        if(is_array($name)){
            foreach ($name as $key=>$value){
                $this->$key = $value;
            }
        }else{
            $this->$name = $value;
        }
        return $this;
    }
}