<?php
namespace app\diygw\controller;
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
use controller\BasicAdmin;

use service\HttpService;
use service\LogService;
use service\WechatService;
use app\diygw\common\PclZip;
use think\Exception;
use think\Db;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Page extends BasicAdmin{

    public function index($pid)
    {
        $page = Db::name("AppPage")->where('id',$pid)->find();
        if(!$page){
            return $this->fetch('empty');
        }
        session('dashboardid',$page['dashboard_id']);
        $this->assign('mpid',$page['mpid']);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function emptypage(){
        $page['title']="温馨提示";
        $page['content']="<h3>你还未设计任何后台页面，请前往<a  target='_blank' href='http://www.diygw.com/'>DIY官网</a>设计</h3>";
        $this->assign('page',$page);
        return $this->fetch("index");
    }
}