<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com>
// +----------------------------------------------------------------------

namespace think\addons;

use think\facade\Env;
use think\facade\Request;
use think\facade\Config;
use think\Loader;
use think\Container;
use think\Db;
/**
 * 插件基类控制器
 * Class Controller
 * @Author: DIY官网  diygwcom@foxmail.com <www.diygw.com>
 */
class Controller extends \think\Controller
{
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
    public function __construct($app = null)
    {
        // 生成request对象
        $this->request = Container::get('request');
        $this->app     = Container::get('app');
        // 初始化配置信息
        $this->config = $this->app['config']->get('template.') ?: $this->config;
        // 是否自动转换控制器和操作名
        $convert = Config::get('url_convert');
        $filter = $convert ? 'strtolower' : '';
        // 处理路由参数
        $this->addon = $this->request->param('addon', '', $filter);
        $this->controller = $this->request->param('controller', 'index', $filter);
        $this->action = $this->request->param('action', 'index', $filter);

        // 生成view_path
        //$view_path = Env::get('addons_path') . $this->addon . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;

        $view_path = DIYGW_ADDON_PATH . $this->addon . DS . 'view' . DS;
        if (file_exists(DIYGW_ADDON_PATH . $this->addon . DS.'common.php')) {
            include_once DIYGW_ADDON_PATH . $this->addon . DS.'common.php';
        }
        // 重置配置
        Config::set('template.view_path', $view_path);
        parent::__construct($this->app);
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

}
