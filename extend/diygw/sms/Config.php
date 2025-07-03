<?php
namespace diygw\sms;

use Overtrue\EasySms\Strategies\OrderStrategy;

/**
 * EasySms配置类
 * Class Config
 * @package diygw\sms
 */
class Config
{
    /**
     * 生成EasySms的配置项
     * @param array $smsConfig
     * @return array
     */
    public static function getEasySmsConfig(array $smsConfig): array
    {
        return [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            'options' =>[
                'verify' => false,
            ],
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => OrderStrategy::class,
                // 默认可用的发送网关
                'gateways' => [$smsConfig['default']],
            ],
            // 可用的网关配置
            'gateways' => [
                'aliyun' => [
                    'access_key_id' => $smsConfig['engine']['aliyun']['AccessKeyId'],
                    'access_key_secret' => $smsConfig['engine']['aliyun']['AccessKeySecret'],
                    'sign_name' => $smsConfig['engine']['aliyun']['sign'],
                ],
                'qcloud' => [
                    'sdk_app_id' => $smsConfig['engine']['qcloud']['SdkAppID'],
                    'secret_id' => $smsConfig['engine']['qcloud']['AccessKeyId'],
                    'secret_key' => $smsConfig['engine']['qcloud']['AccessKeySecret'],
                    'sign_name' => $smsConfig['engine']['qcloud']['sign'],
                ],
                'qiniu' => [
                    'access_key' => $smsConfig['engine']['qiniu']['AccessKey'],
                    'secret_key' => $smsConfig['engine']['qiniu']['SecretKey'],
                ],
            ]
        ];
    }
}