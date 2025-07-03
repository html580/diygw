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
    // https://cloud.tencent.com/document/product/436/54045 搜索媒体处理队列
    $result = $cosClient->describeMediaQueues(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
//        'QueueIds' => '', // 可选 队列 ID，以“,”符号分割字符串
//        'Category' => 'Transcoding', // 可选 CateAll：所有类型；Transcoding：媒体处理队列；SpeedTranscoding：媒体处理倍速转码队列；默认为 Transcoding。
//        'State' => 'Paused', // 可选 1. Active 表示队列内的作业会被媒体转码服务调度转码执行 2. Paused 表示队列暂停，作业不再会被媒体转码调度转码执行，队列内的所有作业状态维持在暂停状态，已经处于转码中的任务将继续转码，不受影响
//        'PageNumber' => '1', // 可选 第几页
//        'PageSize' => '2', // 可选 每页个数
    ));
    // 请求成功
    print_r($result);
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}
