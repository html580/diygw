<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

use service\DataService;
use service\NodeService;
use think\Db;

/**
 * 生成随机字符串
 * @param $length int 字符串长度
 * @return $str string 随机字符串
 */
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

    return $str;
}

/**
 * 生成基于MD5随机字符串
 * @param  string $namespace 字符串前缀
 * @return string           随机字符串
 */
function create_guid($namespace = '') {
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = date('Ymdhis',time()).substr($hash, 0, 8);
    return $guid;
}

/**
 * 打印输出数据到文件
 * @param mixed $data 输出的数据
 * @param bool $force 强制替换
 * @param string|null $file
 */
function p($data, $force = false, $file = null)
{
    is_null($file) && $file = env('runtime_path') . date('Ymd') . '.txt';
    $str = (is_string($data) ? $data : (is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true)) . PHP_EOL;
    $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
}

/**
 * RBAC节点权限验证
 * @param string $node
 * @return bool
 */
function auth($node)
{
    return NodeService::checkAuthNode($node);
}

/**
 * 设备或配置系统参数
 * @param string $name 参数名称
 * @param bool $value 默认是null为获取值，否则为更新
 * @return string|bool
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function sysconf($name, $value = null)
{
    static $config = [];
    if ($value !== null) {
        list($config, $data) = [[], ['name' => $name, 'value' => $value]];
        return DataService::save('SystemConfig', $data, 'name');
    }
    if (empty($config)) {
        $config = Db::name('SystemConfig')->column('name,value');
    }
    return isset($config[$name]) ? $config[$name] : '';
}

/**
 * 日期格式标准输出
 * @param string $datetime 输入日期
 * @param string $format 输出格式
 * @return false|string
 */
function format_datetime($datetime, $format = 'Y年m月d日 H:i:s')
{
    if(is_int($datetime)){
        return date($format, $datetime);
    }else{
        return date($format, strtotime($datetime));
    }

}

/**
 * UTF8字符串加密
 * @param string $string
 * @return string
 */
function encode($string)
{
    list($chars, $length) = ['', strlen($string = iconv('utf-8', 'gbk', $string))];
    for ($i = 0; $i < $length; $i++) {
        $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
    }
    return $chars;
}

/**
 * UTF8字符串解密
 * @param string $string
 * @return string
 */
function decode($string)
{
    $chars = '';
    foreach (str_split($string, 2) as $char) {
        $chars .= chr(intval(base_convert($char, 36, 10)));
    }
    return iconv('gbk', 'utf-8', $chars);
}

/**
 * 下载远程文件到本地
 * @param string $url 远程图片地址
 * @return string
 */
function local_image($url)
{
    return \service\FileService::download($url)['url'];
}


/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list,$field, $sortby='asc') {
    if(is_array($list)){
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ( $refer as $key=> $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

function  addons_hook($name,$field = true){
    $data = \think\Db::name('Hooks')->cache(false)->field($field)->where(['addons'=>['like','%'.$name.'%']])->find();
    return $data;
}



/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author DIY官网  <diygw.cn>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author DIY官网  <diygw.cn>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}

function aesEncrypt($data){
    $privateKey = "diygwcomdiygwcom";
    $iv    = "diygwcomdiygwcom";
    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
    return base64_encode($encrypted);
}

function aesDecrypt($data){
    $privateKey = "diygwcomdiygwcom";
    $iv    = "diygwcomdiygwcom";
    $encryptedData = base64_decode($data);
    $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
    return $decrypted;
}

function get_server_ip()
{
    if (isset($_SERVER['SERVER_NAME'])) {
        return gethostbyname($_SERVER['SERVER_NAME']);
    } else {
        if (isset($_SERVER)) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } elseif (isset($_SERVER['LOCAL_ADDR'])) {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip ? $server_ip : '获取不到服务器IP';
    }
}
function getTableName($tablename){
    $prefix = config('database.prefix');
    return $prefix.$tablename;
}


function addPaymentData($openid='',$member_id = '', $mid = '',$dashboard_id='', $money = '', $title = '', $attach = '', $pay_type = '1', $remark = '',$trade_no='',$from_addon=''){
    $data['member_id'] = $member_id;
    $data['dashboard_id'] = $dashboard_id;
    $data['openid'] = $openid;
    $data['mpid'] = $mid;
    $data['money'] = $money;
    $data['title'] = $title;
    $data['pay_type'] = $pay_type;
    $data['remark'] = $remark;
    $data['attach'] = $attach;
    $data['from_addon'] = $from_addon;
    $data['create_time'] = time();
    $data['trade_no'] = $trade_no;
    $data['status'] = 1;
    $id = \think\Db::name('Payment')->insertGetId($data);
    $data['id']=$id;
    if ($id)
        return $data;
    else
        return false;
}

function getWechatInfo($mpid){
    $wechatInfo = Db::name('wechat')->where(['id' => $mpid])->find();
    if(empty($wechatInfo)){
        throw new Exception("请配置微信相关配置", '0');
    }
    return $wechatInfo;
}

function getWechatXcxInfo($mpid){
    $result = Db::name('WechatConfig')->where(['name' => 'wxmin', 'mpid' => $mpid])->find();
    if(empty($result)){
        throw new Exception("请配置微信小程序相关配置", '0');
    }
    $array = json_decode($result['value'], true);
    return $array;
}

function getWechatPayInfo($mpid){
    $result = Db::name('WechatConfig')->where(['name' => 'wxpay', 'mpid' => $mpid])->find();
    if(empty($result)){
        throw new Exception("请配置微信相关配置", '0');
    }
    $array = json_decode($result['value'], true);
    return $array;
}


function getPayConfig($mpid,$isxcx){

    $wechatInfo = getWechatInfo($mpid);
    if((!empty($isxcx)&&$isxcx=='1')){
        $wechatXcxInfo  = getWechatPayInfo($mpid);
        $wechatInfo['appid'] = $wechatXcxInfo['appid'];
        $wechatInfo['appsecret'] = $wechatXcxInfo['appsecret'];
    }
    $wechatPayInfo  = getWechatPayInfo($mpid);
    if(empty($wechatInfo) || empty($wechatPayInfo)){
        return null;
    }
    $config = [
        'token'          => $wechatInfo['valid_token'],
        'appid'          => $wechatInfo['appid'],
        'appsecret'      => $wechatInfo['appsecret'],
        'encodingaeskey' => $wechatInfo['encodingaeskey'],
        'mch_id'         => $wechatPayInfo['mchid'],
        'mch_key'        => $wechatPayInfo['paysignkey'],
        'ssl_cer'        => $wechatPayInfo['apiclient_cert'],
        'ssl_key'        => $wechatPayInfo['apiclient_key']
    ];
    return $config;
}

/* 定义logger来写日志 用来调试 */
function logger($content){
    $logSize = 100000; //日志大小
    // $log = "log.txt";
    $log = RUNTIME_PATH."/log/log.txt";
    if(strtoupper(substr(PHP_OS,0,3))==='WIN'){
        $log = RUNTIME_PATH."/log/log.txt";
    }
    if(file_exists($log) && filesize($log) > $logSize){
        unlink($log);
    }
    // linux的换行是 \n  windows是 \r\n
    // FILE_APPEND 不写第三个参数默认是覆盖，写的话是追加
    file_put_contents($log,date('H:i:s')."\n".$content."\n",FILE_APPEND);
}
/**
 * 微信支付函数
 * @param  $parment_id 定单id
 * @author LK <diygwcom@foxmail.com>
 * @param int $parment_id

 * @return bool|json数据，可直接填入js函数作为参数
 */
function payByWexinJsApi($parment_id = '',$isxcx)
{
    $payment = \think\Db::name('Payment')->where('payment_id',$parment_id)->find();
    if (empty($payment)) {
        return ['status' =>'error', 'message' => '订单不存在'];
    }
    // 生成预支付码

        $mpid = $payment['mpid'];
        $config = getPayConfig($mpid,$isxcx);
        $wechat = new WeChat\Pay($config);

        $options = [
            'body'             => $payment['title'],
            'out_trade_no'     => $payment['trade_no'],
            'total_fee'        => $payment['money'] * 100,
            'openid'           => $payment['openid'],
            'trade_type'       => 'JSAPI',
            'notify_url'       => url('@wechat/pay/notify', '', true, true),
            'spbill_create_ip' => app('request')->ip(),
        ];
        $result = $wechat->createOrder($options);

        // 创建JSAPI参数签名
        $options = $wechat->createParamsForJsApi($result['prepay_id']);
        $optionJSON = json_encode($options, JSON_UNESCAPED_UNICODE);

        // JSSDK 签名配置
        $configJSON = json_encode(service\WechatService::webJsSDK(), JSON_UNESCAPED_UNICODE);
        return ['status' =>'success', 'config'=>$configJSON,'option'=>$optionJSON];

}


/**
 * @author LK <diygwcom@foxmail.com>
 * @param string $trade_no 订单号
 * @return array status ok: 成功 -1：失败
 */
function queryOrder($trade_no = '')
{
    $payment =  Db::name("payment")->where('trade_no',$trade_no)->find();
    if (!$payment) {
        return ['status' =>'error', 'message' => '订单不存在'];
    }
    $config = getPayConfig($payment['mpid']);
    if ($config) {

        $wechat = new WeChat\Pay($config);
        $result = $wechat->queryOrder(['out_trade_no'=>$trade_no]);
        try{
            if (!empty($result)) {
                if (isset($result['trade_state']) && $result['trade_state'] == 'SUCCESS') {//已经支付
                    if ($payment['status'] == '1') {//订单状态未处理为成功
                        Db::name("payment")->where('trade_no',$trade_no)->update(['status' =>2]);
                        if(!empty($payment['from_addon'])){
                            $addons = explode(",",$payment['from_addon']);
                            foreach ($addons as $addon){
                                \think\Db::name($addon)->where(['trade_no' => $trade_no])->update(['status' =>2]);
                            }
                        }
                        return ['status' =>'success', 'message' => '交易完成','payment'=>$payment];
                    } else {
                        return ['status' =>'success', 'message' => '交易完成','payment'=>$payment];
                    }
                } else {
                    return ['status' =>'error', 'message' => '未完成交易','payment'=>$payment];
                }
            } else {
                return ['status' =>'error', 'message' => '订单不存在'];
            }
        }catch (Exception $e){
            return ['status' =>'error', 'message' =>$e->getMessage()];
        }
    }
    return ['status' =>'error', 'message' => '没有公众号配置信息'];
}

function refundOrder($trade_no = '')
{

    $payment =  Db::name("payment")->where('trade_no',$trade_no)->find();
    if (!$payment) {
        return ['status' =>'error', 'message' => '订单不存在'];
    }
    $config = getPayConfig($payment['mpid']);
    if ($config) {

        $wechat = new WeChat\Pay($config);
        $out_refund_no=create_guid();
        $result = $wechat->createRefund(['out_trade_no'=>$trade_no,'out_refund_no'  =>create_guid(),'total_fee' => $payment['money'] * 100,'refund_fee'=>$payment['money'] * 100]);
        try{
            if (isset($result['return_code'])) {
                if ($result['return_code'] == 'SUCCESS') {
                    if($result['result_code']=="FAIL"){
                        return ['status' =>'error', 'message' => $result['err_code_des']];
                    }else{
                        Db::name("payment")->where('trade_no',$trade_no)->update(['status' =>5,'refund'=>$out_refund_no]);
                        if(!empty($payment['from_addon'])){
                            $addons = explode(",",$payment['from_addon']);
                            foreach ($addons as $addon){
                                \think\Db::name($addon)->where(['trade_no' => $trade_no])->update(['status' =>5]);
                            }
                        }
                        return ['status' =>'success', 'message' => '退款成功'];
                    }

                } else {
                    return ['status' =>'error', 'message' => $result['return_code'] . $result['return_msg']];
                }
            }
        }catch (Exception $e){
            return ['status' =>'error', 'message' =>$e->getMessage()];
        }

    }
    return ['status' =>'error', 'message' => '没有公众号配置信息'];

}
