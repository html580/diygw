<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com>
// +----------------------------------------------------------------------

namespace think\addons;

use think\Request;
use think\Config;
use think\Loader;

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
    public function __construct(Request $request = null)
    {
        // 生成request对象
        $this->request = is_null($request) ? Request::instance() : $request;
        // 初始化配置信息
        $this->config = Config::get('template') ?: $this->config;
        // 是否自动转换控制器和操作名
        $convert = Config::get('url_convert');
        $filter = $convert ? 'strtolower' : '';
        // 处理路由参数
        $this->addon = $this->request->param('addon', '', $filter);
        $this->controller = $this->request->param('controller', 'index', $filter);
        $this->action = $this->request->param('action', 'index', $filter);

        // 生成view_path
        $view_path = $this->config['view_path'] ?: 'view';

        // 重置配置
        Config::set('template.view_path', DIYGW_ADDON_PATH . $this->addon . DS . $view_path . DS);

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
}
