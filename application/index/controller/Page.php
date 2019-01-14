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
use service\WechatService;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Page extends Base
{
    public function index($pid){
        $state = $this->request->request('state');
        if(isset($state)){
            loginCheck();

        }
        $page = Db::name("AppPage")->where('id',$pid)->find();
        if(!$page){
            return $this->fetch('empty');
        }
        session('dashboardid',$page['dashboard_id']);
        session('mpid',$page['mpid']);
        $this->assign('mpid',$page['mpid']);
        $homePage = Db::name("AppPage")->where(array('dashboard_id'=>$page['dashboard_id'],'template'=>'mobile','is_home'=>'1','mpid'=>$page['mpid']))->find();
        if(!empty($homePage)){
            $homePage = Db::name("AppPage")->where(array('dashboard_id'=>$page['dashboard_id'],'template'=>'mobile','mpid'=>$page['mpid']))->order('is_home desc,orderlist asc')->find();
        }
        if(!empty($homePage)){
            $homePage = url('@index/page/index/pid/'.$homePage['id']);
            $this->assign('homePage',$homePage);
        }else{
            $this->assign('homePage',"#");
        }
        $attributes = json_decode($page['attributes'],true);
        $fields=$attributes['design']['fields'];
        if($fields['istitle']){
            $istitle= $fields['istitle']['value'];//是否显示标题
            $this->assign('istitle',$istitle);
        }
        if($fields['backgroundColor']){
            $backgroundColor= $fields['backgroundColor']['value'];//
            $this->assign('backgroundColor',$backgroundColor);
        }
        if($fields['navigationBarBackgroundColor']){
            $navigationBarBackgroundColor= $fields['navigationBarBackgroundColor']['value'];//
            $this->assign('navigationBarBackgroundColor',$navigationBarBackgroundColor);
        }
        if($fields['styles']){
            $styles= $fields['styles']['value'];
            $this->assign('styles',$styles);
        }
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function emptypage(){

        return $this->getErrorPage('你还没有定义跳转页面哟，可根据实际需要安排');
    }

}