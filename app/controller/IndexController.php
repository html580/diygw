<?php
namespace app\controller;

use app\BaseController;

class IndexController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = true;
    public $isModel = false;

    public function index()
    {
        return $this->fetch();
    }
}
