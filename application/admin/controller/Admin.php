<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络 <82550565@qq.com> <http://www.twothink.cn>
// | Update: 邓志锋 <280160522@qq.com> <http://www.diygw.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Controller;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
/**
 * 后台首页控制器
 */
class Admin extends Controller {
	public function __construct(){
		/* 读取数据库中的配置 */
		$config = cache('db_config_data');
		if(!$config){
			$config =   api('Config/lists');
			$config ['var_module'] = request()->module();
			$config ['var_controller'] = request()->controller();
			$config ['var_action'] = request()->action();$config['admin_view_path']='default';
			$config ['template']['view_path'] = APP_PATH.'admin/view/'.$config['admin_view_path'].'/'; //模板主题
			$config['dispatch_error_tmpl' ]    =  APP_PATH .'admin'. DS .'view' . DS .$config['admin_view_path'].DS. 'public' . DS . 'error.html'; // 默认错误跳转对应的模板文件
			$config['dispatch_success_tmpl' ]  =  APP_PATH .'admin'. DS .'view' . DS .$config['admin_view_path'].DS. 'public' . DS . 'success.html'; // 默认成功跳转对应的模板文件
			cache('db_config_data', $config);
		}
		config($config);//添加配置
		parent::__construct();
	}

    /**
     * 后台控制器初始化
     */
    public function _initialize(){
    	// SESSION_ID设置的提交变量,解决flash上传跨域
    	$session_id=input(config('session.var_session_id'));
    	if($session_id){
    		session_id($session_id);
    	}
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Publics/login');
        }
        // 是否是超级管理员
        define('IS_ROOT',   is_administrator());
        if(!IS_ROOT && config('admin_allow_ip')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',config('admin_allow_ip')))){
                $this->error('403:禁止访问');
            }
        }

        // 检测系统权限
        if(!IS_ROOT){
            $access =   $this->accessControl();
            if ( false === $access ) {
                $this->error('403:禁止访问');
            }elseif(null === $access ){
                //检测访问权限
                $rule  = strtolower($this->request->module().'/'.$this->request->controller().'/'.$this->request->action());
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!');
                }else{
                    // 检测分类及内容有关的各项动态权限
                    $dynamic    =   $this->checkDynamic();
                    if( false === $dynamic ){
                        $this->error('未授权访问!');
                    }
                }
            }
        }
        $this->assign('__MENU__', $this->getMenus());
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type=AuthRule::rule_url, $mode='url'){
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \com\Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *
     * @author 艺品网络  <twothink.cn>
     */
    protected function checkDynamic(){}


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 艺品网络  <twothink.cn>
     */
    final protected function accessControl(){
        $allow = config('allow_visit');
        $deny  = config('deny_visit');
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false; //非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null; //需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author 艺品网络 <twothink.cn>
     */
    final protected function editRow ( $model ,$data, $where , $msg=false ){
        $id=input('id/a');
        if(!empty($id)){
	        $id    = array_unique($id);
	        $id    = is_array($id) ? implode(',',$id) : $id;
	        //如存在id字段，则加入该条件
	        $fields = db()->getTableFields(array('table'=>config('database.prefix').$model));

	        if(in_array('id',$fields) && !empty($id)){
	            $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
	        }
        }

        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>var_export(Request()->isAjax(), true)) , (array)$msg );

        if( db($model)->where($where)->update($data)!==false ) {
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的 where()方法的参数
     * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author 艺品网络 <twothink.cn>
     */
    protected function forbid ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
        $data    =  array('status' => 0);
        $this->editRow( $model , $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author 艺品网络 <twothink.cn>
     */
    protected function resume (  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
        $data    =  array('status' => 1);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author 艺品网络  <twothink.cn>
     */
    protected function restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
        $data    = array('status' => 1);
        $where   = array_merge(array('status' => -1),$where);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author 艺品网络 <twothink.cn>
     */
    protected function delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
        $data['status']  =   -1;
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     * $Model 模型名称
     */
    public function setStatus($Model=false){
        if(empty($Model)){
        	 $Model=request()->controller();
        }
        $ids    =   input('ids/a');
        $status =   input('status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 艺品网络  <twothink.cn>
     */
    final public function getMenus(){
    	$model_name = $this->request->module();
    	$controller      = $this->request->controller();
    	$action_name = $this->request->action();
    	session('admin_menu_list.'.$controller,null);
        $menus  =   session('admin_menu_list.'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
//            $where['module']  =   $model_name;
            if(!config('develop_mode')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   \think\Db::name('Menu')->where($where)->order('sort asc')->field('id,title,url')->select();
            $menus['child'] =   array(); //设置子节点
            foreach ($menus['main'] as $key => $item) {
                // 判断主菜单权限
                if ( !IS_ROOT && !$this->checkRule(strtolower($model_name.'/'.$item['url']),AuthRule::rule_main,null) ) {
                    unset($menus['main'][$key]);
                    continue;//继续循环
                }
                if(strtolower($controller.'/'.$action_name)  == strtolower($item['url'])){
                    $menus['main'][$key]['class']='current';
                }
            }

            // 查找当前子菜单
            $pid = \think\Db::name('Menu')->where("pid !=0 AND url like '%{$controller}/".$action_name."%'")->value('pid');
            $this->assign('__PID__', $pid);
            if($pid){
                // 查找当前主菜单
                $nav =  \think\Db::name('Menu')->find($pid);
                if($nav['pid']){
                    $nav    =   \think\Db::name('Menu')->find($nav['pid']);
                }
                foreach ($menus['main'] as $key => $item) {
                    // 获取当前主菜单的子菜单项
                    if($item['id'] == $nav['id']){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = \think\Db::name('Menu')->where(array('group'=>array('neq',''),'pid' =>$item['id']))->distinct(true)->column("group");

                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!config('develop_mode')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = \think\Db::name('Menu')->where($where)->column('id,url');

                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,$model_name)!==0 ){
                                    $rule = $model_name.'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, AuthRule::rule_url,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid']     =   $item['id'];
                            $map['hide']    =   0;
                            if(!config('develop_mode')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = \think\Db::name('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                    }
                }
            }
            session('admin_menu_list.'.$controller,$menus);
        }
        return $menus;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 艺品网络  <twothink.cn>
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        $module_name = $this->request->module();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            $list = \think\Db::name('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],$module_name)!==0 ){
                    $list[$key]['url'] = $module_name.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = \think\Db::name('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],$module_name)!==0 ){
                    $nodes[$key]['url'] = $module_name.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }


    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 艺品网络  <twothink.cn>
     *
     * @return array|false
     * 返回数据集
     */
    protected  function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =  (array)input('request.');
        if(is_string($model)){
            $model  =   \think\Db::name($model);
        }
        $pk         =   $model->getPk();

        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }

        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = config('list_rows') > 0 ? config('list_rows') : 10;
        }
       // 分页查询
        $list = $model->where($options['where'])->order($order)->field($field)->paginate($listRows);
        // 获取分页显示
        $page = $list->render();
        // 模板变量赋值
        $this->assign('_page', $page);
        $this->assign('_total',$total);
        if($list && !is_array($list)){
        	$list=$list->toArray();
        }
        return $list['data'];//TODO 可以返回带分页的$list
    }

    /**
     * 处理文档列表显示
     * @param array $list 列表数据
     * @param integer $model_id 模型id
     */
    protected function parseDocumentList($list,$model_id=null){
        $model_id = $model_id ? $model_id : 1;
        $attrList = get_model_attribute($model_id,false,'id,name,type,extra');
        // 对列表数据进行显示处理
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
                        }elseif('date'==$type){ // 日期型
                            $data[$key]    =   date('Y-m-d',$val);
                        }elseif('datetime' == $type){ // 时间型
                            $data[$key]    =   date('Y-m-d H:i',$val);
                        }
                    }
                }
                $data['model_id'] = $model_id;
                $list[$k]   =   $data;
            }
        }
        return $list;
    }
    /*
     * 一键清空缓存
     */
    public function delcache() {
    	$path=ROOT_PATH.'/runtime';
		$ret = '删除成功';
		$files = $this->getFiles($path);
		if (!is_array($files)) {
			$ret = $files;
		} elseif (empty($files)) {
			$ret = '删除失败,目录下没有文件或目录';
		} else {
			foreach ($files as $item => $file) {
				if (is_dir($file)) {
					rmdir($file);
				} elseif (is_file($file)) {
					unlink($file);
				}
			}
		}
		if($ret == '删除成功'){
			$this->success($ret);
		}else{
			$this->error($ret);
		}
    }
    //获取目录下的所有文件和目录
	//使用$path = 'a/x/s/';
	public function getFiles($path)
	{
		if (is_dir($path)) {
			$path = dirname($path) . '/' . basename($path) . '/';
			$file = dir($path);
			while (false !== ($entry = $file->read())) {
				if ($entry !== '.' && $entry !== '..') {
					$cur = $path . $entry;
					if (is_dir($cur)) {
						$subPath = $cur . '/';
						$this->getFiles($subPath);
					}
					$this->files[] = $cur;
				}
			}
			$file->close();
			return $this->files;
		} else {
			$this->error = $path . 'not a dir';
			return $this->error;
		}
	}
}
