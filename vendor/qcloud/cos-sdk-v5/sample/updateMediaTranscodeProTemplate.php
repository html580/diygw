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
    // 更新音视频转码 pro 模板 https://cloud.tencent.com/document/product/460/84753
    $result = $cosClient->updateMediaTranscodeProTemplate(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Key' => '', // TemplateId
        'Tag' => 'TranscodePro',
        'Name' => 'media-transcode-pro-name',
        'Container' => array(
            'Format' => 'mxf',
        ),
        'Video' => array(
            'Codec' => 'xavc',
            'Profile' => 'XAVC-HD_intra_420_10bit_class50',
            'Width' => '1440',
            'Height' => '1080',
            'Interlaced' => 'true',
            'Fps' => '30000/1001',
            'Bitrate' => '',
            'Rotate' => '',
        ),
        'TimeInterval' => array(
            'Start' => '',
            'Duration' => '',
        ),
        'Audio' => array(
            'Codec' => '',
            'Remove' => '',
        ),
        'TransConfig' => array(
            'AdjDarMethod' => '',
            'IsCheckReso' => '',
            'ResoAdjMethod' => '',
            'IsCheckVideoBitrate' => '',
            'VideoBitrateAdjMethod' => '',
            'IsCheckAudioBitrate' => '',
            'AudioBitrateAdjMethod' => '',
            'IsCheckVideoFps' => '',
            'VideoFpsAdjMethod' => '',
            'DeleteMetadata' => '',
            'IsHdr2Sdr' => '',
        ),
    ));
    // 请求成功
    print_r($result);
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}
