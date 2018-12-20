<?php

// +----------------------------------------------------------------------
// | DiygwApp
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 DIY官网 [ http://www.diygw.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.diygw.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/html580/diygw
// +----------------------------------------------------------------------

namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Db;

/**
 * 微信公众号管理
 * Class Wechat
 * @package app\admin\controller
 * @author LK <diygwcom@foxmail.com>
 * @date 2018/11/15
 */
class Hooks extends BasicAdmin
{
    public $currentMenuUrl='hooks/index';
    public $formSuccessUrl='hooks/index';
    /**
     * 绑定操作模型
     * @var string
     */
    public $table = 'Hooks';

    /**
     * 公众号列表
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function index()
    {
        $this->title = '钩子管理';
        $db = Db::name($this->table)->order('id asc');
        return parent::_list($db, true);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_data_filter(&$data)
    {
        int_to_string ( $data, array (
            'type' => array(1=>'视图',2=>'控制器')
        ) );
    }


    /**
     * 添加公众号
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function add()
    {
        $this->title = '新增钩子';
        return $this->_form($this->table, 'form');
    }


    /**
     * 用户编辑
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function edit()
    {
        $this->title = '编辑钩子';
        return $this->_form($this->table, 'form');
    }


    /**
     * 表单数据默认处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {
            $hook=Db::name($this->table)->where(['name' => $data['name']])->find();
            if (!empty($hook)&&$hook['id']!=$data['id']){
                $this->error ( '钩子已存在' );
            }
        }
    }

}
