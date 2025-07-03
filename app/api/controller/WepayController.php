<?php
namespace app\api\controller;
use app\BaseController;

use app\diy\model\OrderModel;
use app\diy\model\UserModel;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Message;
use think\App;


/*
 * 支付
 */
class WepayController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    public $isModel = false;
    //判断不需要登录的方法
    public $notNeedLogin = ['notify','test'];

    public $wepayApp;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

    }

    /**
     * 用户下单
     * @return \think\response\Json
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\exception\DbException
     */
    public function order(){
        $userModel = UserModel::where(['id'=>$this->request->userId])->find();
        if(!$userModel){
            return $this->error('请先登录'.$this->request->userId);
        }
        $user = $userModel->toArray();
        if(empty($user['openid'])){
            return $this->error('请先登录');
        }
        // 生成订单信息
        $data = $this->request->param();
        $data['orderNo'] = getOrderNo();
        $data['status'] = 0;
        $data['payStatus'] = 0;
        $data['openid'] = $user['openid'];
        $data['userId'] = $this->request->userId;
        $model = new OrderModel();
        $data = $model->add($data);
        $notify_url = url('api/wepay/notify')
            ->suffix('html')
            ->domain($this->request->domain())->build();
        $paymentConfig = config('wechat.payment');
        $this->wepayApp =  new Application($paymentConfig);
        //调起微信支付
        $payData = [
            "mchid" => $paymentConfig['mch_id'],
            "out_trade_no" => $data['orderNo'],
            "appid" => $paymentConfig['app_id'],
            "description" => $data['body'],
            "notify_url" => $notify_url, // 支付成功后回调通知URL
            "amount" => [
                "total" => (int)bcmul($data['total'],100),
                "currency" => "CNY",
            ],
            "payer" => [
                "openid" => $data['openid'],
            ],
        ];
        try {
            $response = $this->wepayApp->getClient()->postJson('v3/pay/transactions/jsapi', $payData);
            $result = $response->toArray(false);
            if (isset($result['prepay_id'])) {
                $utils = $this->wepayApp->getUtils();
                $config = $utils->buildMiniAppConfig($result['prepay_id'], $paymentConfig['app_id'], 'RSA'); // 返回数组
                return $this->successData($config);
            }else{
                $this->error("发起支付失败");
            }
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }

    }


    /**
     * 支付回调
     * @return mixed
     */
    public function notify(){

        $paymentConfig = config('wechat.payment');
        $this->wepayApp =  new Application($paymentConfig);
        $server = $this->wepayApp->getServer();


        $server->handlePaid(function (Message $message, \Closure $next) {
            // $message->out_trade_no 获取商户订单号
            // $message->payer['openid'] 获取支付者 openid
            $order = OrderModel::where(['order_no'=>$message->out_trade_no])->find();
            //如果状态已为1，直接返回
            if (!$order || $order['status'] == '1') {
                return $next($message);
            }
            $order['status'] = '1';
            $order->save();
            return $next($message);
        });
        return $server->serve();
    }


}
