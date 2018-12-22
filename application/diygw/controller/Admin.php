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
class Admin extends Base
{

    public function index($id,$template)
    {
        $this->assign("id",$id);
        $dashboard = Db::name("AppDashboard")->where('mpid',$this->mpid)->select();
        $src="";
        foreach ($dashboard as $key => $item ){
            if($item['id']==$id){
                $dashboardtitle=$item['title'];
                $this->assign("dashboardtitle",$dashboardtitle);
            }
        }
        session('dashboardid',$id);
        session('mpid',$this->mpid);

        $this->assign('dashboard',$dashboard);
        $this->assign("template",$template);
        $this->assign("pageid","");
        $dashboardpage = Db::name("AppPage")->where(array('dashboard_id'=>$id,'template'=>$template,'mpid'=>$this->mpid))->order('is_home desc,orderlist asc')->select();

        if(!empty($dashboardpage)&&is_array($dashboardpage)>0){
            if($template=='mobile'){
                $this->assign("pageid",$dashboardpage[0]["id"]);
                $src=url('@index/page/index',['pid'=>$dashboardpage[0]["id"]]);
            }else{
                $src=url('@diygw/page/index',['pid'=>$dashboardpage[0]["id"]]);
                $this->assign("pageid",$dashboardpage[0]["id"]);
            }
        }

        $this->assign('domain',$this->request->domain());
        $dashboardExtends = $this->getDashboardExtends();
        $pageGroup = $dashboardExtends[$template.'group'];
        if(empty($pageGroup)){
            $groups[]=['title'=>'默认分组','ids'=>array()];
        }else{
            $groups = @json_decode($pageGroup,true);
        }

        foreach ($groups as &$group){
            $ids = $group['ids'];
            $children=[];
            foreach ($ids as $id) {
                foreach ($dashboardpage as $key => $page) {
                    if ($page['id'] ==$this->mpid.'_'.$id){
                        $children[]=$page;
                        unset($dashboardpage[$key]);
                        break;
                    }
                }
            }
            $group['children'] =$children;
        }

        if(count($dashboardpage)>0){
            $groups[0]['children']=array_merge($groups[0]['children'],$dashboardpage);
        }


        $this->assign("groups",$groups);
        $this->assign("dashboardpage",$dashboardpage);
        if(empty($src)){
            $src=url("@diygw/page/emptypage");
        }
        $this->assign("src",$src);
        return $this->fetch();
    }

}