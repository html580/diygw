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
class Order extends Interceptor
{
    public $isLogin=true;
    public $title="我的订单";

    public function index()
    {
        return $this->fetch();
    }

    public function confirm()
    {
        $this->title="订单确认";
        $ids = $this->request->request("ids");
        $this->assign("ids",$ids);
        return $this->fetch();
    }


    public function nav()
    {
        
        $titles=["待付款","待发货","待收货","退款/售后"];
        $status=["1","2","3","10"];
        $rows=[];
        foreach ($titles as $index=>$title){
            $row['title']=$title;
            $row['status']=$status[$index];
            $rows[]=$row;
        }
        return $this->getPageMessage("加载数据成功",$rows,count($row));
    }

    public function pay()
    {
        $__isxcx__ = $this->request->request("__isxcx__");
        $__isajax__ = $this->request->request("__isajax__");
        $cart['user_id']=$this->uid;
        $cart['dashboard_id']=$this->dashboardid;
        $ids=$this->request->request("ids");
        $cartDb = Db::name("AppCart")->where($cart);
        if (!empty($ids)) {
            if(is_array($ids)){
                $cartDb->whereIn('id',$ids);
            }else{
                $cartDb->whereIn('id',explode(",",$ids));
            }
        }
        $list =$cartDb->select();
        if(empty($list)){
           return $this->getErrorPage('购物车空空如也，不能进行支付');
        }
        $totalPrice=0;
        foreach ($list as $key=>$item){
            $totalPrice=$totalPrice+$item['link_price']*$item['link_total'];
        }

        try{
            $order["user_id"]=$this->uid;
            $order["dashboard_id"]=$this->dashboardid;
            $order["order_id"]=create_guid();
            $order["pay_price"]=$totalPrice;
            $order['mpid']=$this->mpid;
            $order["cart_price"]=$totalPrice;
            $order['create_time']=date("Y-m-d H:i:s", time());
            $order['update_time']=date("Y-m-d H:i:s", time());
            $order['pay_title']=$list[0]["link_title"];
            $order['client_remark']=$this->request->request("remark");
            $order["cart_list"]=json_encode($list);

            $addressid=$this->request->request("addressid");
            if(!empty($addressid)){
                $address= Db::name('AppAddress')->where('id',$addressid)->find();
                $order['client_gender']=$address["gender"];
                $order['client_name']=$address["name"];
                $order['client_tel']=$address["tel"];
                $order['client_address']=$address["address"].$address["address_xq"];
            }


            $orderid =  Db::name("AppOrder")->insertGetId($order);
            $cartIds=[];
            foreach ($list as $key=>$item){
                $cartIds[]=$item['id'];
                $item["id"]=null;
                $item["order_id"]=$orderid;
                Db::name("AppOrderInfo")->insertGetId($item);
            }
            Db::name("AppCart")->whereIn('id',$cartIds)->delete();
            if((!empty($__isajax__)&&$__isajax__=='true')||(!empty($__isxcx__)&&$__isxcx__=='1')){
                echo json_encode(["id"=>$orderid,"status"=>'success']);
            }else{
                $this->redirect(url('@index/pay/index').'?id='.$orderid);
            }
        }catch (Exception $e) {
            if((!empty($__isajax__)&&$__isajax__=='true')||(!empty($__isxcx__)&&$__isxcx__=='1')){
                $this->getErrorMessage('生成订单失败');
            }else{
                $this->getErrorPage('生成订单失败');
            }
        }
    }



    public function changeStatus()
    {
        
        $id = $this->request->request("id");
        $status = $this->request->request("status");

        $order= Db::name("AppOrder")->where(array('id'=>$id))->find();
        if($order['status']>=2){
            return $this->getErrorMessage("订单已支付成功，不能手动修改状态");
        }
        $result = Db::name("AppOrder")->where(array('id'=>$id))->update(array('status'=>$status));
        if(false!==$result){
            return $this->getSuccessMessage("修改状态成功");
        }else{
            return $this->getSuccessMessage("修改状态失败");
        }
    }

    public function data()
    {
        $status = $this->request->request("status");
        $order['dashboard_id']=$this->dashboardid;
        $order['user_id']=$this->uid;
        $order['mpid']=$this->mpid;
        $order['status']=$status;

        $list = Db::name("AppOrder")->where($order)->select();

        return $this->getPageMessage("加载数据成功",$list,count($list));
    }

}