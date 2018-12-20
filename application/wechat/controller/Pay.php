<?php

// +----------------------------------------------------------------------
// | DiygwApp
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 DIY官网 [ http://www.diygw.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.diygw.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/html580/diygw
// +----------------------------------------------------------------------

namespace app\wechat\controller;

use think\Controller;
use app\wechat\service\FansService;
use app\wechat\service\MediaService;
use service\DataService;
use service\WechatService;
use think\Db;
use think\Exception;

/**
 * 支付接口控制器
 * Class Menu
 * @package app\wechat\controller
 * @author LK <diygwcom@foxmail.com>
 * @date 2018/11/15
 */
class Pay extends Controller{


    /**
     * 支付通知接收处理
     * @return string
     * @throws \WeChat\Exceptions\InvalidResponseException
     */
    public function notify()
    {

        $trade_no=$this->request->request("out_trade_no");
        $payment =  Db::name("payment")->where('trade_no',$trade_no)->find();
        if(!$payment){
            $xml = "<xml>
                    <return_code><![CDATA[FAIL]]></return_code>
                    <return_msg><![CDATA[订单不存在]]></return_msg>
                </xml>";
            echo $xml;
        }
        try{
            $config = getPayConfig($payment['mpid']);
            $wechat = new WeChat\Pay($config);
            $result = $wechat->getNotify();
            if($result['result_code']=='SUCCESS'){
                $result = queryOrder($trade_no);
                if($result['stauts']=='success'){
                    $xml = "<xml>
                        <return_code><![CDATA[SUCCESS]]></return_code>
                        <return_msg><![CDATA[支付成功]]></return_msg>
                    </xml>";
                    echo $xml;
                }else{
                    $xml = "<xml>
                        <return_code><![CDATA[SUCCESS]]></return_code>
                        <return_msg><![CDATA["+$result['message']+"]]></return_msg>
                    </xml>";
                    echo $xml;
                }
            }else{
                $xml = "<xml>
                        <return_code><![CDATA[FAIL]]></return_code>
                        <return_msg><![CDATA[支付失败]]></return_msg>
                    </xml>";
                echo $xml;
            }
        }catch (Exception $e){
            $xml = "<xml>
                        <return_code><![CDATA[FAIL]]></return_code>
                        <return_msg><![CDATA[签名不一致]]></return_msg>
                    </xml>";
            echo $xml;
        }
    }

}
