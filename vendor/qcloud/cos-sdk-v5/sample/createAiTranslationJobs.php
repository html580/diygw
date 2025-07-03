<?php

require dirname(__FILE__, 2) . '/vendor/autoload.php';

$secretId = "SECRETID"; //替换为用户的 secretId，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
$secretKey = "SECRETKEY"; //替换为用户的 secretKey，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
$region = "ap-beijing"; //替换为用户的 region，已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'scheme' => 'https', // 万象接口必须使用https
        'credentials'=> array(
            'secretId'  => $secretId,
            'secretKey' => $secretKey)));
try {
    // 提交一个翻译任务 https://cloud.tencent.com/document/product/460/84799
    $result = $cosClient->createAiTranslationJobs(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Tag' => 'Translation',
        'Input' => array(
            'Object' => 'test.docx',
            'Lang' => 'zh',
            'Type' => 'docx',
//            'BasicType' => '',
        ),
        'Operation' => array(
//            'UserData' => 'xxx',
//            'JobLevel' => '',
//            'NoNeedOutput' => 'true',
            'Translation' => array(
                'Lang' => 'en',
                'Type' => 'docx',
            ),
            'Output' => array(
                'Region' => $region,
                'Bucket' => 'examplebucket-125000000',
                'Object' => 'xxx.txt',
            ),
        ),
//        'CallBack' => '',
//        'CallBackFormat' => '',
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
