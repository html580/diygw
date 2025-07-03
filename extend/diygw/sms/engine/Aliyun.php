<?php
declare (strict_types=1);

namespace diygw\sms\engine;

use diygw\sms\package\aliyun\SignatureHelper;

/**
 * 阿里云短信模块引擎
 * Class Aliyun
 * @package diygw\sms\engine
 */
class Aliyun extends Server
{
    private $config;

    /**
     * 构造方法
     * Qiniu constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 发送短信通知
     * @param array $sceneConfig 场景配置
     * @param array $templateParams 短信模板参数
     * @return bool
     */
    public function sendSms(array $sceneConfig, array $templateParams)
    {
        $params = [];
        // *** 需用户填写部分 ***

        // 必填: 短信接收号码
        $params["PhoneNumbers"] = $sceneConfig['acceptPhone'];

        // 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $this->config['sign'];

        // 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $sceneConfig['templateCode'];

        // 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $templateParams;

        // 可选: 设置发送短信流水号
        // $params['OutId'] = "12345";

        // 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        // $params['SmsUpExtendCode'] = "1234567";

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params['TemplateParam'])) {
            $params['TemplateParam'] = helper::jsonEncode($params['TemplateParam']);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper;

        // 此处可能会抛出异常，注意catch
        $response = $helper->request(
            $this->config['AccessKeyId']
            , $this->config['AccessKeySecret']
            , "dysmsapi.aliyuncs.com"
            , array_merge($params, [
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ])
            // 选填: 启用https
            , true
        );
        // 记录日志
        log_record([
            'name' => '发送短信',
            'config' => $this->config,
            'params' => $params
        ]);
        log_record($response);
        $this->error = $response->Message;
        return $response->Code === 'OK';
    }

}
