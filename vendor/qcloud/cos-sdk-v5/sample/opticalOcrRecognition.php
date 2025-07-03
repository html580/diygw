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
    // -------------------- 1. 通用文字识别 原图存储在COS -------------------- //
    $result = $cosClient->opticalOcrRecognition(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Key' => 'test.jpg',
//        'Type' => 'general',
//        'LanguageType' => 'zh',
//        'IsPDF' => 'true',
//        'PdfPageNumber' => 2,
//        'IsWord' => 'true',
//        'EnableWordPolygon' => 'false',
    ));
    // 请求成功
    print_r($result);
    // -------------------- 1. 通用文字识别 原图存储在COS -------------------- //

    // -------------------- 2. 通用文字识别 原图来自其他链接 -------------------- //
    $result = $cosClient->opticalOcrRecognition(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Key' => '', // 该值为空即可
        'DetectUrl' => 'https://www.xxx.com/xxx.jpg',
//        'Type' => 'general',
//        'LanguageType' => 'zh',
//        'IsPDF' => 'true',
//        'PdfPageNumber' => 2,
//        'IsWord' => 'true',
//        'EnableWordPolygon' => 'false',
    ));
    // 请求成功
    print_r($result);
    // -------------------- 2. 通用文字识别 原图来自其他链接 -------------------- //
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}
