<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +---------------------------------------------------------------------- 
namespace app\admin\controller; 
use app\admin\model\AuthGroup; 

/**
 * 独立模型控制器
 * @author 艺品网络  <twothink.cn>
 */
class Independent extends Admin { 

    /**
     * 内容表页 
     * @param integer $model_id 模型id 
     */
    public function index($model_id = null){ 
        if(empty($model_id))
        	$this->error('模型id不能为空'); 
        // 获取基础模型信息
        $model = \think\Db::name('Model')->getById($model_id); 
        $this->assign('model', $model); 
        //解析列表规则
        $fields =	array();
        $grids  =	preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }   
        // 过滤重复字段信息
        $fields =   array_unique($fields);    
        // 列表查询
        $list   =   $this->getDocumentList($model_id,$fields);
         
        // 列表显示处理
        $list   =   $this->parseDocumentList($list,$model_id); 
        $this->assign('model_id',$model_id); 
        $this->assign('list',   $list);
        $this->assign('list_grids', $grids);
        $this->assign('model_list', $model);
        
        $this->assign('meta_title','内容管理');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        return $this->fetch();
    }

    /**
     * 默认文档列表方法 
     * @param integer $model_id 模型id 
     * @param mixed $field 字段列表 
     */
    protected function getDocumentList($model_id=null,$field=true){
        /* 查询条件初始化 */
    	$data=input();
        $map = array();
        if(isset($data['title'])){
            $map['title']  = array('like', '%'.(string)input('title').'%');
        }
        if(isset($data['status'])){
            $map['status'] = input('status');
            $status = $map['status'];
        }else{
            $status = null;
            $map['status'] = array('in', '0,1,2');
        }
        if ( isset($data['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(input('time-start')));
        }
        if ( isset($data['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(input('time-end')));
        }
        if ( isset($data['nickname']) ) {
            $map['uid'] = \think\Db::name('Member')->where(array('nickname'=>input('nickname')))->value('uid');
        } 
         
        // 获取基础模型信息
        $model = \think\Db::name('Model')->getById($model_id); 
        $this->assign('model_title',$model['title']);
        // 构建列表数据
        $Document = \think\Db::name($model['name']); 
        if(!is_null($position)){
            $map['position'] = $position;
        }
        if(!is_null($group_id)){
        	$map['group_id']	=	$group_id;
        } 
        $listRows = config('list_rows') > 0 ? config('list_rows') : 10;   
		// 分页查询
        $list = $Document->where($map)->order('id desc')->field($field)->paginate($listRows);
      
        $total        =   $Document->where($map)->count();
        // 获取分页显示
        $page = $list->render(); 

        $this->assign('status', $status); 
        $this->assign('_page', $page);
        $this->assign('_total',$total);
        $this->meta_title = '文档列表';
        
        $list = $list->toArray(); 
        return $list['data'];
    }

    /**
     * 设置一条或者多条数据的状态 
     */
    public function setStatus($modelname='Document'){ 
        return parent::setStatus($modelname);
    }

    /**
     * 文档新增页面初始化
     * @author 艺品网络  <twothink.cn>
     */
    public function add(){ 
        $model_id   =   input('model_id',0);  
        empty($model_id) && $this->error('该分类未绑定模型！'); 
        // 获取当前的模型信息
        $model    =   get_document_model($model_id); 
        //处理结果 
        $info['model_id']       =   $model_id;  

        //获取表单字段排序
        $fields = get_model_attribute($model['id']); 
        $this->assign('info',       $info);
        $this->assign('fields',     $fields); 
        $this->assign('model',      $model);
        $this->assign('meta_title','新增'.$model['title']);
        return $this->fetch();
    }

    /**
     * 文档编辑页面初始化
     * @author 艺品网络  <twothink.cn>
     */
    public function edit(){ 
        $model_id = input('param.model',0); 
        $id = input('param.id',0);
        empty($model_id) && $this->error('该分类未绑定模型！'); 
        // 获取当前的模型信息 
    	$Document=logic($model_id,'Independent'); 
        $data = $Document->detail($id);   
        if(!$data){
            $this->error($Document->getError());
        }   
        // 获取当前的模型信息
        $model    =   get_document_model($model_id);
 
        $this->assign('data', $data);
        $this->assign('model_id', $model_id);
        $this->assign('model',      $model);
 
        //获取表单字段排序
        $fields = get_model_attribute($model_id);
        $this->assign('fields',     $fields); 

        $this->assign('meta_title', '编辑文档');
        return $this->fetch();
    }

    /**
     * 更新一条数据
     * @author 艺品网络  <twothink.cn>
     */
    public function update(){
    	$model_id = input('param.model_id',0);
    	$document=logic($model_id,'Independent');
        $res = $document->updates(); 
        if(!$res){
            $this->error($document->getError());
        }else{
            $this->success(isset($res['id'])?'更新成功':'新增成功', Cookie('__forward__'));
        }
    } 

    /**
     * 待审核列表
     */
    public function examine(){ 

        $map['status']  =   2;
        if ( !IS_ROOT ) {
            $cate_auth  =   AuthGroup::getAuthCategories(UID);
            if($cate_auth){
                $map['category_id']    =   array('IN',$cate_auth);
            }else{
                $map['category_id']    =   -1;
            }
        }
        $list = $this->lists(db('Document'),$map,'update_time desc');
        //处理列表数据
        if(is_array($list)){
            foreach ($list as $k=>&$v){
                $v['username']      =   get_nickname($v['uid']);
            }
        }

        $this->assign('list', $list);
        $this->assign('meta_title','待审核');
        return $this->fetch();
    }

    /**
     * 回收站列表
     * @author 艺品网络  <twothink.cn>
     */
    public function recycle(){ 

        $map['status']  =   -1;
        if ( !IS_ROOT ) {
            $cate_auth  =   AuthGroup::getAuthCategories(UID);
            if($cate_auth){
                $map['category_id']    =   array('IN',$cate_auth);
            }else{
                $map['category_id']    =   -1;
            }
        }
        $list = $this->lists(\think\Db::name('Document'),$map,'update_time desc');

        //处理列表数据
        if(is_array($list)){
            foreach ($list as $k=>&$v){
                $v['username']      =   get_nickname($v['uid']);
            }
        } 
        $this->assign('list', $list);
        $this->assign('meta_title','回收站');
        return $this->fetch();
    }
 
    /**
     * 还原被删除的数据
     * @author 艺品网络  <twothink.cn>
     */
    public function permit(){
        /*参数过滤*/
        $data = \think\Request::instance()->post();
        $ids = $data['ids'];
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        } 
        /*拼接参数并修改状态*/
        $Model  =   'Document';
        $map    =   array();
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        } 
        $this->restore($Model,$map);
    }

    /**
     * 清空回收站
     * @author 艺品网络  <twothink.cn>
     */
    public function clear(){
        $res = model('Document')->remove();
        if($res !== false){
            $this->success('清空回收站成功！');
        }else{
            $this->error('清空回收站失败！');
        }
    } 
}