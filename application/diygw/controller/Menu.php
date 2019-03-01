<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\diygw\controller;

use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Db;

/**
 * 系统后台管理管理
 * Class Menu
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15
 */
class Menu extends BasicAdmin
{
    /**
     * 绑定操作模型
     * @var string
     */
    public $table = 'AppMenu';
    public $currentMenuUrl='menu/index';
    public $formSuccessUrl='menu/index';
    /**
     * 菜单列表
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function index()
    {
        $this->title = '后台菜单管理';
        $db = Db::name($this->table)->order('sort asc,id asc');
        if ($this->request->isPost() && $this->request->post('action') === 'resort') {
            $this->_form_result();
        }
        return parent::_list($db, false);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_data_filter(&$data)
    {
        foreach ($data as $key=>&$vo) {
            if ($vo['url'] !== '#') {
                $vo['url'] = url($vo['url']) . (empty($vo['params']) ? '' : "?{$vo['params']}");
            }
            $vo['ids'] = join(',', ToolsService::getArrSubIds($data, $vo['id']));
        }
        $data = ToolsService::arr2table($data);
    }

    /**
     * 添加菜单
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function add()
    {
        $this->title = '新增菜单';
        return $this->_form($this->table, 'form');
    }

    /**
     * 编辑菜单
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function edit()
    {
        $this->title = '编辑菜单';
        return $this->_form($this->table, 'form');
    }

    public function _form_result(){
        cache('AppMenus',null);
        session('layoutmenus',null);
    }
    /**
     * 表单数据前缀方法
     * @param array $vo
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            // 上级菜单处理
            $_menus = Db::name($this->table)->where(['status' => '1'])->order('sort asc,id asc')->select();
            $_menus[] = ['title' => '顶级菜单', 'id' => '0', 'pid' => '-1'];
            $menus = ToolsService::arr2table($_menus);
            foreach ($menus as $key => &$menu) {
                if (substr_count($menu['path'], '-') > 3) {
                    unset($menus[$key]);
                    continue;
                }
                if (isset($vo['pid'])) {
                    $current_path = "-{$vo['pid']}-{$vo['id']}";
                    if ($vo['pid'] !== '' && (stripos("{$menu['path']}-", "{$current_path}-") !== false || $menu['path'] === $current_path)) {
                        unset($menus[$key]);
                        continue;
                    }
                }
            }
            // 读取系统功能节点
            $nodes = NodeService::get();
            foreach ($nodes as $key => $node) {
                if (empty($node['is_menu'])) {
                    unset($nodes[$key]);
                }
            }
            // 设置上级菜单
            if (!isset($vo['pid']) && $this->request->get('pid', '0')) {
                $vo['pid'] = $this->request->get('pid', '0');
            }
            $this->assign(['nodes' => array_column($nodes, 'node'), 'menus' => $menus]);
        }else{
            if(isset($vo['pid'])&&isset($vo['id'])&&$vo['id']==$vo['pid']){
                return false;//防止自己选自己
            }
        }
    }

    /**
     * 删除菜单
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $this->_form_result();
            $this->success("菜单删除成功!", url('index'));
        }
        $this->error("菜单删除失败, 请稍候再试!");
    }

    /**
     * 菜单禁用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            $this->_form_result();
            $this->success("菜单禁用成功!", url('index'));
        }
        $this->error("菜单禁用失败, 请稍候再试!");
    }

    /**
     * 菜单禁用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->_form_result();
            $this->success("菜单启用成功!", url('index'));
        }
        $this->error("菜单启用失败, 请稍候再试!");
    }

}
