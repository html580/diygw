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
use service\LogService;
use service\WechatService;
use think\Exception;
use think\Db;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Base extends BasicAdmin
{

    public function getDashboardExtends(){
        $dashboardid = session('dashboardid');
        $mpid = session('mpid');

        $data=[];
        $result = Db::name("AppDashboardExtend")->where(array('dashboard_id'=>$dashboardid,'mpid'=>$mpid))->select();

        foreach ($result as $key => $value) {
            $data[$value['name']]=$value['value'];
        }

        $homePage = Db::name("AppPage")->where(array('dashboard_id'=>$dashboardid,'template'=>'mobile','is_home'=>'1','mpid'=>$mpid))->find();
        if(empty($homePage)){
            $homePage = Db::name("AppPage")->where(array('dashboard_id'=>$dashboardid,'template'=>'mobile','mpid'=>$mpid))->order('is_home desc,orderlist asc')->find();
        }
        if(!empty($homePage)){
            $homePage = url('@index/page/index/id/'.$homePage['id']);
            $data["homePage"]=$homePage;
        }
        $this->assign('extendValues',$data);
        return $data;
    }

}