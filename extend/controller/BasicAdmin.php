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

namespace controller;

use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Controller;
use think\Db;
use think\db\Query;
use think\helper\Str;

/**
 * 后台权限基础控制器
 * Class BasicAdmin
 * @package controller
 */
class BasicAdmin extends BasicData
{
    /**
     *  公众号ID
     */
    public $mpid;
    /**
     *  公众号信息
     */
    public $wechatInfo;
    /**
     *  当前菜单 为了选中编辑时可能没配置进菜单时，默认显示左边的菜单高亮
     */
    public $currentMenuUrl='';
    /**
     * 页面标题
     * @var string
     */
    public $title;

    /**
     * 表单提交成功后转向地址
     * @var string
     */
    public $formSuccessUrl='';

    /**
     * 默认操作数据表
     * @var string
     */
    public $table;

    protected function initialize(){
        if (!session('user.id')) {
            $this->redirect('@admin/login');
        }
        $mpid = session('mpid');
        $wechatInfo = session('wechatInfo');
        if(empty($mpid)||empty($wechatInfo)){
            $wechatDefault = Db::name("SystemConfig")->where(['name'=>'wechat_default'])->find();
            $mpid = $wechatDefault['value'];
            $wechatInfo = Db::name('wechat')->where(['id' => $mpid])->find();
            if(empty($wechatInfo)){
                $wechatInfo = Db::name('wechat')->find();
            }
            session('wechatInfo',$wechatInfo);
            session('mpid',$wechatInfo['id']);
        }
        $this->mpid= $wechatInfo['id'];
        session('mpid',$wechatInfo['id']);
        $this->wechatInfo =$wechatInfo;
        $this->assign('wechatInfo',$this->wechatInfo);
        $this->assign('mpid',$this->mpid);

        if(!$this->request->isAjax()){
            if( session('user')){
                $systemMenus = cache("SystemMenus");
                if(empty($systemMenus)){
                    $systemMenus = (array)Db::name('SystemMenu')->where(['status' => '1'])->order('sort asc,id asc')->select();
                    cache("SystemMenus",$systemMenus);
                }
                $model_name = $this->request->module();
                $controller      = Str::snake($this->request->controller());
                $action_name = $this->request->action();
                $currentMenu = ToolsService::getCurrentMenu($systemMenus,$model_name.'/'.$controller.'/'.$action_name,'url');
                $this->_menu_filter($systemMenus, $currentMenu);
                $this->assign('currentMenu', $currentMenu);
                $topParentMenu =[];
                if(!empty($currentMenu)){
                    $topParentMenu = ToolsService::getTopParentMenu($systemMenus,$currentMenu['id']);
                }
                $this->assign('topParentMenu', $topParentMenu);
                $layoutmenus = session("layoutmenus");
                if(empty($layoutmenus)){
                    NodeService::applyAuthNode();
                    $layoutmenus = $this->buildMenuData(ToolsService::arr2tree($systemMenus), NodeService::get(), !!session('user'));
                    session("layoutmenus",$layoutmenus);
                }
                $this->assign('layoutmenus', $layoutmenus);
            }

        }

    }

    public function _menu_filter($systemMenus,&$currentMenu)
    {
        if (empty($currentMenu)) {
            if(empty($this->currentMenuUrl)){
                $this->currentMenuUrl      = Str::snake($this->request->controller()).'/index';
            }
            $this->currentMenuUrl = $this->request->module() . "/" . $this->currentMenuUrl;
            $currentMenu = ToolsService::getCurrentMenu($systemMenus, $this->currentMenuUrl, 'url');

        }
        return true;
    }

    /**
     * 获取当前节点的面包屑
     * @param string $id 节点ID
     * @author LK <280160522@qq.com>
     * @return array
     */
    public static function getBrandCrumbs($id){
        if (!$id) {
            return false;
        }
        $map = $menu = [];
        $map['id'] = $id;
        $row = self::where($map)->field('id,pid,title,url,param')->find();
        if ($row->pid > 0) {
            if (isset($row->lang->title)) {
                $row->title = $row->lang->title;
            }
            $menu[] = $row;
            $childs = self::getBrandCrumbs($row->pid);
            if ($childs) {
                $menu = array_merge($childs, $menu);
            }
        }
        return $menu;
    }


    /**
     * 表单默认操作
     * @param Query $dbQuery 数据库查询对象
     * @param string $tplFile 显示模板名字
     * @param string $pkField 更新主键规则
     * @param array $where 查询规则
     * @param array $extendData 扩展数据
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    protected function _form($dbQuery = null, $tplFile = '', $pkField = '', $where = [], $extendData = [])
    {
        $db = is_null($dbQuery) ? Db::name($this->table) : (is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery);
        $pk = empty($pkField) ? ($db->getPk() ? $db->getPk() : 'id') : $pkField;
        $pkValue = $this->request->request($pk, isset($where[$pk]) ? $where[$pk] : (isset($extendData[$pk]) ? $extendData[$pk] : null));
        // 非POST请求, 获取数据并显示表单页面
        if (!$this->request->isPost()) {
            $vo = ($pkValue !== null) ? array_merge((array)$db->where($pk, $pkValue)->where($where)->find(), $extendData) : $extendData;
            if (false !== $this->_callback('_form_filter', $vo, [])) {
                empty($this->title) || $this->assign('title', $this->title);

                return $this->fetch($tplFile, ['vo' => $vo]);
            }
            return $vo;
        }
        // POST请求, 数据自动存库
        $data = array_merge($this->request->post(), $extendData);
        if (false !== $this->_callback('_form_filter', $data, [])) {
            $result = DataService::save($db, $data, $pk, $where);
            if (false !== $this->_callback('_form_result', $result, $data)) {
                if ($result !== false) {
                    $this->success('恭喜, 数据保存成功!', $this->formSuccessUrl);
                }
                $this->error('数据保存失败, 请稍候再试!');
            }
        }else{
            $this->error('数据保存失败, 请稍候再试!');
        }
    }

    /**
     * 列表集成处理方法
     * @param Query $dbQuery 数据库查询对象
     * @param bool $isPage 是启用分页
     * @param bool $isDisplay 是否直接输出显示
     * @param bool $total 总记录数
     * @param array $result 结果集
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    protected function _list($dbQuery = null, $isPage = true, $isDisplay = true, $total = false, $result = [])
    {
        $db = is_null($dbQuery) ? Db::name($this->table) : (is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery);
        // 列表排序默认处理
        if ($this->request->isPost() && $this->request->post('action') === 'resort') {
            foreach ($this->request->post() as $key => $value) {
                if (preg_match('/^_\d{1,}$/', $key) && preg_match('/^\d{1,}$/', $value)) {
                    list($where, $update) = [['id' => trim($key, '_')], ['sort' => $value]];
                    if (false === Db::table($db->getTable())->where($where)->update($update)) {
                        $this->error('列表排序失败, 请稍候再试');
                    }
                }
            }
            $this->success('列表排序成功, 正在刷新列表', $this->formSuccessUrl);
        }
        // 列表数据查询与显示
        if (null === $db->getOptions('order')) {
            in_array('sort', $db->getTableFields($db->getTable())) && $db->order('sort asc');
        }
        if ($isPage) {
            $rows = intval($this->request->get('rows', cookie('page-rows')));
            cookie('page-rows', $rows = $rows >= 10 ? $rows : 20);
            // 分页数据处理
            $query = $this->request->get();
            $page = $db->paginate($rows, $total, ['query' => $query]);
            if (($totalNum = $page->total()) > 0) {
                list($rowHTML, $curPage, $maxNum) = [[], $page->currentPage(), $page->lastPage()];
                foreach ([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200] as $num) {
                    list($query['rows'], $query['page']) = [$num, '1'];
                    $url = $this->request->baseUrl() . '?' . urldecode(http_build_query($query));
                    $rowHTML[] = "<option data-url='{$url}' " . ($rows === $num ? 'selected' : '') . " value='{$num}'>{$num}</option>";
                }
                list($pattern, $replacement) = [['|href="(.*?)"|', '|pagination|'], ['data-open="$1"', 'pagination pull-right']];
                $html = "<span class='pagination-trigger nowrap'>共 {$totalNum} 条记录，每页显示 <select class='layui-select' data-auto-none>" . join('', $rowHTML) . "</select> 条，共 {$maxNum} 页当前显示第 {$curPage} 页。</span>";
                list($result['total'], $result['list'], $result['page']) = [$totalNum, $page->all(), $html . preg_replace($pattern, $replacement, $page->render())];
            } else {
                list($result['total'], $result['list'], $result['page']) = [$totalNum, $page->all(), $page->render()];
            }
        } else {
            $result['list'] = $db->select();
        }
        if (false !== $this->_callback('_data_filter', $result['list'], []) && $isDisplay) {
            !empty($this->title) && $this->assign('title', $this->title);

            return $this->fetch('', $result);
        }
        return $result;
    }

    /**
     * 当前对象回调成员方法
     * @param string $method
     * @param array|bool $data1
     * @param array|bool $data2
     * @return bool
     */
    protected function _callback($method, &$data1, $data2)
    {
        foreach ([$method, "_" . $this->request->action() . "{$method}"] as $_method) {
            if (method_exists($this, $_method) && false === $this->$_method($data1, $data2)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 后台主菜单权限过滤
     * @param array $menus 当前菜单列表
     * @param array $nodes 系统权限节点数据
     * @param bool $isLogin 是否已经登录
     * @return array
     */
    public function buildMenuData($menus, $nodes, $isLogin)
    {
        foreach ($menus as $key => &$menu) {
            !empty($menu['sub']) && $menu['sub'] = $this->buildMenuData($menu['sub'], $nodes, $isLogin);
            if (!empty($menu['sub'])) {
                $menu['url'] = '#';
            } elseif (preg_match('/^https?\:/i', $menu['url'])) {
                continue;
            } elseif ($menu['url'] !== '#') {
                $node = join('/', array_slice(explode('/', preg_replace('/[\W]/', '/', $menu['url'])), 0, 3));
                $menu['url'] = url($menu['url']) . (empty($menu['params']) ? '' : "?{$menu['params']}");
                if (isset($nodes[$node]) && $nodes[$node]['is_login'] && empty($isLogin)) {
                    unset($menus[$key]);
                } elseif (isset($nodes[$node]) && $nodes[$node]['is_auth'] && $isLogin && !auth($node)) {
                    unset($menus[$key]);
                }
            } else {
                unset($menus[$key]);
            }
        }
        return $menus;
    }



}
