<?php

/**
 * error code 说明.
 * <ul>
 *    <li>-41001: encodingAesKey 非法</li>
 *    <li>-41003: aes 解密失败</li>
 *    <li>-41004: 解密后得到的buffer非法</li>
 *    <li>-41005: base64加密失败</li>
 *    <li>-41016: base64解密失败</li>
 * </ul>
 */
class ErrorCode
{
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    public static $RequestTokenFailed = -41005;
    public static $SignNotMatch = -41006;
    public static $EncryptDataNotMatch = -41007;


    public static $errCode = [
        '0'     => '处理成功',
        '40001' => '校验签名失败',
        '40002' => '解析xml失败',
        '40003' => '计算签名失败',
        '40004' => '不合法的AESKey',
        '40005' => '校验AppID失败',
        '40006' => 'AES加密失败',
        '40007' => 'AES解密失败',
        '40008' => '公众平台发送的xml不合法',
        '40009' => 'Base64编码失败',
        '40010' => 'Base64解码失败',
        '40011' => '公众帐号生成回包xml失败',
    ];

    /**
     * 获取错误消息内容
     * @param string $code 错误代码
     * @return bool
     */
    public static function getErrText($code)
    {
        if (isset(self::$errCode[$code])) {
            return self::$errCode[$code];
        }
        return false;
    }

    public static function  getErrInfo($code){
        return ['code'=>$code, 'message'=>self::getErrText($code)];
    }
}