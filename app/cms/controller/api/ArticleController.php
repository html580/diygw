<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2024 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\cms\controller\api;

use app\BaseController;

/**
 * @package app\cms\controller\api
 */
class ArticleController extends BaseController
{
    //是否显示所有数据
    public $isAll = false;
    //是否初始化模型
    public $isModel = true;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法 API前端调用默认不允许新增 只允许获取数据
    public $notNeedLogin = ['list','get'];

}
