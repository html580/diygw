<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2022 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\sys\controller;

use app\BaseController;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\controller
 */
class ApiController extends BaseController
{
    //是否初始化模型
    public $isModel = true;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];
    //是否返回所有数据
    public $isAll = true;

}
