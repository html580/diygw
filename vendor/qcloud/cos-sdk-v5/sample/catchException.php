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
    $result = $cosClient->getBucketAcl(array(
        'Bucket' => 'examplebucket-125000000' //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
    ));
    // 请求成功
    print_r($result);
} catch (\Exception $e) {
    // 请求失败
    $statusCode = $e->getStatusCode(); // 获取错误码
    $errorMessage = $e->getMessage(); // 获取错误信息
    $requestId = $e->getRequestId(); // 获取错误的requestId
    $errorCode = $e->getCosErrorCode(); // 获取错误名称
    $request = $e->getRequest(); // 获取完整的请求
    $response = $e->getResponse(); // 获取完整的响应
}
