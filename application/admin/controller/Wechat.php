<?php

// +----------------------------------------------------------------------
// | Diygw
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
class Wechat extends BasicAdmin
{

    /**
     * 绑定操作模型
     * @var string
     */
    public $table = 'Wechat';

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
        $this->title = '后台公众号管理';
        $db = Db::name($this->table)->order('id asc');
        return parent::_list($db, false);
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
        if ($this->request->isPost()) {
            $data['valid_token'] = getRandChar('32');
            $data['token'] = getRandChar('32');
            $data['encodingaeskey'] = getRandChar('43');
            $data['create_time'] = time();
            $data['user_id'] = session('user.id');
            return $this->_form($this->table, 'form','',[],$data);
        }else{
            return $this->_form($this->table, 'form',[]);
        }

    }


    /**
     * 编辑公众号
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    public function auth(){
        $wechat = Db::name($this->table)->where('id',$this->request->request("id"))->find();
        $wechat['url'] = url('@wechat/index/index', ['mpid' => $wechat['id']],true,true);
        $this->assign('wechat',$wechat);
        return $this->fetch();
    }
    /**
     * 删除公众号
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $this->success("公众号删除成功!", '');
        }
        $this->error("公众号删除失败, 请稍候再试!");
    }

    /**
     * 公众号默认
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function jump()
    {
         $id = $this->request->request("id");
         session('mpid', $id);
         $this->wechatInfo = Db::name('wechat')->where(['id' => $id])->find();
         session('wechatInfo', $this->wechatInfo);
         sysconf('wechat_default',$id);
         $this->redirect('@admin/index');
    }

    /**
     * 公众号禁用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("公众号启用成功!", '');
        }
        $this->error("公众号启用失败, 请稍候再试!");
    }

}
