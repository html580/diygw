# think-wxminihelper
基于ThinkPHP5.0的微信小程序composer包。 


你只需要两部就可以实现微信的登录验证，验证成功后会自动返回一个3rd_session，和用户的基本信息

> 这里要注意的是，获取3rd_session因为微信官网建议Linux下面读取/dev/urandom设备,
所以这段代码只适合用于linux系统，但是如果你需要在Windows系统上测试，那么可以修改
WXLoginHelper下的randomFromDev方法

1.在config.php配置文件中加入必须的配置

```
// wx配置
'wx'  => [
    'url' => 'https://api.weixin.qq.com/sns/jscode2session',
    'appid' => 'wxde3ea15f3a18f7f6',
    'secret' => '53b1a4e12b88d78f3bcc2786fb72adcf',
    'grant_type' => 'authorization_code'
]
```

2.使用```checkLogin```进行验证

```
$code = input("code", '', 'htmlspecialchars_decode');
$rawData = input("rawData", '', 'htmlspecialchars_decode');
$signature = input("signature", '', 'htmlspecialchars_decode');
$encryptedData = input("encryptedData", '', 'htmlspecialchars_decode');
$iv = input("iv", '', 'htmlspecialchars_decode');

$wxHelper = new WXLoginHelper();
$data = $wxHelper->checkLogin($code, $rawData, $signature, $encryptedData, $iv);
```


当然，如果你自己有一套登录验证流程，你也可以使用```decryptData```进行解密验证前面的正确性

```php
$appid = 'wx4f4bc4dec97d474b';
$sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

$encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

$iv = 'r7BXXKkLb8qrSNn05n0qiA==';

$pc = new \think\wxmini\WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}

```
