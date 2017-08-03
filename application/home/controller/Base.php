<?php


namespace app\home\controller;


/**
 * 前台用户后台公共控制器
 */
class Base extends Home {
    protected function _initialize(){
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', url('Login/index'));
        parent::_initialize();
    }

}
