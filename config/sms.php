<?php

return [
    // 存储配置信息
    'default' => 'qcloud',
    'templates' => [
        //登录模板
        'login' => '',
    ],
    // 短信服务渠道
    'gateways' => [
        // 阿里云
        'aliyun' => [
            'name' => '阿里云短信',
            'website' => 'https://dysms.console.aliyun.com/dysms.htm',
            'AccessKeyId' => '',
            'AccessKeySecret' => '',
            'sign' => ''   // 短信签名
        ],
        // 腾讯云
        'qcloud' => [
            'name' => '腾讯云短信',
            'website' => 'https://console.cloud.tencent.com/smsv2',
            'SdkAppID' => '',
            'AccessKeyId' => '',
            'AccessKeySecret' => '',
            'sign' => ''   // 短信签名
        ],
        // 七牛云
        'qiniu' => [
            'name' => '七牛云短信',
            'website' => 'https://portal.qiniu.com/sms/dashboard',
            'AccessKey' => '',
            'SecretKey' => '',
            'sign' => ''   // 短信签名
        ],
    ]
];
