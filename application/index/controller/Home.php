<?php
namespace app\index\controller;
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
use think\Controller;
use think\Db;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Home extends Controller
{
    public function index()
    {
        if(empty(session('mpid'))||empty(session('dashboardid'))){
            $this->assign('title','温馨提示');
            $this->assign('content','你访问的页面不存在');
            return $this->fetch("login/perror");
        }

        $homePage = Db::name("AppPage")->where(array('dashboard_id'=>session('dashboardid'),'template'=>'mobile','mpid'=>session('mpid')))->order('is_home desc,orderlist asc')->find();;
        if(empty($homePage)){
            $this->assign('title','温馨提示');
            $this->assign('content','你访问的页面不存在');
            return $this->fetch("login/perror");
        }
        $homePage = url('@index/page/index/pid/'.$homePage['id']);
        $this->redirect($homePage);
    }
}