<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Request;
use think\Model;
use think\Db;
/*
 * 插件公共模型  所有使用Twothink模型创建的表都可以继承本模型简化开发
 * @atuh 艺品网络
 */
class AddonsBase extends Model {
    protected $autoWriteTimestamp = false;
    protected $FormData; //接收表单数据
    protected $addon_name; //插件名称

    public function __construct($name = ''){
        $data = Request::instance()->param();
        if(empty($this->name) && !empty($data['_controller'])){
            $this->name = $data['_controller'];
        }
        if(empty($this->addon_name) && !empty($data['_addons'])){
            $this->addon_name = $data['_addons'];
        }
        parent::__construct($name);
    }
    public function initialize(){
        $data = Request::instance()->param();
        if(empty($data['id']))
            unset($data['id']);
        $this->FormData = $data;
        parent::initialize();
    }
    /*
     * 获取模型信息
     */
    public function get_model(){
        return Db::name('model')->where(['name'=>$this->name])->find();
    }
    /*
     * 获取模型字段信息
     * $fields array ['fields'=>'id,title','status'=>false]查询的字段 statu字段查询方式 true排除 false查询指定字段
     */
    public function get_fields($fields = false){
        $model = $this->get_model();
        //获取表单字段排序
        $_fields = get_model_attribute($model['id'],true);
        //字段排除
        if($fields){
            $field = explode(",", $fields['field']);
            $field = array_flip( $field );
            foreach ($_fields as $key=>$vaule){
                foreach ($vaule as $k=>$v){
                    if($fields['status'] == true){//排除字段
                        if(isset($field[$v['name']])){
                            unset($_fields[$key][$k]);
                        }
                    }else{ //查询字段
                        if(!isset($field[$v['name']])){
                            unset($_fields[$key][$k]);
                        }
                    }
                }
            }
        }
        return $_fields;
    }
    /**
     * 新增或添加模型数据
     * @return boolean    数据ID-操作成功，false-操作失败
     */
    public function updates($id='') {
        //自动验证及自动完成
        if(!$check = $this->checkModelAttr()){
            return false;
        };
        $data = $this->FormData;
        if (empty($data['id'])) {//新增数据
            if(!empty($id)){ $data['id'] = $id;  }
            $id = $this->data($data)->allowField(true)->save();
            if (!$id) {
                $this->error = '新增数据失败！';
                return false;
            }
            $id = $this->id;
        } else { //更新数据
            $id = $data['id'];
            $status = $this->data($data,true)->allowField(true)->save($data,['id'=>$id]);
            if (false === $status) {
                $this->error = '更新数据失败！';
                return false;
            }
        }
        return $id;
    }
    //删除数据
    public function del($map){
        if($this->where($map)->delete()){
            return true;
        }else{
            $this->error('删除失败');
            return false;
        }
    }
    /**
     * 检测属性的自动验证和自动完成属性 并进行验证
     * 验证场景  insert和update二个个场景，可以分别在新增和编辑
     * @return boolean
     */
    public function checkModelAttr($model_id=false,$data=false){
        if(!$data){
            $data = $this->FormData; //获取数据
        }
        //查询模型信息
        if(!$model_id){
            if(!$model = $this->get_model()){
                $model_id = $data['model_id'];
            }else{
                $model_id = $model['id'];
            }
        }
        if(!$model_id)
            return true;
        $fields     =   get_model_attribute($model_id,false);
        $validate   =   array();
        $auto_data = $data;
        foreach($fields as $key=>$attr){
            switch ($attr['validate_time']) {
                case '1':
                    if (empty($data['id'])) {//新增数据
                        $scene = 'insert';//验证场景
                        // 自动验证规则
                        if(!empty($attr['validate_rule'])) {
                            if($attr['is_must']){// 必填字段
                                $require = 'require|';
                                $require_msg= $attr['title'].'不能为空|';
                            }
                            $msg = $attr['error_info']?$attr['error_info']:$attr['title'].'验证错误';
                            $validate[]=[$attr['name'], $require.$attr['validate_rule'],$require_msg.$msg];
                            $validate_scene_field[] = $attr['name'];//验证字段
                        }elseif($attr['is_must']){
                            $validate[]=[$attr['name'], 'require', $attr['title'].'不能为空'];
                            $validate_scene_field[] = $attr['name'];//验证字段
                        }

                    }
                    break;
                case '2':
                    if (!empty($data['id'])) {//编辑
                        $scene = 'update';//验证场景
                        // 自动验证规则
                        if(!empty($attr['validate_rule'])) {
                            if($attr['is_must']){// 必填字段
                                $require = 'require|';
                                $require_msg= $attr['title'].'不能为空|';
                            }
                            $msg = $attr['error_info']?$attr['error_info']:$attr['title'].'验证错误';
                            $validate[]=[$attr['name'], $require.$attr['validate_rule'],$require_msg.$msg];
                            $validate_scene_field[] = $attr['name'];//验证字段
                        }elseif($attr['is_must']){
                            $validate[]=[$attr['name'], 'require', $attr['title'].'不能为空'];
                            $validate_scene_field[] = $attr['name'];//验证字段
                        }
                    }
                    break;
                default:
                    $scene = 'auto';//验证场景
                    // 自动验证规则
                    if(!empty($attr['validate_rule'])) {
                        if($attr['is_must']){// 必填字段
                            $require = 'require|';
                            $require_msg= $attr['title'].'不能为空|';
                        }
                        $msg = $attr['error_info']?$attr['error_info']:$attr['title'].'验证错误';
                        $validate[]=[$attr['name'], $require.$attr['validate_rule'],$require_msg.$msg];
                        $validate_scene_field[] = $attr['name'];//验证字段
                    }elseif($attr['is_must']){
                        $validate[]=[$attr['name'], 'require', $attr['title'].'不能为空'];
                        $validate_scene_field[] = $attr['name'];//验证字段
                    }
                    break;
            }
            // 自动完成
            switch ($attr['auto_time']){
                case '1':
                    if(empty($data['id']) && !empty($attr['auto_rule'])){//新增
                        $auto_data[$attr['name']] = $attr['auto_rule']($data[$attr['name']],$data);
                    }
                    break;
                case '2':
                    if (!empty($data['id']) && !empty($attr['auto_rule'])) {//编辑
                        $auto_data[$attr['name']] = $attr['auto_rule']($data[$attr['name']],$data);
                    }
                    break;
                default:
                    if (!empty($attr['auto_rule'])){//始终
                        $auto_data[$attr['name']] = $attr['auto_rule']($data[$attr['name']],$data);
                    }elseif('checkbox'==$attr['type']){ // 多选型
                        $auto_data[$attr['name']] = arr2str($data[$attr['name']],$data);
                    }elseif('datetime' == $attr['type'] || 'date' == $attr['type']){ // 日期型
                        $auto_data[$attr['name']] = strtotime($data[$attr['name']]);
                    }
                    break;
            }
        }
        $this->FormData = $auto_data;//自动完成更新接收数据
        //判断验证模型
        $class = "addons\\{$this->addon_name}\\validate\\{$this->name}";
        if(class_exists($class)){//添加验证规则
            $validate_module = new $class();
            $validate_module->Validationrules(['rule'=>$validate,'scene'=>$scene,'scene_fields'=>$validate_scene_field]);
        }else{
            $validate_module = \think\Validate::make($validate);
            $validate_module->scene($scene);
        }
        if (!$validate_module->check($data)) {
            $this->error = $validate_module->getError();
            return false;
        }
        return true;
    }
}
?>