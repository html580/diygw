<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace think\modelinfo;

use think\Db;
use think\Loader;
use think\Request;

/*
 * @title 模型解析类公共类
 * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
 */
class Base{
    public $model;//当前模型信息
    public $QueryModel;//绑定模型对象列表
    public $info;//解析后的信息
    public $pk = 'id';//主键
    public $scene = false; //应用场景

    /*
     * info解析初始化
     */
    public function setInit(){
        $info = $this->info;
        //field_group
        if(isset($info['field_group'])){
            $this->info['field_group'] = parse_config_attr($this->info['field_group']);
        }
        //fields:extra
        if(isset($info['field_group']) && isset($info['fields'])){
            $this->set_extra($this->info['field_group'],$this->info['fields']);
        }
        return $this;
    }
    /*
     * 操作场景(控制器方法)
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function scene($scene = false){
        if($scene)
            $this->scene = $scene;
        return $this;
    }
    /*
     * @title 列表定义解析
     *  @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getListField($list_grid=''){
        //解析
        $fields = $grids = [];
        if(!empty($list_grid)){
            $grids  = is_array($list_grid)?$list_grid:preg_split('/[;\r\n]+/s', trim($list_grid));
            foreach ($grids as &$value) {
                // 字段:标题:链接
                $val      = explode(':', $value);
                // 支持多个字段显示
                $field   = explode(',', $val[0]);
                $field_name = explode('|', $field[0]);
                $value    = ['name'=>$field_name['0'],'field' => $field, 'title' => $val[1]];
                if(isset($val[2])){
                    // 链接信息
                    $value['href']  =   $val[2];
                }
                if(strpos($val[1],'|')){
                    // 显示格式定义
                    list($value['title'],$value['format'])    =   explode('|',$val[1]);
                }
                foreach($field as $vals){
                    $array  =   explode('|',$vals);
                    if($val[1] !== '操作'){
                        $fields[$array[0]] = $val[1];
                    }
                }
                unset($fields[0]);
            }
        }
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        $this->info['field'] = array_keys($fields); //列表字段集
        $this->info['list_field'] = $grids; //列表规则
        return $this;
    }
    /*
     * @title   字段类型extra属性解析
     * @param array  $field_group 表单显示分组
     * @param array  $fields      字段列表
     * @param array  $data        数据(为空  优先info.data info.field_default_value)
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function set_extra($field_group='',$fields='',$data=''){
        if(empty($fields))
            return false;
        if(empty($data) && isset($this->info['data'])){
            $data = $this->info['data'];
        }else{
            if(!isset($this->info['field_default_value'])){
                $this->FieldDefaultValue($fields);
            }
            $data = $this->info['field_default_value'];
        }
        if(!empty($field_group)){
            foreach ($field_group as $key=>$vaule){
                if(!isset($fields[$key]))
                    continue;
                foreach ($fields[$key] as $k=>&$v){
                    if(!empty($v['extra'])){
                        $v['extra'] = parse_field_attr($v['extra'],$data,$data[$v['name']]);
                    }
                }
            }
        }else{
            foreach ($fields as &$v){
                if(!empty($v['extra'])){
                    $v['extra'] = parse_field_attr($v['extra'],$data,$data[$v['name']]);
                }
            }
        }
        $this->info['fields'] = $fields;
        $this->info['data'] = $data;
        return $this;
    }
    /*
     * @title   拼装搜索条件
     * @$param  [] 请求信息
     * @$where_default [] 默认搜索条件 在所有请求查询条件的为空情况下启用设置怎不使用模型配置的条件
     * @$where_solid [] 固定搜索条件 在所有条件下都会加上该条件
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function getWhere($param=false,$where_default=false,$where_solid=false){
        if (!$param){
            $param = request()->param();
        }
        $where=[];
        //默认搜索条件
        if(empty($param['like_seach']) && empty($param['seach_all']) && !$where_default){
            if( $search_list = $this->info['search_list']){
                foreach ($search_list as $value){
                    //表达式为空不参与搜索
                    if(empty($value['exp']))
                        continue;
                    if(isset($where[$value['name']])){
                        $where[$value['name']]['0']=$where[$value['name']];
                        $where[$value['name']]['1']=$this->QueryExpression($value['exp'],$value['value']);
                    }else{
                        $where[$value['name']] = $this->QueryExpression($value['exp'],$value['value']);
                    }
                }
            }
        }elseif($where_default){
            $where += $where_default;
        }
        //自由组合搜索
        if(!empty($param['seach_all']) && empty($param['like_seach'])){
            $seach_all = $param['seach_all'];
            foreach ($seach_all['exp'] as $key => $value) {
                //表达式为空不参与搜索
                if(empty($value))
                    continue;
                $search_arr = $this->QueryExpression($value,$seach_all['value'][$key]);
                if(isset($where[$seach_all['name'][$key]])){
                    $where[$seach_all['name'][$key]]['0']=$where[$seach_all['name'][$key]];
                    $where[$seach_all['name'][$key]]['1']=$search_arr;
                }else{
                    $where[$seach_all['name'][$key]]=$search_arr;
                }
            }
        }elseif (!empty($param['like_seach'])){ //搜索列表定义字段
            if($this->info['list_field']){
                $fields = array_unique(array_column($this->info['list_field'],'name'));
                $fields = implode('|',$fields);
                $where[] = [$fields,'like',"%".$param['like_seach']."%"];
            }else{
                $where[] = [$this->pk,'eq',$param['like_seach']];
            }
        }else{
            $where[$this->pk] = ['gt',0];
        }
        //固定搜索
        if($where_solid){
            $where += $where_solid;
        }elseif(isset($this->info['search_fixed'])){
            foreach ($this->info['search_fixed'] as $value){
                $search_arr = $this->QueryExpression($value['exp'],$value['value']);
                if(isset($where[$value['name']])){
                    $where[$value['name']]['0']=$where[$value['name']];
                    $where[$value['name']]['1']=$search_arr;
                }else{
                    $where[$value['name']] = $search_arr;
                }
            }
        }
        $this->info['where'] = $where;
        return $this;

    }
    /*
     * @title 查询表达式
     * @param $exp 表达式规则
     * @param $value 参数
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function QueryExpression($exp=false,$value){
        switch (trim($exp)) {//判断查询方式
            case 'neq':
                $search_arr=['neq',$value];
                break;
            case 'lt':
                $search_arr=['lt',$value];
                break;
            case 'elt':
                $search_arr=['elt',$value];
                break;
            case 'gt':
                $search_arr=['gt',$value];
                break;
            case 'egt':
                $search_arr=['egt',$value];
                break;
            case 'like':
                $search_arr=['like',"%".$value."%"];
                break;
            default:
                $search_arr=['eq',$value];
                break;
        }
        return $search_arr;
    }
    /**
     * @title  获取字段列表配置默认值 函数支持解析的参数默认为requer信息
     * @param $fields array 字段列表
     * @param $data   array 数据
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     * @return $obj
     */
    public function FieldDefaultValue($fields=false,$data=''){
        if(!$fields)
            $fields = $this->info['fields'];
        $arr = [];
        foreach ($fields as $key=>$value){
            $arr = array_merge_recursive($arr,$value);
        }
        if(empty($data)){
            $data = Request::instance()->param();
        }
        $new_arr = [];
        foreach ($arr as $key=>$value){
            if(isset($value['value']))
                if(0 === strpos($value['value'],':') || 0 === strpos($value['value'],'[')) {
                    if(!isset($data[$value['name']])){
                        $data[$value['name']] = '';
                    }
                    $value['value'] = parse_field_attr($value['value'],$data,$data[$value['name']]);
                }
                $new_arr[$value['name']] = $value['value'];
        }
        $this->info['field_default_value'] = $new_arr;
        return $this;
    }
    /*
     * 获取单条数据信息(使用模型查询)
     * @param  $where 查询条件
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getFind($where){
        if(!$this->QueryModel){
            $this->getQueryModel();
        }
        $data = [];
        foreach ($this->QueryModel as $key=>$value){
            $arr= [];
            if($arr = $value->where($where)->find()){
                $data += $arr->toArray();
            }
        }
        $this->info['data'] = $data;
        return $this;
    }
    /*
     * 删除数据
     * @param  $where 查询条件
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getDel($where){
        if(!$this->QueryModel){
            $this->getQueryModel();
        }
        foreach ($this->QueryModel as $key=>$value){
            if(!$arr = $value->where($where)->delete()){
                return $arr;
            }
        }
        return true;
    }
    /*
     * @title View视图实例化
     * @param $model_list 模型列表
     * @return obj View对象
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getView($model_list = false){
        //模型列表
        if(!$model_list)
            $model_list = $this->model;

        $Basics_modelname = $model_list[0]['name'];
        $Basics_model_fields = Db::name($Basics_modelname)->getTableFields();
        $query_modelobj = Db::view($Basics_modelname,$Basics_model_fields);
        if(count($model_list) > 1){
            for ($i=1; $i<count($model_list); $i++) {
                $table_name = $model_list[$i]['name'];
                $query_modelobj->view($table_name,true,$table_name.'.id='.$Basics_modelname.'.id');
            }
        }
        return $query_modelobj;
    }
    /*
     * @title View视图分页查询
     * @return array 参数模型和父模型的信息集合
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getViewList($where=false){
        $param = request()->param();
        if(!$where){
            $where = $this->info['where'];
        }
        //模型列表
        $model_list = $this->model;
        $Basics_modelname = $model_list[0]['name'];
        $Basics_model_fields = Db::name($Basics_modelname)->getTableFields();
        $query_modelobj = Db::view($Basics_modelname,$Basics_model_fields);
        if(count($model_list) > 1){
            for ($i=1; $i<count($model_list); $i++) {
                $table_name = $model_list[$i]['name'];
                $query_modelobj->view($table_name,true,$table_name.'.id='.$Basics_modelname.'.id');
            }
            $order = 'level desc,'.$this->pk.' desc';
        }else{
            $order = $this->pk.' desc';
        }

//        $field = $this->info['field'] ? $this->info['field']:false;
//        $field = array_combine($field,$field);
//        if($field['id']){
//            $field['id'] = $Basics_modelname.'.id';
//        }

        $listRows = isset($param['limit'])?$param['limit']:config('list_rows');
        // 分页查询
//        $list = $query_modelobj->where($where)->order($order)->field($field)->paginate($listRows);
        $list = $query_modelobj->where($where)->order($order)->paginate($listRows);

        // 获取分页显示
        $page = $list->render();
        if(is_object($list)){
            $list=$list->toArray();
        }
        $this->info['data'] = $list;
        $this->info['page'] = $page;
        return $this;
    }
    /**
     * 实例化模型列表的模型对象
     * @param string    $layer 业务层名称
     * @param string    $base 默认模型名称
     * @param bool   $appendSuffix 是否添加类名后缀
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getQueryModel( $layer = 'model',$base='Base',$appendSuffix=false,$common = 'common'){
        $model_list = $this->model;
        foreach ($model_list as $key=>$value){
            $name = $value['name'];
            $model[] = $this->getModelClass($name,$layer,$base,$appendSuffix,$common);
        }
        $this->QueryModel = $model;
        return $this;
    }
    /**
     * 实例化（分层）模型
     * @param string $name         Model名称
     * @param string $layer        业务层名称
     * @param string    $base 默认模型名称
     * @param bool   $appendSuffix 是否添加类名后缀
     * @param string $common       公共模块名
     * @param stting $setname      设置当前模型名称
     * @return object
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getModelClass($name = '', $layer = 'model',$base = 'Base', $appendSuffix = false, $common = 'common',$setname=''){
        if (false !== strpos($name, '\\')) {
            $class  = $name;
            $module = request()->module();
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = request()->module();
            }
            $class = Loader::parseClass($module, $layer, $name, $appendSuffix);
        }
        if(!empty($setname)){
            $setname = ['diygw_name'=>$setname];
        }
        if (class_exists($class)) {
            $model = is_array($setname)?new $class($setname):new $class();
        }else{
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
            if (class_exists($class)) {
                $model = is_array($setname)?new $class($setname):new $class();
            } else {
                if($name != $base && !empty($base) || $base !== false){
                    $model = $this->getModelClass($base,$layer,$base,$appendSuffix,$common,$name);
                }
            }
        }
        return $model;
    }
    /*
     * 新增更新数据
     * @param  $param 编辑数据
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getUpdate($param=''){
        if(empty($param)){
            $param = request()->param();
        }

        //自动完成
        $param = $this->checkModelAttr($this->info['fields'],$param);
        //获取模型对象
        if(!$this->QueryModel){
            $this->getQueryModel();
        }
        $QueryModel = $this->QueryModel;
        $res_id = '';
        foreach ($QueryModel as $value){
            $logic = $value;
            $res_id = $logic->editData($param,$res_id);
            if(!$res_id){
                $this->error = $logic->getError();
                return false;
            }
        }
        return $res_id;
    }

    /*
     * 自动验证
     * @param  array $fields 字段列表
     * @param  array $data   验证数据
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function checkValidate($fields=false,$data=false){
        if(!$fields){
            $fields = $this->info['fields'];
        }
        if(is_array($fields)){
            $fields = $this->MergeFields($fields);
        }
        if(count($fields) < 1){
            $fields = [];
        }
        if(!$data){
            $data = request()->param(); //获取数据
        }

        $validate   =   array();
        $scene = 'auto';//验证场景
        $validate_scene_field = [];//验证字段
        foreach($fields as $key=>$attr){
            if(!isset($attr['validate_time']))
                continue;
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
        }
        //验证场景
        if($this->scene){
            $scene = $scene;
        }
        foreach ($this->model as $value){
            $vli_obg = $this->getModelClass($value['name'],'validate');
            if(method_exists($vli_obg,'Validationrules')){
                $vli_obg->Validationrules(['rule'=>$validate,'scene'=>$scene,'scene_fields'=>$validate_scene_field]);
            }else{
                $vli_obg::make($validate);
                $vli_obg->scene($scene);
            }
            if (!$vli_obg->check($data)) {
                $this->error = $vli_obg->getError();
                return false;
            }
        }
        return true;
    }
    /**
     * 检测属性的自动验证和自动完成属性 并进行验证
     * 验证场景  insert和update二个个场景，可以分别在新增和编辑
     * @$fields 模型字段属性信息(get_model_attribute($model_id,false))
     * @return boolean  验证通过返回自动完成后的数据 失败返回原始数据
     */
    public function checkModelAttr($fields=false,$data=[]){
        if(!$fields){
            $fields = $this->info['fields'];
        }
        if(is_array($fields)){
            $fields = $this->MergeFields($fields);
        }
        $auto_data = $data; //自动完成更新接收数据
        foreach($fields as $key=>$attr){
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
                        $auto_data[$attr['name']] = $data[$attr['name']]?arr2str($data[$attr['name']]):'';
                    }elseif('datetime' == $attr['type'] || 'date' == $attr['type']){ // 日期型
                        $auto_data[$attr['name']] = strtotime($data[$attr['name']]);
                    }
                    break;
            }
        }
        return $auto_data;
    }
    /**
     * 字段分组列表转一维数组
     * @param array $list 列表数据
     * @author DIY官网 diygwcom@foxmail.com
     */
    private function MergeFields($fields=false){
        $attrList = [];
        if (is_array($fields) && $fields){
            foreach ($fields as $key=>$value){
                $attrList = array_merge_recursive($attrList,$value);
            }
        }
        return $attrList;
    }

    /**
     * 对列表数据进行字段映射处理
     * @param array $list 列表数据
     * @param array $int_to_string 映射关系二维数组
     * @return $this
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function parseIntTostring($list=false,$int_to_string=false){
        if(!$list){
            $list = $this->info['data']['data'];
        }
        if(!$int_to_string && isset($this->info['int_to_string'])){
            $int_to_string = $this->info['int_to_string'];
        }else{
            return $this;
        }
        $this->info['data']['data'] = int_to_string($list,$int_to_string);
        return $this;
    }
    /**
     * 对列表数据进行显示处理
     * @param array $list 列表数据
     * @param array $attrList fields字段列表
     * @return $this
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function parseList($list=false,$attrList=false){
        if(!$list){
            $list = $this->info['data']['data'];
        }
        if(!$attrList){
            $attrList = $this->info['fields'];
        }

        $attrList = $this->MergeFields($attrList);
        $attrList = Array_mapping($attrList,'name');
        if(is_array($list)){
            foreach ($list as $k=>$data){
                foreach($data as $key=>$val){
                    if(isset($attrList[$key])){
                        $extra      =   $attrList[$key]['extra'];
                        $type       =   $attrList[$key]['type'];
                        if('select'== $type || 'checkbox' == $type || 'radio' == $type || 'bool' == $type) {
                            // 枚举/多选/单选/布尔型
                            $options    =   parse_field_attr($extra);
                            if($options && array_key_exists($val,$options)) {
                                $data[$key]    =   $options[$val];
                            }
                        }elseif('date'==$type && is_int($val)){ // 日期型
                            $data[$key]    =   date('Y-m-d',$val);
                        }elseif('datetime' == $type && is_int($val)){ // 时间型
                            $data[$key]    =   date('Y-m-d H:i',$val);
                        }
                    }
                }
                $list[$k]   =   $data;
            }
        }
        $this->info['data']['data']=$list;
        return $this;
    }
    /**
     * 对列表数据进行列表解析
     * @param array $list 列表数据
     * @param array $list_field 列表定义规则
     * @param array $replace_string 字符串替换规则
     * @return $this
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function parseListIntent($list=false,$list_field=false,$replace_string = ''){
        if(!$list){
            $list = $this->info['data']['data'];
        }
        if(!$list_field){
            $list_field = $this->getListField();
        }
        if(isset($this->info['replace_string']) && !empty($this->info['replace_string'])){
            $replace_string = $this->info['replace_string'];
        }
        if(is_array($list)){
            foreach ($list as $k=>$v){
                foreach ($this->info['list_field'] as $key=>$value){
                    $list_data_new[$k][$key+1] = intent_list_field($v,$value,$replace_string);
                }
            }
        }
        $this->info['data']['data']=$list_data_new;
        return $this;
    }
    /**
     * 指定info获取字段 支持字段排除和指定数据字段
     * @param mixed   $field
     * @param boolean $except    是否排除
     * @return $this
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function field($field, $except = false)
    {
        if (empty($field)) {
            return $this;
        }
        if (is_string($field)) {
            $field = array_map('trim', explode(',', $field));
            $field = array_flip($field);
        }
        if($except){
            $field  = array_diff_key($this->info, $field);
        }else{
            $field = array_intersect_key($this->info, $field);
        }
        $this->options['field'] = $field;
        return $this;
    }
    /**
     * param数据字段转换
     * @author diygw diygwcom@foxmail.com
     * @param $array 要转换的数组
     * @return 返回param请求数据数组
     */
    protected function buildParam($array=[])
    {
        $data = $this->request->param();
        if (is_array($array)&&!empty($array)){
            foreach( $array as $item=>$value ){
                $data[$item] = $data[$value];
            }
        }
        return $data;
    }
    /*
     * @title 设置模型配置信息
     * @$arr array 支持数组[name=>value]
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function setInfo($arr,$value = ''){
        if(is_array($arr)){
            foreach ($arr as $key=>$v){
                $this->info[$key] = $v;
            }
        }else{
            $this->info[$arr] = $value;
        }
        return $this;
    }
    /*
    * @title 获取对象值
    * @$param 要获取的参数 支持多级  a.b.c
    * @return array
    * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
    */
    public function getParam($param = false){
        if($param){
            if (is_string($param)) {
                $name = explode('.', $param);
                $arr = $this->toArray($name[0]);
                for ($i=1;$i< count($name);$i++){
                    $arr = $arr[$name[$i]];
                }
                return $arr;
            }
        }else{
            return $this->toArray();
        }
    }
    //对象转数组
    public function toArray($name='info'){
        return (array)$this->$name;
    }
    /**
     * 返回模型的错误信息
     * @access public
     * @return string|array
     */
    public function getError()
    {
        return $this->error;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}