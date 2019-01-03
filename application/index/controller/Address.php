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
class Address extends Interceptor
{
    public $isLogin=true;

    public $title="我的地址";

    public function index()
    {
        $extendValues = $this->getDashboardExtends();
        return $this->fetch();
    }


    public function data()
    {

        $dashboardid = session("dashboardid");
        $mpid =session("mpid");
        $uid =$this->getUid();

        $list = Db::name('AppAddress')->where(["user_id"=>$uid,"mpid"=>$mpid])->select();

        $is_def = $this->request->request("isdef");

        $data=[];

        if($is_def=='1'){
            foreach ($list as $key=>$item){
                if($item['is_def']==1){
                    $data[]=$item;
                    break;
                }
            }
            if(count($data)==0 && count($list)>0){
                $data[]=$list[0];
            }else{
                $list=$data;
            }
        }
        if(empty($list)){
            $list=[];
        }
        echo json_encode(["status"=>"success","message"=>"获取数据成功","rows"=>$list,"total"=>count($list),"totalPage"=>1]);
    }


    public function edit()
    {
        $id=$this->request->request("id");
        if(!empty($id)){
            $address =  Db::name('AppAddress')->where(['id'=>$id])->find();
            if($address['user_id']!=$this->getUid()){
                return $this->getErrorPage("没有权限修改地址");
            }
        }
        return $this->fetch();
    }

    public function save()
    {
        $dashboardid = $this->dashboardid;
        $mpid =$this->mpid;
        $uid =$this->getUid();

        $is_def = $this->request->request("is_def");

        $id=$this->request->request('id');
        if(!empty($id)){
            $address =  Db::name('AppAddress')->where(['id'=>$id])->find();
            if($address['user_id']!=$this->getUid()){
                return $this->getErrorMessage("没有权限修改地址");
            }
        }
        if($is_def=="1"){
            Db::name('AppAddress')->where(array("user_id"=>$uid,"mpid"=>$mpid,"dashboard_id"=>$dashboardid))->update(array("is_def"=>0));
        }
        if(empty($is_def)){
            $is_def = 0;
        }
        $address['dashboard_id']=$dashboardid;
        $address['mpid']=$this->mpid;
        $address['user_id']=$uid;
        $address['name']=$this->request->request("name");
        $address['tel']=$this->request->request("tel");
        $address['address']=$this->request->request("address");
        $address['address_xq']=$this->request->request("address_xq");
        $address['create_time']=date("Y-m-d H:i:s", time());
        $address['update_time']=date("Y-m-d H:i:s", time());
        $address['is_def']=$is_def;
        if(!empty($id)){
            Db::name('AppAddress')->where(array("id"=>$id))->update($address);

        }else{
            $result = Db::name('AppAddress')->insertGetId($address);
        }
        if(false!==$result){
            return $this->getSuccessMessage("保存地址成功");
        }else{
            return $this->getSuccessMessage("保存地址失败");
        }
    }


    public function delete()
    {
        $ids=$this->request->request('values/a');

        foreach ($ids as $id){
            $address =  Db::name('AppAddress')->where(['id'=>$id])->find();
            if($address['user_id']!=$this->getUid()){
                return $this->getErrorMessage("没有权限修改地址");
            }
        }
        $result = Db::name('AppAddress')->whereIn('id',$ids)->delete();
        if(false!==$result){
            return $this->getSuccessMessage("删除地址成功");
        }else{
            return $this->getSuccessMessage("删除地址失败");
        }
    }

    public function changeDef()
    {
        $id=$this->request->request('id');
        if(!empty($id)){
            $address =  Db::name('AppAddress')->where(['id'=>$id])->find();
            if($address['user_id']!=$this->getUid()){
                return $this->getErrorMessage("没有权限修改地址");
            }
        }
        $uid = $this->getUid();
        $mpid =session("mpid");
        $dashboardid =session("dashboardid");
        Db::name('AppAddress')->where(array("user_id"=>$uid,"mpid"=>$mpid,"dashboard_id"=>$dashboardid))->update(array("is_def"=>0));

        $result = Db::name('AppAddress')->where(array("id"=>$id))->update(array("is_def"=>1));

        if(false!==$result){
            return $this->getSuccessMessage("设置成功");
        }else{
            return $this->getSuccessMessage("设置失败");
        }
    }
}