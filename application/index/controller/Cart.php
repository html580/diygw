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
class Cart extends Interceptor
{
    public $isLogin=true;
    public $title="我的购物车";

    public function index()
    {

        return $this->fetch();
    }

    public function save()
    {

        $uid =$this->getUid();
        $tablename=$this->getTable();
        $linkjson['tablename']=$tablename;
        $sqlfields = $this->request->request("sqlfields");
		$linkjson['sqlfields']=$sqlfields;

		$sql="select $sqlfields from $tablename where 1=1 and ";

		$pkey=$this->request->request("pkey");
		if(empty($pkey)){
            $pkey='id';
        }
        $sql = $sql.' '.$pkey.'= :'.$pkey;
        $link = $this->getUserDb()->query($sql,array($pkey=>  $this->request->request("linkid")));

        if(count($link)<0){
            return $this->getErrorMessage("商品已下架或不存在");
        }

        $link = $link[0];
        if(empty($link['stock'])){
            return $this->getErrorMessage("库存不足");
        }

        $cart['dashboard_id']=$this->dashboardid;
        $cart['dbid']=$this->request->request("dbid");
        $cart['user_id']=$uid;
        $cart['link_id']=$this->request->request("linkid");
        $cart['form_id']=$this->request->request("formid");
        $cart['status']=1;

        $result =  Db::name('AppCart')->where($cart)->find();
      
        if(empty($result)){
            $cart['pkey']=$pkey;
            $cart['link_json']=json_encode($linkjson);
            $cart['page_id']=$this->request->request("page_id");
            $cart['page_name']=$this->request->request("page_name");
            $cart['link_title']=$link["title"];
            $cart['link_img']=$link["img"];
            $cart['link_price']=$link["price"];
            $cart['link_total']=1;
            $cart['mpid']=$this->mpid;
            if(empty($cart['link_price'])){
                $cart['link_price']=1;
            }
            $cart['status']=1;
            $cart['create_time']=date("Y-m-d H:i:s", time());
            $cart['update_time']=date("Y-m-d H:i:s", time());

            if(Db::name('AppCart')->insertGetId($cart)){
                return $this->getSuccessMessage("加入购物车成功");
            }else{
                return $this->getErrorMessage("加入购物车失败");
            }
        }else{
            return $this->getSuccessMessage("购物车已经存在此商品");
        }
    }

    public function data()
    {
        $ids= $this->request->request("ids");
        if($ids){
            $list =  Db::name('AppCart')->whereIn('id',explode(",",$ids))->select();
        }else{
            $list =  Db::name('AppCart')->where(array('dashboard_id'=>$this->dashboardid,'user_id'=>$this->getUid(),'mpid'=>$this->mpid))->select();
        }
        return $this->getPageMessage("加载数据成功",$list,count($list));
    }

    public function changeCount()
    {
        
        $linkTotal = $this->request->request("linktotal");
        if($linkTotal<1){
            return $this->getErrorPage("数量不能小于1");
        }
        $id =$this->request->request("id");
        $result = Db::name('AppCart')->where('id',$id)->update(array("link_total"=>$linkTotal));
        if(false!==$result){
            return $this->getSuccessMessage("修改数量成功");
        }else{
            return $this->getSuccessMessage("修改数量失败");
        }
    }



    public function delete()
    {
        
        $ids    =   $this->request->request("values");
        if(!is_array($ids)){
            $ids[]    =  $this->request->request("id");
        }
        $result = Db::name('AppCart')->whereIn('id',$ids)->delete();
        if(false!==$result){
            return $this->getSuccessMessage("删除成功");
        }else{
            return $this->getSuccessMessage("删除失败");
        }
    }

    public function clear()
    {
        $result = Db::name('AppCart')->where(array("dashboard_id"=>$this->dashboardid,"user_id"=>$this->uid,"mpid"=>$this->mpid))->delete();
        if(false!==$result){
            return $this->getSuccessMessage("清空成功");
        }else{
            return $this->getSuccessMessage("清空失败");
        }
    }

}