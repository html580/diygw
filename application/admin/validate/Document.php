<?php
 
namespace app\admin\validate;
use app\common\validate\Base;
/**
*  文档验证模型
*/
class Document extends Base{
    // 验证规则
    protected $rule = [
        ['name', '/^[a-zA-Z]\w{0,39}$/|checkName', '文档标识不合法|标识已经存在'],
        ['title', 'require|length:1,80', '标题不能为空|标题长度不能超过80个字符'],
        ['level', '/^[\d]+$/', '优先级只能填正整数'],
        ['description', 'length:1,140', '简介长度不能超过140个字符'],
    	['category_id','require|check_category','分类不能为空|该分类不允许发布内容'],
    	['model_id','check_category_model','该分类没有绑定当前模型']
    ];
    protected $scene = [
        'auto'     => 'name,title,level,description,category_id',//写入时验证
        'update'   => 'name,title,level,description,category_id',//更新时验证
        'insert'   => 'name,title,level,description,category_id',//写入时验证
    ];
    /**
     * 验证分类是否允许发布内容
     * @param  integer $id 分类ID
     * @return boolean     true-允许发布内容，false-不允许发布内容
     */
    protected function check_category($id,$rlue='',$data){
    	if (is_array($id)) {
    		$id['type']	=	!empty($id['type'])?$id['type']:2;
    		$type = get_category($id['category_id'], 'type');
    		$type = explode(",", $type);
    		return in_array($id['type'], $type);
    	} else {
    		$publish = get_category($id, 'allow_publish');
    		return $publish ? true : false;
    	}
    } 
    /**
     * 检测分类是否绑定了指定模型
     * @param  array $info 模型ID和分类ID数组
     * @return boolean     true-绑定了模型，false-未绑定模型
     */
    protected function check_category_model($id,$rlue='',$info){
    	$cate   =   get_category($info['category_id']);
    	$array  =   explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);
    	return in_array($info['model_id'], $array);
    }
    /**
     * 检查标识是否已存在(只需在同一根节点下不重复)
     * @param string $name
     * @return true无重复，false已存在 
     */
    protected function checkName($value,$rlue='',$data){ 
    	$name        = $data['name'];
    	$category_id = $data['category_id'];
    	$id          = $data['id']?$data['id']:0;
    
    	$map = array('name' => $name, 'id' => array('neq', $id), 'status' => array('neq', -1));
 
    	$category = get_category($category_id);
    	if ($category['pid'] == 0) {
    		$map['category_id'] = $category_id;
    	} else {
    		$parent             = get_parent_category($category['id']);
    		$root               = array_shift($parent);
    		$map['category_id'] = array('in', model("Category")->getChildrenId($root['id']));
    	}
    
    	$res = \think\Db::name('Document')->where($map)->value('id');
    	if ($res) {
    		return false;
    	}
    	return true;
    }

}