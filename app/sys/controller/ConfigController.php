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
use app\sys\model\ConfigModel;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\controller
 */
class ConfigController extends BaseController
{
    //是否显示所有数据
    public $isAll = false;
    //是否初始化模型
    public $isModel = true;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];



}
