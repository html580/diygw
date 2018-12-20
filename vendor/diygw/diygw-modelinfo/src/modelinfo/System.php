<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace think\modelinfo;

/*
 * @title 系统(动态)模型处理类用与后台系统模型的处理 非静态模型
 * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
 */
class System extends Base {
    private $type;//模型类型 1:单线模型往上级查找列表定义 2:绑定多个模型获取基础模型的列表定义(即分支模型V形模型)

    public function __construct($data = ['type'=>1])
    {
         foreach ($data as $key=>$value){
             $this->$key = $value;
         }
    }
    /*
     * @title 获取当前模型信息
     * @param $model_id 模型ID
     * @param $returnmodel   true 是否返回当前模型信息
     * @param $status true 是否查询父级模型
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function info($model_id,$returnmodel=false,$status=true){
        if($status){
            $model_list = $this->get_parent_model($model_id);
        }else{
            $model_list[] =  db('Model')->getById($model_id);
        }
        $this->model = $model_list;
        if($returnmodel){
            $model_list = Array_mapping($model_list,'id');
            $this->info = $model_list[$model_id];
        }
        return $this;
    }
    /*
     * 获取模型参数的所有父级模型列表
     * @param int $cid 模型id
     * @return array 参数模型和父模型的信息集合
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function get_parent_model($cid){
        if(empty($cid)){
            return false;
        }
        $cates  =   db('Model')->where('status','eq',1)->select();
        $child  =   db('Model')->getById($cid);//获取参数模型的信息
        $pid    =   $child['extend'];
        $temp   =   array();
        $res[]  =   $child;
        while(true){
            foreach ($cates as $key=>$cate){
                if($cate['id'] == $pid){
                    $pid = $cate['extend'];
                    array_unshift($res, $cate); //将父模型插入到数组第一个元素前
                }
            }
            if($pid == 0){
                break;
            }
        }
        return $res;
    }
    /*
     * @title 列表定义解析
     * @param $list_grid 列表定义规则
     * @param $type 1:单线模型往上级查找列表定义 2:绑定多个模型获取基础模型的列表定义(即分支模型V形模型)
     * @param $model_id 模型ID
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getListField($list_grid=false){
        if(!$list_grid){
            $model_list = $this->model;
            switch ($this->type){
                case 1:
                    rsort($model_list);
                    foreach ($model_list as $value){
                        if(!empty($value['list_grid'])){
                            $list_grid = $value['list_grid'];
                            continue;
                        }
                    }
                    break;
                default:
                    $list_grid = $model_list[0]['list_grid'];
                    break;
            }
        }
        return parent::getListField($list_grid);
    }

    /*
     * @title 获取高级搜索配置
     * @param $type 1:单线模型往上级查找 2:绑定多个模型获取基础模型的搜索配置(即分支模型V形模型)
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getSearchList(){
        $model_list = $this->model;
        switch ($this->type){
            case 1:
                rsort($model_list);
                foreach ($model_list as $value){
                    if(!empty($value['search_list'])){
                        $search_list = $value['search_list'];
                        continue;
                    }
                }
                break;
            default:
                $search_list = $model_list[0]['search_list'];
                break;
        }
        if(empty($search_list))
            return $this;

        $search_arr = json_decode($search_list,true);

        //value extra规则解析
        foreach ($search_arr as $key=>&$value){
            if(0 === strpos($value['value'],':') || 0 === strpos($value['value'],'[')) {
                $value['value'] = parse_field_attr($value['value']);
            }
            if(!empty($value['extra'])){
                $value['extra'] = parse_field_attr($value['extra']);
            }
        }
        $this->info['search_list'] = $search_arr;
        $this->getSearchFixed();//调用固定搜索
        return $this;
    }
    /*
     * @title 获取固定搜索配置
     * @param $type 1:单线模型往上级查找 2:绑定多个模型获取基础模型的搜索配置(即分支模型V形模型)
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getSearchFixed(){
        $model_list = $this->model;
        switch ($this->type){
            case 1:
                rsort($model_list);
                foreach ($model_list as $value){
                    if(!empty($value['search_fixed'])){
                        $search_list = $value['search_fixed'];
                        continue;
                    }
                }
                break;
            default:
                $search_list = $model_list[0]['search_fixed'];
                break;
        }
        $search_arr = json_decode($search_list,true);

        $param = request()->param();
        //value 规则解析
        foreach ($search_arr as $key=>&$value){
            if(0 === strpos($value['value'],':') || 0 === strpos($value['value'],'[')) {
                $string = $value['value'];
                $str = substr($string,1);
                if(0 === strpos($str,'[')){
                    if(preg_match('/\[([a-z_]+)\]/',$str,$matches)){
                        if(!isset($param[$matches['1']])){
                            unset($search_arr[$key]);
                            continue;
                        }
                    }
                }
                $value['value'] = parse_field_attr($string);
            }
        }
        $this->info['search_fixed'] = $search_arr;
        return $this;
    }
    /*
     * 获取模型字段排序列表
     * @param  $model_id 模型id
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getFields($model_id = false){
        if(!$model_id){
            $model_list = $this->model;
            rsort($model_list);
            $model_id = $model_list[0]['id'];
        }
        $fields = get_model_attribute($model_id);
        foreach ($fields as $key => $value) {
            $data_name = array_column($value,'name');
            if(count($data_name) == count(array_filter($data_name)))
                $this->info['fields'][$key] = Array_mapping($fields[$key],'name');
        }
        return $this;
    }
    /*
     * @title 获取button组
     * @param $button 按钮规则
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getButton($button = false){
        //TODO 后期定位扩展
        return $this;
    }
}