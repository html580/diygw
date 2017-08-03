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
 * 属性模型 
 */

class Attribute extends Model {  
    /* 操作的表名 */
    protected $table_name = null;

    /**
     * 新增或更新一个属性
     * @return boolean fasle 失败 ， int  成功 返回完整的数据 
     */
    public function updates($data = null, $create = true){
        /* 获取数据对象 */
        $data = empty($data) ? \think\Request::instance()->post() : $data;  
        /* 获取数据对象 */ 
        $validate = validate('Attribute');
         
        /* 添加或新增属性 */
        if(empty($data['id'])){ //新增属性  
        	if(!$validate->check($data)){
        		$this->error=$validate->getError();
        		return false;
        	} 
            $id = $this->insert($data); 
            if(!$id){ 
                return false;
            } 
        	if($create){ 
                //新增表字段
                $res = $this->addField($data);
                if(!$res){ 
                    //删除新增数据
                    $this->delete($id);
                    return false;
                }
            }

        } else { //更新数据 
        	if(!$validate->scene('update')->check($data)){
        		$this->error=$validate->getError();
        		return false;
        	}
        	if($create){
            //更新表字段
                $res = $this->updateField($data); 
                if(!$res){ 
                    return false;
                } 
        	}

            $status = $this->update($data);
            if(false === $status){ 
                return false;
            }
        } 
        //记录行为
        action_log('update_attribute', 'attribute', isset($data['id']) ? $data['id'] : $id, UID);

        //内容添加或更新完成
        return $data;

    }
 

    /**
     * 检查当前表是否存在
     * @param intger $model_id 模型id
     * @return intger 是否存在 
     */
    protected function checkTableExist($model_id){
        $Model = \think\Db::name('Model');
        //当前操作的表
        $model = $Model->where(array('id'=>$model_id))->field('name,extend')->find();

        if($model['extend'] == 0){	//独立模型表名
            $table_name = $this->table_name = config('database.prefix').strtolower($model['name']);
        }else{						//继承模型表名
            $extend_model = $Model->where(array('id'=>$model['extend']))->field('name,extend')->find();
            $table_name = $this->table_name = config('database.prefix').strtolower($extend_model['name']).'_'.strtolower($model['name']);
        }
        $sql = <<<sql
                SHOW TABLES LIKE '{$table_name}';
sql;
        $res = \think\Db::connect()->query($sql); 
        return count($res);
    } 

    /**
     * 新建表字段
     * @param array $field 需要新建的字段属性
     * @return boolean true 成功 ， false 失败 
     */
    protected function addField($field){
        //检查表是否存在
        $table_exist = $this->checkTableExist($field['model_id']); 
        
        //获取默认值
        if($field['value'] === ''){
            $default = '';
        }elseif (is_numeric($field['value'])){
            $default = ' DEFAULT '.$field['value'];
        }elseif (is_string($field['value'])){
            $default = ' DEFAULT \''.$field['value'].'\'';
        }else {
            $default = '';
        }

        if($table_exist){
            $sql = <<<sql
                ALTER TABLE `{$this->table_name}`
ADD COLUMN `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}';
sql;
        }else{
            //新建表时是否默认新增“id主键”字段
            $model_info = \think\Db::name('Model')->field('engine_type,need_pk')->getById($field['model_id']);
            if($model_info['need_pk']){
                $sql = <<<sql
                CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
                `id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键' ,
                `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}' ,
                PRIMARY KEY (`id`)
                )
                ENGINE={$model_info['engine_type']}
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                CHECKSUM=0
                ROW_FORMAT=DYNAMIC
                DELAY_KEY_WRITE=0
                ;
sql;
            }else{
                $sql = <<<sql
                CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
                `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}'
                )
                ENGINE={$model_info['engine_type']}
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                CHECKSUM=0
                ROW_FORMAT=DYNAMIC
                DELAY_KEY_WRITE=0
                ;
sql;
            }

        }
        $res = \think\Db::connect()->execute($sql);
        return $res !== false;
    }

    /**
     * 更新表字段
     * @param array $field 需要更新的字段属性
     * @return boolean true 成功 ， false 失败 
     */
    protected function updateField($field){
        //检查表是否存在
        $table_exist = $this->checkTableExist($field['model_id']);
        if(!$table_exist){
        	$this->error = '表不存在';
        	return false;
        }
       
         
        //获取原字段名
        $last_field = $this->getFieldById($field['id'], 'name');

        //获取默认值
        $default = $field['value']!='' ? ' DEFAULT '.$field['value'] : ''; 
        $sql = <<<sql
            ALTER TABLE `{$this->table_name}`
CHANGE COLUMN `{$last_field}` `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}' ;
sql;
          
        $res = db()->execute($sql);
        return $res !== false;
    }

    /**
     * 删除一个字段
     * @param array $field 需要删除的字段属性
     * @return boolean true 成功 ， false 失败 
     */
    public function deleteField($field){
    	$field = $field->toArray();
        //检查表是否存在
        $table_exist = $this->checkTableExist($field['model_id']); 
        if(!$table_exist){
        	return true;
        } 
        //如存在id字段，则加入该条件
        $fields = \think\Db::connect()->getTableFields(array('table'=>$this->table_name));
        foreach ($fields as $key => $value) {
        	$field_new[$value] = $value;
        }  
        if(!isset($field_new[$field['name']])){
        	return true;
        } 
        $sql = <<<sql
            ALTER TABLE `{$this->table_name}`
DROP COLUMN `{$field['name']}`;
sql;
        $res = \think\Db::connect()->execute($sql);
        return $res !== false;
    }

}
