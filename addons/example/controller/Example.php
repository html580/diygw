<?php

namespace addons\example\controller; 
use think\addons\Controller;

class Example extends Controller{
    public function index()
    {
        $this->assign("text","测试调用插件ACTION例子");
        return $this->fetch();
    }
}
