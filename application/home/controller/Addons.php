<?php

namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Config;
use think\Loader;
use think\Cache;


/**
 * 插件执行默认控制器
 * Class Addons
 * @package think\addons
 */
class Addons extends Controller
{
	public function _initialize(){
		/* 读取数据库中的配置 */
		$config = Cache::get('db_config_data');
		if(!$config){
			$config = api('Config/lists');
            Cache::set('db_config_data',$config);
		}
		config($config); //添加配置
	}
    /**
     * 插件执行
     */
    public function execute($_addons = null, $_controller = null, $_action = null)
    {
        if (!empty($_addons) && !empty($_controller) && !empty($_action)) {
            // 获取类的命名空间
            $class = get_addon_class($_addons, 'controller', $_controller);

            if(class_exists($class)) {
                $model = new $class();
                if ($model === false) {
                    $this->error(lang('addon init fail'));
                }

                // 调用操作
                return  \think\App::invokeMethod([$class, $_action]);
            }else{
                $this->error(lang('控制器不存在'.$class));
            }
        }
        $this->error(lang('没有指定插件名称，控制器或操作！'));
    }

    // 当前插件操作
    protected $addon = null;
    protected $controller = null;
    protected $action = null;
    // 当前template
    protected $template;
    // 模板配置信息
    protected $config = [
    		'type' => 'Think',
    		'view_path' => '',
    		'view_suffix' => 'html',
    		'strip_space' => true,
    		'view_depr' => DS,
    		'tpl_begin' => '{',
    		'tpl_end' => '}',
    		'taglib_begin' => '{',
    		'taglib_end' => '}',
    ];

    /**
     * 架构函数
     * @param Request $request Request对象
     * @access public
    */
    public function __construct(Request $request = null)
    {
    	// 生成request对象
    	$this->request = is_null($request) ? Request::instance() : $request;
    	// 初始化配置信息
    	$this->config = Config::get('template') ?: $this->config;
    	// 处理路由参数
    	$route = $this->request->param();
    	// 格式化路由的插件位置
    	$this->action = $route['_action'];
    	$this->controller = $route['_controller'];
    	$this->addon = $route['_addons'];
    	// 生成view_path
    	$view_path = isset($this->config['view_path']) ?: 'view';
    	// 重置配置
    	Config::set('template.view_path', TWOTHINK_ADDON_PATH . $this->addon . DS . $view_path . DS);

    	// 获取当前插件目录
        $this->addon_path = TWOTHINK_ADDON_PATH . $this->addon . DS;
        //加载插件函数文件
        if (file_exists($this->addon_path.'common.php')) {
            include_once $this->addon_path.'common.php';
        }
    	parent::__construct($request);
    }

    /**
     * 加载模板输出
     * @access protected
     * @param string $template 模板文件名
     * @param array $vars 模板输出变量
     * @param array $replace 模板替换
     * @param array $config 模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
    	$controller = Loader::parseName($this->controller);
    	if ('think' == strtolower($this->config['type']) && $controller && 0 !== strpos($template, '/')) {
    		$depr = $this->config['view_depr'];
    		$template = str_replace(['/', ':'], $depr, $template);
    		if ('' == $template) {
    			// 如果模板文件名为空 按照默认规则定位
    			$template = str_replace('.', DS, $controller) . $depr . $this->action;
    		} elseif (false === strpos($template, $depr)) {
    			$template = str_replace('.', DS, $controller) . $depr . $template;
    		}
    	}
    	return parent::fetch($template, $vars, $replace, $config);
    }
    /*
     * 模型列表数据
     *
     */
    protected function _list($where=[],$fields=true,$tpl=false){
        $this_model_class = $this->this_model_class();

        $model = $this_model_class->get_model();
        $list_grid = $grids  =	preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        $fields_new = array ();
        foreach ( $list_grid as &$value ) {
            // 字段:标题:链接
            $val = explode ( ':', $value );
            // 支持多个字段显示
            $field = explode ( ',', $val [0] );
            $value = array ('field' => $field,'title' => $val [1]);
            if (isset ( $val [2] )) {
                // 链接信息
                $value ['href'] = $val [2];
                // 搜索链接信息中的字段信息
                preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
                    $fields [] = $match [1];
                }, $value ['href'] );
            }
            if (strpos ( $val [1], '|' )) {
                // 显示格式定义
                list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
            }
            foreach ( $field as $val ) {
                $array = explode ( '|', $val );
                $fields_new [] = $array [0];
            }
        }
        if($fields == true && !empty($fields_new)){
            $fields = $fields_new;
        }
        $listRows = Config::get('list_rows') > 0 ? Config::get('list_rows') : 10;
        $list = $this_model_class->where($where)->field($fields)->paginate($listRows);
        $page = $list->render();
        if($list && !is_array($list)){
            $list=$list->toArray();
        }
        $assign['addon'] = $this->addon;
        $assign['controller'] = $this->controller;
        $assign['list_grid'] = $list_grid;
        $assign['list'] = $list['data'];
        $assign['page'] = $page;
        $this->assign('list',$assign);
        $tpl_url =  ROOT_PATH.'application/home/view/'.Config::get('home_view_path').'/addons/_list.html';
        $tpl ? $tpl_url=$tpl : $tpl_url;
        return $this->fetch($tpl_url);
    }
    /*
     * 模型编辑及新增页面(仅用于Twothink创建的模型使用)仅对单个模型表进行操作关联模型请在模型中定义
     * $fields ['fields'=>'id,title','status'=>false]查询的字段 statu字段查询方式 true排除 false查询指定字段
     * $tpl 自定义模版
     * $where 查询条件
     */
    protected function _edit($where=false,$fields=true,$tpl = false){
        //实列化模型
        $this_model_class = $this->this_model_class();

        $model = $this_model_class->get_model();
        //模型字段
        $_fields = $this_model_class->get_fields($fields);
        //数据
        if(!$where){
            $where['id'] = Request::instance()->param('id');
        }
        $data = $this_model_class->where($where)->find();
        $this->assign('data',$data);
        $this->assign ( 'meta_title','编辑');
        $this->assign('model',$model);
        $this->assign('fields',$_fields);
        $tpl_url =  ROOT_PATH.'application/home/view/'.Config::get('home_view_path').'/addons/_edit.html';
        $tpl ? $tpl_url=$tpl : $tpl_url;
        return $this->fetch($tpl_url);
    }
    protected function _add($fields=true,$tpl = false){
        $this_model_class = $this->this_model_class();

        $model = $this_model_class->get_model();
        //模型字段
        $_fields = $this_model_class->get_fields($fields);
        $this->assign ( 'meta_title','新增');
        $this->assign('model',$model);
        $this->assign('fields',$_fields);
        $home_view_path = Config::get('home_view_path');
        $tpl_url =  ROOT_PATH.'application/home/view/'.Config::get('home_view_path').'/addons/_edit.html';
        $tpl ? $tpl_url=$tpl : $tpl_url;
        return $this->fetch($tpl_url);
    }
    protected function _update(){
        $data = Request::instance()->param();

        $this_model_class = $this->this_model_class();
        $res = $this_model_class->updates();
        $res || $this->error($this_model_class->getError());
        $this->success(!empty($data['id'])?'更新成功':'新增成功');
    }

    protected function _del($map=false){
        if(!$map){
            $data = Request::instance()->param();
            $ids = array_unique ( ( array ) $data['ids'] );
            $map['id'] =['in',$ids];
        }
        $this_model_class = $this->this_model_class();
        $res = $this_model_class->del($map);
        $res || $this->error($this_model_class->getError());
        $this->success('删除成功');
    }
    protected function this_model_class(){
        $controller = Loader::parseName($this->controller,1);
        //实列化模型
        $this_model_class = get_addon_model($this->addon,$controller);//当前模型
        if(!class_exists($this_model_class)){
            $this_model_class = 'app\common\model\AddonsBase';
        }
        return new $this_model_class();
    }
}
