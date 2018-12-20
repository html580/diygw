<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@foxmail.com>
// +----------------------------------------------------------------------
namespace addons\sitestat;

use think\Db;
/**
 * 系统环境信息插件
 */
class Sitestat extends \think\Addons {

    public $info = array(
        'name'=>'sitestat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'status'=>1,
        'author'=>'diygw',
        'version'=>'0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
        $config = $this->getConfig(); 
        $this->assign('addons_config', $config);
        if($config['display']){
            $info['user']		=	10;
            $info['action']		=	10;
            $info['document']	=	10;
            $info['category']	=	10;
            $info['model']		=	10;
            $this->assign('info',$info);
            return $this->fetch('info');
        }
    }
}