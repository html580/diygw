<?php
 
namespace app\admin\validate;
use think\Validate;
/**
*  模型属性验证
*/
class Attribute extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|/^[a-zA-Z][\w_]{1,29}$/|checkName', '字段名必须|字段名不合法|字段名已存在'],
        ['field', 'require|length:1,100', '字段定义必须|注释长度不能超过100个字符'],
        ['title', 'length:1,100', '注释长度不能超过100个字符'],
        ['remark', 'length:1,100', '备注不能超过100个字符'],
    	['model_id','require','未选择操作的模型']
    ];  
    
    protected $scene = array(
        'auto'     => 'name,field,title,remark,model_id',//写入时验证 
    	'update'     => 'field,title,remark,model_id',//编辑验证
    );  
    /**
     * 检查标识是否已存在(只需在同一根节点下不重复)
     * @param string $name
     * @return true无重复，false已存在 
     */
    protected function checkName($value,$rlue='',$data){  
    	$name = $data['name'];
    	$model_id = $data['model_id'];
    	$id = $data[''];
    	$map = array('name'=>$name, 'model_id'=>$model_id);
    	if(!empty($id)){
    		$map['id'] = array('neq', $id);
    	}
    	$res = \think\Db::name('Attribute')->where($map)->find();
    	return empty($res);
    }

}