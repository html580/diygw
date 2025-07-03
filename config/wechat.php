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
            'secret'  =>  env('wechat.mini_secret'),     // AppSecret
        
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
            'certificate' => env('wechat.pay_cert_path'), // XXX: 绝对路径！！！！
            'private_key'  => env('wechat.pay_key_path'),      // XXX: 绝对路径！！！！

            'notify_url' => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
        ],

        // 更多配置请查看 https://easywechat.com/
];