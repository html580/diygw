<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com>
// +----------------------------------------------------------------------
namespace think;


use think\View;
use think\Db;


/**
 * @title插件基类
 * @author diygw <diygwcom@foxmail.com>
 */
abstract class Addons{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;
    /**
     * $info = array(
     *  'name'=>'Editor',
     *  'title'=>'编辑器',
     *  'description'=>'用于增强整站长文本的输入和显示',
     *  'status'=>1,
     *  'author'=>'thinkphp',
     *  'version'=>'0.1'
     *  )
     */
    public $info                =   [];
    public $addon_path          =   '';
    public $config_file         =   '';
    public $custom_config       =   '';
    public $admin_list          =   [];
    public $custom_adminlist    =   '';
    public $access_url          =   [];

    public function __construct(){
        // 获取当前插件目录
        $this->addon_path = DIYGW_ADDON_PATH . $this->getName() . DS;
        // 读取当前插件配置信息
        if (is_file($this->addon_path . 'config.php')) {
            $this->config_file = $this->addon_path . 'config.php';
        }
        // 初始化视图模型
        $config['view_path']=$this->addon_path;
        // 初始化视图模型

        $config = array_merge(config('template.'), $config);

        $this->view = (new \think\View())->engine($config);
        //加载插件函数文件
        if (file_exists($this->addon_path.'common.php')) {
            include_once $this->addon_path.'common.php';
        }
        // 控制器初始化
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
        /*if(!empty(facade\Config::get('template'))){
            $config = array_merge(facade\Config::get('template'), $config);
        }

        $this->view = new View($config, facade\Config::get('view_replace_str'));
        //加载插件函数文件
        if (file_exists($this->addon_path.'common.php')) {
            include_once $this->addon_path.'common.php';
        }
        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }*/
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return void
     */
    final protected function assign($name, $value = '')
    {
        $this->view->assign($name, $value);
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (!is_file($template)) {
            $template = '/' . $template;
        }
        // 关闭模板布局
        $this->view->engine->layout(false);

        echo $this->view->fetch($template, $vars, $replace, $config);
    }
    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     */
    public function display($content, $vars = [], $replace = [], $config = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);

        echo $this->view->display($content, $vars, $replace, $config);
    }
    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array $vars 模板输出变量
     * @return mixed
     */
    public function show($content, $vars = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);

        echo $this->view->fetch($content, $vars, [], [], true);
    }
    /**
     * @title 获取当前模块名
     * @return string
     */
    final public function getName(){
        $data = explode('\\', get_class($this));
        return strtolower(array_pop($data));
    }
    /**
     * @title 获取当前模块名
     * @return string
     */
    final public function checkInfo(){
        $info_check_keys = ['name','title','description','status','author','version'];
        foreach ($info_check_keys as $value) {
            if(!array_key_exists($value, $this->info))
                return false;
        }
        return true;
    }

    /**
     * @title 获取插件的配置数组
     * @param string $name 可选模块名
     * @return array|mixed|null
     */
    final public function getConfig($name=''){
        static $_config = [];
        if (empty($name)) {
            $name = $this->getName();
        }
        if (isset($_config[$name])) {
            return $_config[$name];
        }
        $map['name'] = $name;
        $map['status'] = 1;
        $config  =   Db::name('Addons')->where($map)->value('config');
        if($config){
            $config   =   json_decode($config, true);
        }else{
            if (is_file($this->config_file)) {
                $temp_arr = include $this->config_file;
                foreach ($temp_arr as $key => $value) {
                    if ($value['type'] == 'group') {
                        foreach ($value['options'] as $gkey => $gvalue) {
                            foreach ($gvalue['options'] as $ikey => $ivalue) {
                                $config[$ikey] = $ivalue['value'];
                            }
                        }
                    } else {
                        $config[$key] = $temp_arr[$key]['value'];
                    }
                }
                unset($temp_arr);
            }
        }
        $_config[$name] = $config;
        return $config;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();

    public $error;//错误信息

    /**
     * 获取插件所需的钩子是否存在，没有则新增
     * @param string $str 钩子名称
     * @param string $addons 插件名称
     * @param string $msg 插件描述
     * @param int 类型
     * @Author: DIYGW.COM  280160522@qq.com <www.twothink.cn>
     */
    public function existHook($str, $addons, $msg = '',$type = 1) {
        $hook_mod = Db::name( 'Hooks' );
        $where ['name'] = $str;
        $gethook = $hook_mod->where ( $where )->find ();
        if (! $gethook || empty ( $gethook ) || ! is_array ( $gethook )) {
            $data ['name'] = $str;
            $data ['description'] = $msg;
            $data ['type'] = $type;
            $data ['update_time'] = time();
            $data ['addons'] = $addons;

            $rule = [
                ['name','require|unique:hooks','钩子名称必须|钩子已存在'],
                ['description','require','钩子描述必须']
            ];
            $validate = new Validate($rule);
            if (!$validate->check($data)) {
                $this->error( $validate->getError() );
                return false;
            }

            if($hook_mod->insert ( $data ))
                cache ( 'hooks', null );
        }
        return true;
    }
    /*
     * @title 查询单条钩子信息
     * @param array $name 钩子名称
     * @Author: DIYGW.COM  280160522@qq.com <www.twothink.cn>
     */
    public function findHooks($name) {
        return $data = Db::name('Hooks')->getByName($name);
    }
    /*
     * @title 删除钩子
     * @param array $name 钩子名称
     * @Author: DIYGW.COM  280160522@qq.com <www.twothink.cn>
     */
    public function delHooks($name) {
        $gd_name = ['pageHeader','pageFooter','documentEditForm','documentDetailAfter','documentDetailBefore','documentSaveComplete','documentEditFormContent','adminArticleEdit','topicComment','app_begin'];
        if(in_array($name,$gd_name)){
            $this->error ( '系统钩子不可删除' );
            return false;
        }
        $obj = Db::name('Hooks');
        if(!$data = $obj->getByName($name)){
            return true;
        }
        $count = count(explode(',',$data['addons']));
        if($count <= 1){
            $obj->delete($data['id']);
        }
        return true;
    }
    protected function error($msg){
        $this->error = $msg;
    }
    public function getError()
    {
        return $this->error;
    }
}