<?php 
/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */ 
/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态 
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = model('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数 
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = model('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url 
 */
function get_nav_url($url){
    switch ($url) {
        case strripos($url,".html")===strlen($url)-5;
        break;
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = url($url);
            break;
    }
    return $url;
}
/**
 * 获取导航信息并缓存导航
 * @param  integer $id    导航ID
 * @param  string  $field 要获取的字段名
 * @return string         导航信息
 */
function get_channel($id = null, $field = null){
    static $list; 
    /* 读取缓存数据 */
    if(empty($list)){
        $list = cache('sys_channel_list');
    }
    if(empty($list)){
    	$data = db('Channel')->select();
    	foreach ($data as $key => $value) {	 
            $list[$value['id']] = $value;
        }
    	cache('sys_channel_list',$list);
    } 
    if(empty($id)){
    	return $list;
    }else{
    	if(isset($list[$id])){
    		return is_null($field) ? $list[$id] : $list[$id][$field];
    	}
    	return false;
    } 
}
 
/**
 * 获取文档列表
 * @param integer $cate_id 分类id
 * @param integer $where   查询条件
 * @param integer $model_id 模型id 
 * @param string  $fields   显示字段
 * @param int     $listRows 查询列数
 * @param integer $sor      排序 
 */
function get_document_list($where = null,$sor = 'id desc',$listRows = null,$cate_id = null, $model_id = null, $fields=true){
	 //获取模型name
	if(!empty($model_id)){
		$model  = get_document_model($model_id); 
		if($model['extend'] != 0){
			$model_name2 = get_document_model($model['extend'], 'name');
			$model_name  = $model_name2.'_'.$model['name'];
			if(!empty($where)){
				$fields1 = db()->getTableFields(array('table'=>config('database.prefix').$model_name));
				foreach ($fields1 as $key=>$value){ 
					$fields1_new[$value]=$value;
				}   
			    $fields2 = db()->getTableFields(array('table'=>config('database.prefix').$model_name2));
		        foreach ($fields2 as $key=>$value){
			    	$fields2_new[$value]=$value;
			    }
			    foreach ($where as $key=>$value){
			    	if(isset($fields2_new[$key])){
			    		$new_where['a.'.$key]=$value;
			    	}elseif(isset($fields1_new[$key])){
			    		$new_where['b.'.$key]=$value;
			    	}
			    	 
			    }
			    $where=$new_where;
			}
			$model    = db($model_name2)->alias('a')->join ( config('database.prefix').$model_name.' b','a.id=b.id' );
		}
			  
	}else{
		$model = db('Document');
	}
	if(empty($where))
		$where['status'] = 1;
	if(empty($listRows) ){ 
		$listRows = config('list_rows') > 0 ? config('list_rows') : 10;
	}
	return $list = $model->where($where)->order($sor)->field($fields)->limit($listRows)->select();  
}
