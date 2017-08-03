<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------

namespace app\admin\model;
use think\Model as Model;

/**
 * 文档基础模型
 */
class Modelmodel extends Model{
	// 设置当前模型名称
	protected $name = 'model';
	protected $auto = ['field_sort','attribute_list'];
	protected $insert = ['name','status'=>1];
// 	protected $update = ['create_time'];
	protected function setCreateTimeAttr($value){
		return time();
	}
	protected function setNameAttr($value){
		return strtolower($value);
	}
	protected function setFieldSortAttr($value){
		return empty($value) ? '' : json_encode($value);
	}
	protected function setAttributeListAttr($fields) {
		return empty($fields) ? '' : implode(',', $fields);
	}
    /**
     * 新增或更新一个文档
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     */
    public function updates(){
        /* 获取数据对象 */
        $data = request()->Post();
        if(empty($data)){
            return false;
        }
        /* 添加或新增基础内容 */
        if(empty($data['id'])){ //新增数据
            $id = $this->allowField(true)->create($data); //添加基础内容
            if(!$id){
                return false;
            }
        } else { //更新数据
            $status = $this->update($data); //更新基础内容
            if(false === $status){
                return false;
            }
        }
        // 清除模型缓存数据
        cache('document_model_list', null);
        //记录行为
        action_log('update_model','model',isset($data['id']) ? $data['id'] : $id->id,UID);
        //内容添加或更新完成
        return $data;
    }

    /**
     * 获取指定数据库的所有表名
     */
    public function getTables(){
        return \think\Db::connect()->getTables();
    }

    /**
     * 根据数据表生成模型及其属性数据
     * @author 艺品网络  <twothink.cn>
     */
    public function generate($table,$name='',$title=''){
        //新增模型数据

        if(empty($name)){
            $name = $title = substr($table, strlen(config('prefix')));
        }
        $data = array('name'=>$name, 'title'=>$title);
        $res = $this->create($data);
        if(!$res){
                return false;
        }
        //新增属性
        $fields = db()->query('SHOW FULL COLUMNS FROM '.$table);
        foreach ($fields as $key=>$value){
            $value  =   array_change_key_case($value);
            //不新增id字段
            if(strcmp($value['field'], 'id') == 0){
                continue;
            }

            //生成属性数据
            $data = array();
            $data['name'] = $value['field'];
            $data['title'] = $value['comment'];
            $data['type'] = 'string';	//TODO:根据字段定义生成合适的数据类型
            //获取字段定义
            $is_null = strcmp($value['null'], 'NO') == 0 ? ' NOT NULL ' : ' NULL ';
            $data['field'] = $value['type'].$is_null;
            $data['value'] = $value['default'] == null ? '' : $value['default'];
            $data['model_id'] = $res->id;
            $_POST = $data;		//便于自动验证
            model('Attribute')->updates($data, false);
        }

        return $res;
    }

    /**
     * 删除一个模型
     * @param integer $id 模型id
     * @author 艺品网络  <twothink.cn>
     */
    public function del($id){
        //获取表名
        $model = \think\Db::name($this->name)->field('name,extend')->find($id);
        if($model['extend'] == 0){
            $table_name = config('database.prefix').strtolower($model['name']);
        }else{
        	$model_jc = \think\Db::name($this->name)->field('name')->find($model['extend']);
            $table_name = config('database.prefix').$model_jc['name'].'_'.strtolower($model['name']);
        }
        //删除属性数据
        db('Attribute')->where(array('model_id'=>$id))->delete();
        //删除模型数据
        $this->where(['id'=>$id])->delete();
        //检查数据表是否存在
        $sql = <<<sql
                SHOW TABLES LIKE '{$table_name}';
sql;
        $res = db()->query($sql);
        if(!count($res))
        	return true;
        //删除该表
        $sql = <<<sql
                DROP TABLE {$table_name};
sql;
        $res = db()->execute($sql);
        return $res !== false;
    }
}
