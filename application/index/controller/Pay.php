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
use think\Exception;

/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Pay extends Interceptor
{
    public $isLogin = true;
    public $isWechaLogin=true;

    public function index(){
        $id = $this->request->request("id");
        $ispay = $this->request->request("ispay");

        $order = Db::name('AppOrder')->where(['id'=>$id])->find();
        if(empty($order)){
            return $this->getErrorPage('支付订单不存在！');
        }
        if(!isset($ispay)){
            $ispay = 1;
        }
        $this->assign("ispay",$ispay);
        if($order['status']==0){
            return $this->getErrorPage('订单已取消！');
        }else if($order['status']==1){
            $order['orderstatus']="待支付";
            $this->assign("order",$order);
            $orderinfos = Db::name('AppOrderInfo')->where(['order_id'=>$id])->select();
            foreach ($orderinfos as $key=>$item){
                $linkjson = json_decode($item['link_json'],true);
                $tablename=$linkjson['tablename'];
                $sqlfields =$linkjson['sqlfields'];

                $sql="select $sqlfields from $tablename where 1=1 and ";

                $pkey=$item["pkey"];
                if(empty($pkey)){
                    $pkey='id';
                }
                $sql = $sql.' '.$pkey.'= :'.$pkey;

                $link = $this->getUserDb($item['dbid'])->query($sql,array($pkey=> $item['link_id']));

                if(count($link)<0){
                    return $this->getErrorPage($link['link_title']."商品已下架或不存在");
                }
                $link = $link[0];
                if(empty($link['stock'])){
                    return $this->getErrorPage($link['link_title']."库存不足");
                }
                if(intval($link['stock'])<intval($item['link_total'])){
                    return $this->getErrorPage($link['link_title']."库存不足");
                }
            }

            $uid = $this->getUid();
            $orderPay = Db::name('AppOrderPay')->where(['order_id'=>$order['id']])->find();
            if(!$orderPay){
                $orderpay["pay_type"]="0";
                $orderpay["mpid"]=$this->mpid;
                $orderpay["pay_title"]=$order['pay_title'];
                $orderpay["pay_time"]=date("Y-m-d H:i:s", time());
                $orderpay["trade_no"]=create_guid();
                $orderpay["pay_price"]=$order['pay_price'];
                $orderpay["order_id"]=$order['id'];
                $orderpay["user_id"]=$uid;
                $orderpay["openid"]=$this->getOpenId();
                $orderpay["status"]="1";
                Db::name('AppOrderPay')->insertGetId($orderpay);
                Db::name('AppOrder')->where(['id'=>$id])->update(["trade_no"=>$orderpay["trade_no"]]);
            }else{
                $trade_no=create_guid();
                $orderpay["trade_no"]=$trade_no;
                Db::name('AppOrderPay')->where(['id'=>$orderPay['id']])->update(["trade_no"=>$trade_no]);
                Db::name('AppOrder')->where(['id'=>$id])->update(["trade_no"=>$trade_no]);
            }


            $this->assign("pay",$orderpay);
            $data = addPaymentData($this->getOpenId(),$this->getUid(), $this->mpid,$this->dashboardid,$order['pay_price'],$order['pay_title'] ,'','1',$order['pay_title'],$orderpay["trade_no"],'AppOrderPay,AppOrder');
            try{
            $__isxcx__ = $this->request->request("__isxcx__");
            $result = payByWexinJsApi($data['id'],$__isxcx__);
            /*$option = [];
            $option["appId"] = 1;
            $option["timeStamp"] = (string)time();
            $option["nonceStr"] = 1;
            $option["package"] = "";
            $option["signType"] = "MD5";
            $option["paySign"] = 1;
            $option['timestamp'] = $option['timeStamp'];

            $config=[
                'debug'     => false,
                "appId"     => 1,
                "nonceStr"  => $data['noncestr'],
                "timestamp" => $data['timestamp'],
                "signature" => 1,
                'jsApiList' => [
                    'onWXDeviceBluetoothStateChange', 'onWXDeviceStateChange',
                    'openProductSpecificView', 'addCard', 'chooseCard', 'openCard',
                    'translateVoice', 'getNetworkType', 'openLocation', 'getLocation',
                    'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone',
                    'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'closeWindow', 'scanQRCode', 'chooseWXPay',
                    'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem',
                    'startScanWXDevice', 'stopScanWXDevice', 'onWXDeviceBindStateChange', 'onScanWXDeviceResult', 'onReceiveDataFromWXDevice',
                    'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice',
                    'openWXDeviceLib', 'closeWXDeviceLib', 'getWXDeviceInfos', 'sendDataToWXDevice', 'disconnectWXDevice', 'getWXDeviceTicket', 'connectWXDevice',
                ]
            ];

            $result =['status' => 'success', 'config'=>json_encode($config,JSON_UNESCAPED_UNICODE),'option'=>json_encode($option,JSON_UNESCAPED_UNICODE)];
            */
            if ($result['status'] == 'success') {
                if((!empty($isxcx)&&$isxcx=='1')){
                    echo json_encode(['weixin'=>$result,'pay'=>$orderpay,'status'=>'success']);
                }else{
                    $this->assign("weixin",$result);
                    return $this->fetch('pay/paycenter');
                }

            } else {
                $this->getErrorPage($result['message']);

            }
            }catch (Exception $e){
                $this->getErrorPage($e->getMessage());
            }
        }else{
            if($order['status']==2){
                $message="订单已付款待发货";
            }else if($order['status']==3){
                $message="订单待收货";
            }else if($order['status']==4){
                $message="交易完成";
            }
            $order['orderstatus']=$message;
            $this->assign("order",$order);
            return $this->fetch('pay/paymessage');
        }
    }




    public function payResult()
    {
        $trade_no= $this->request->request("trade_no");
        $result = queryOrder($trade_no);
        echo json_encode($result);
    }

}