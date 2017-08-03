<?php 
namespace app\common\logic;
use think\Request;
use think\Model;

/**
 * 独立模型逻辑层公共模型
 * 所有独立层模型都需要继承此模型
 */
class Independent extends Model { 
	protected $autoWriteTimestamp = false;
    protected $FormData; //接收表单数据

    public function __construct($name=''){
        if(!empty($name) && !empty($name['twothink_name']) && count($name) == 1){
            $this->name=$name['twothink_name'];
            parent::__construct();
        }else{
            parent::__construct($name);
        }
    }
    public function initialize(){
        $data = Request::instance()->param();
        if(empty($data['id']))
            unset($data['id']);
        $this->FormData = $data;
        parent::initialize();
    }


    /**
     * 获取模型详细信息
     * @param  integer $id 文档ID
     * @return array       当前模型详细信息
     */
    public function detail($id) {
        //查询表字段
        $fields = $this->getTableFields(array('name'=>$this->name));
        if ($fields == false) {
            $data = array();
        } else {
            $data = $this->field(true)->where(['id'=>$id])->find();
            if (!$data) {
                $this->error = 'Base获取详细信息出错！';
                return false;
            }else{
                $data = $data->toArray();
            }
        }
        return $data;
    }
    /**
     * 新增或添加模型数据
     * @param  number $id 文章ID
     * @return boolean    true-操作成功，false-操作失败
     */
    public function updates($id = '') {
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
        //行为记录
        if($id){
            action_log('add_'.$this->name, $this->name, $id, UID);
        }
        return $id;
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
            if(!$model_id = \think\Db::name('Model')->where(['name'=>$this->name])->value('id')){
                $model_id = $data['model_id'];
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
        $validate_status = true;
        $module = \think\Request::instance()->module();
        $class = \think\Loader::parseClass($module, 'validate', $this->name, config('class_suffix'));
        $validate_scene_field = ['dd'];
        if (!class_exists($class)) {//判断app\{$model}\logic\是否存在模型
            $common = 'common';
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
            if (!class_exists($class)) {
                $validate_status = false;
            }
        }
        if($validate_status){//添加验证规则
            $validate_module = \think\Loader::validate($this->name);
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
