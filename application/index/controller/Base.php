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
use controller\BasicData;
use think\Controller;
use think\Db;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Base extends BasicData
{

    public function getDashboardExtends(){
        $dashboardid = session('dashboardid');
        $mpid = session('mpid');

        $data=[];
        $result = Db::name("AppDashboardExtend")->where(array('dashboard_id'=>$dashboardid,'mpid'=>$mpid))->select();

        foreach ($result as $key => $value) {
            $data[$value['name']]=$value['value'];
        }

        $homePage = Db::name("AppPage")->where(array('dashboard_id'=>$dashboardid,'template'=>'mobile','mpid'=>$mpid))->order('is_home desc,orderlist asc')->find();

        if(!empty($homePage)){
            $homePage = url('@index/page/index/pid/'.$homePage['id']);
            $data["homePage"]=$homePage;
        }else{
            $data["homePage"]=url('@index/home/index');
        }
        $this->assign('extendValues',$data);
        return $data;
    }


    public function  getErrorPage($message){
        $homePage = Db::name("AppPage")->where(array('dashboard_id'=>session('dashboardid'),'template'=>'mobile','mpid'=>session('mpid')))->order('is_home desc,orderlist asc')->find();
        $this->assign('title','温馨提示');
        $this->assign('message',$message);
        if(empty($homePage)){
            $homePage = url('@index/page/index/id/'.$homePage['id']);
            $this->assign('homepage',$homePage);
        }
        return $this->fetch("login/perror");
    }

    public function  getErrorMessage($message){
        echo json_encode(['status'=>'error','message'=>$message]);
    }

    public function  getPageMessage($message,$rows=[],$total=1,$totalPage=1){
        echo json_encode(['status'=>'success','message'=>$message,'rows'=>$rows,'total'=>$total,'totalPage'=>$totalPage]);
    }

    public function  getSuccessMessage($message){
        echo json_encode(['status'=>'success','message'=>$message]);
    }

}