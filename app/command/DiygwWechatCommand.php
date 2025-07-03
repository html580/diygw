<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2022 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class DiygwWechatCommand extends Command
{
    protected function configure()
    {
        $this->setName('diygw:wechat')
            ->setDescription('设置微信配置文件');
    }

    protected function execute(Input $input, Output $output)
    {
        file_put_contents(config_path() . 'wechat.php', $this->config());

        $this->env();

        $output->warning('生成微信配置文件成功，请修改env下相关微信配置');
    }

    protected function config()
    {
        return <<<CONFIG
<?php

return [
        /**
         * 公众号配置
         *
         */
        'official_account' => [
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'        => env('wechat.official_app_id'),         // AppID
            'secret'        => env('wechat.official_secret'),     // AppSecret
            'token'         => env('wechat.official_token'),          // Token
            'aes_key'       => env('wechat.official_aes_key'),                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

            'response_type' => 'array',

            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            'oauth'         => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/examples/oauth_callback.php',
            ],
        ],

        /**
         * 小程序
         */
        'mini_program' => [
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'  =>  env('wechat.mini_app_id'),         // AppID
            'secret'  =>  env('wechat.mini_secret'),         // AppSecret
            'token'   =>  env('wechat.mini_token'),          // Token
            'aes_key' =>  env('wechat.mini_aes_key'),                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！
        
            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/examples/oauth_callback.php',
            ],
        ],
         
        /**
         * 开放平台
         */
         'open_platform' => [
         ],
         
        /**
         * 企业微信
         */
         'work' => [
         ],
         
        /**
         * 企业微信开放平台
         */
         'open_work' => [
         ],
         
        /**
         * wechat pay
         */
        'payment' => [
            'app_id'    => env('wechat.pay_app_id'),
            'mch_id'    => env('wechat.pay_mch_id'),
            // v3 API 秘钥
            'secret_key'       => env('wechat.pay_signature'),
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path' => env('wechat.pay_cert_path'), // XXX: 绝对路径！！！！
            'key_path'  => env('wechat.pay_key_path'),      // XXX: 绝对路径！！！！
            'notify_url' => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
        ],

        // 更多配置请查看 https://easywechat.com/
];
CONFIG;
    }

    protected function env()
    {
        $filename =  file_exists(root_path() . '.env') ? '.env' : '.example.env';


        $env = \parse_ini_file(root_path() . $filename, true);

        $env['WECHAT'] = $this->envConfig();

        $dotEnv = '';

        foreach ($env as $key => $e) {
            if (is_string($e)) {
                $dotEnv .= sprintf('%s = %s', $key, $e === '1' ? 'true' : ($e === '' ? 'false' : $e)) . PHP_EOL;
                $dotEnv .= PHP_EOL;
            } else {
                $dotEnv .= sprintf('[%s]', $key) . PHP_EOL;
                foreach ($e as $k => $v) {
                    $dotEnv .= sprintf('%s = %s', $k, $v === '1' ? 'true' : ($v === '' ? 'false' : $v)) . PHP_EOL;
                }

                $dotEnv .= PHP_EOL;
            }
        }

        file_put_contents(root_path() . '.env', $dotEnv);

    }


    protected function envConfig()
    {
        return [
            "official_app_id" => null,
            "official_secret" => null,
            "official_token" => null,
            "official_aes_key" => null,
            "mini_app_id" => null,
            "mini_secret" => null,
            "mini_token" => null,
            "min_aes_key" => null,
            "pay_app_id" => null,
            "pay_mch_id" => null,
            "pay_signature" => null,
        ];
    }
}