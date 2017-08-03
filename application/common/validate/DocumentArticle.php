<?php
 
namespace app\common\validate;
/**
*  模型属性验证
*/
class DocumentArticle extends Base {
    // 验证规则
    protected $rule = [
        ['content', 'getContent','内容不能为空']
    ];
    
    public $scene = array(
        'auto'     => 'content',//写入验证
        'update'   => 'content',//更新验证
        'insert'   => 'content',//新增验证
    );


    /**
     * 获取文章的详细内容
     * @return boolean
     * @author 艺品网络  <twothink.cn>
     */
     function getContent($value,$rlue,$data){
    	$type = $data['type'];
    	$content = $data['content'];
    	if($type > 1){//主题和段落必须有内容
    		if(empty($content)){
    			return false;
    		}
    	}return true;
    } 

}