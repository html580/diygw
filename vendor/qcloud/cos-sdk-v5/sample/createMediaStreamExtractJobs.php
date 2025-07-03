<?php

require dirname(__FILE__, 2) . '/vendor/autoload.php';

$secretId = "SECRETID"; //替换为用户的 secretId，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
$secretKey = "SECRETKEY"; //替换为用户的 secretKey，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
$region = "ap-beijing"; //替换为用户的 region，已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'scheme' => 'https', //协议头部，默认为http
        'credentials'=> array(
            'secretId'  => $secretId,
            'secretKey' => $secretKey)));
try {
    // 提交一个音视频流分离任务 https://cloud.tencent.com/document/product/460/84787
    $result = $cosClient->createMediaStreamExtractJobs(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Tag' => 'StreamExtract',
        'Input' => array(
            'Object' => 'test.mp4',
        ),
        'Operation' => array(
//            'UserData' => 'xxx', // 透传用户信息
//            'JobLevel' => '0', // 任务优先级，级别限制：0 、1 、2。级别越大任务优先级越高，默认为0
            'Output' => array(
                'Bucket' => 'examplebucket-125000000',
                'Region' => $region,
                'StreamExtracts' => array(
                    array(
                        'Index' => '0',
                        'Object' => 'output/out0.mp4',
                    ),
                    array(
                        'Index' => '1',
                        'Object' => 'output/out1.mp4',
                    ),
                ),
            ),
        ),
        'CallBack' => 'http://xxx.com/callback',
        'CallBackFormat' => 'JSON',
//        'CallBackType' => '',
//        'CallBackMqConfig' => array(
//            'MqRegion' => '',
//            'MqMode' => '',
//            'MqName' => '',
//        ),
    ));
    // 请求成功
    print_r($result);
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}
