<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +---------------------------------------------------------------------- 

namespace app\admin\controller;  
/**
 * 后台首页控制器
 * @author 艺品网络  <twothink.cn>
 */
class Index extends Admin  {

    /**
     * 后台首页
     * @author 艺品网络  <twothink.cn>
     */
    public function index(){ 
        $this->assign('meta_title','管理首页') ;
        return $this->fetch();
    }

    //跳转页面
    public function page(){
        $dashboardid = input("dashboardid", '');
        $page = input("page", '');
        return $this->fetch("/diygw/".$dashboardid."/".$page);
    }

    //系统菜单
    public function system(){
        $this->assign('meta_title','应用管理');
        return $this->fetch();
    }

    //系统菜单
    public function subsystem(){
        $this->assign('meta_title','应用管理');
        return $this->fetch();
    }
}
