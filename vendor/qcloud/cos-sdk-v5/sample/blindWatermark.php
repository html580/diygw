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
    $blindWatermarkTemplate = new Qcloud\Cos\ImageParamTemplate\BlindWatermarkTemplate();
    $blindWatermarkTemplate->setImage("http://examplebucket-125000000.cos.ap-beijing.myqcloud.com/shuiyin.jpeg");
    $blindWatermarkTemplate->setType(2);
    $blindWatermarkTemplate->setLevel(3);

    // -------------------- 1. 下载时处理 -------------------- //
    $result = $cosClient->getObject(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Key' => 'exampleobject',
        'ImageHandleParam' => $blindWatermarkTemplate->queryString(),
        'SaveAs' => '/data/exampleobject'
    ));
    // 请求成功
    print_r($result);
    // -------------------- 1. 下载时处理 -------------------- //

    // -------------------- 2. 上传时处理 -------------------- //
    $local_path = "/data/exampleobject";
    $picOperationsTemplate = new Qcloud\Cos\ImageParamTemplate\PicOperationsTransformation();
    $picOperationsTemplate->setIsPicInfo(1);
    $picOperationsTemplate->addRule($blindWatermarkTemplate, "resultobject");
    $result = $cosClient->putObject(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Key' => 'exampleobject',
        'Body' => fopen($local_path, 'rb'),
        'PicOperations' => $picOperationsTemplate->queryString(),
    ));
    // 请求成功
    print_r($result);
    // -------------------- 2. 上传时处理 -------------------- //

    // -------------------- 3. 云上数据处理 -------------------- //
    $result = $cosClient->ImageProcess(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Key' => 'exampleobject',
        'PicOperations' => $picOperationsTemplate->queryString(),
    ));
    // 请求成功
    print_r($result);
    // -------------------- 3. 云上数据处理 -------------------- //
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}
