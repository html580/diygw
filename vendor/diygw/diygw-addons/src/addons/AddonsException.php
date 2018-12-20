<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace think\addons;

use think\Exception;

/**
 * 插件异常处理类
 * @package think\addons
 */
class AddonsException extends Exception
{

    public function __construct($message, $code, $data = '')
    {
        $this->message  = $message;
        $this->code     = $code;
        $this->data     = $data;
    }

}