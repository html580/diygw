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
class Order extends BasicAdmin
{
    public function index()
    {
        return $this->fetch();
    }

    public function data()
    {
        $where = ['mpid'=>$this->mpid,'dashboard_id'=>session('dashboardid')];
        if(!empty($this->request->request("status"))){
            $where['status']=$this->request->request("status");
        }
        $page = Db::name("AppOrder")->where($where)->paginate($this->getPageRow(), false, ['page'=>$this->getPageNum()]);

        $plist=$page->all();
        foreach ($plist as &$item){
            switch ($item['status']){
                case "1":
                    $item['statustext']="待支付";
                    break;
                case "2":
                    $item['statustext']="已付款待发货";
                    break;
                case "3":
                    $item['statustext']="待收货";
                    break;
                case "4":
                    $item['statustext']="交易完成";
                case "5":
                    $item['statustext']="已退款";
                    break;
            }
            $rows[]=$item;
        }
        $list['rows']=$plist;
        $list['total']=$page->total();
        $list['totalPage']=$page->lastPage();
        $list['status']='success';
        echo json_encode($list);

    }

    public function tk()
    {
      try {
            $order = Db::name("AppOrder")->where([
                'id'=>$this->request->request("id"),
                'status'=>'2'
            ])->find();
            if($order){
                $result = refundOrder($order['trade_no']);
                echo json_encode($result);
            }else{
                $info = ['status'=>'success', 'message'=>'订单未支付不能发起退款'];
                echo json_encode($info);
            }
       } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>$e->getMessage()];
            echo json_encode($info);
        }
    }


    /**
     * 发货
     */
    public function fh(){
        try {
            Db::name("AppOrder")->where([
                'id'=>$this->request->request("id")
            ])->update(['status'=>'3']);
            $info = ['status'=>'success', 'message'=>'设为完成成功'];
            echo json_encode($info);
        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'设为完成失败'];
            echo json_encode($info);
        }
    }

    public function xq(){
        $order = Db::name("AppOrder")->where(['id'=>$this->request->request("id")])->find();
        queryOrder($order['trade_no']);
        $order = Db::name("AppOrder")->where(['id'=>$this->request->request("id")])->find();
        switch ($order['status']){
            case "1":
                $order['statustext']="待支付";
                break;
            case "2":
                $order['statustext']="已付款待发货";
                break;
            case "3":
                $order['statustext']="待收货";
                break;
            case "4":
                $order['statustext']="交易完成";
            case "5":
                $order['statustext']="已退款";
                break;
        }
        $this->assign("order",$order);
        return $this->fetch();
    }
    /**
     * 完成
     */
    public function wc()
    {
        try {
            Db::name("AppOrder")->where([
                'id'=>$this->request->request("id")
            ])->update(['status'=>'4']);
            $info = ['status'=>'success', 'message'=>'设为完成成功'];
            echo json_encode($info);
        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'设为完成失败'];
            echo json_encode($info);
        }
    }

}