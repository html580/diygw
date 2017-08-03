<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace addons\sitestat;
use app\common\controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */
class Sitestat extends Addon{

    public $info = array(
        'name'=>'sitestat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'status'=>1,
        'author'=>'thinkphp',
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
            $info['user']		=	\think\Db::name('Member')->count();
            $info['action']		=	\think\Db::name('ActionLog')->count();
            $info['document']	=	\think\Db::name('Document')->count();
            $info['category']	=	\think\Db::name('Category')->count();
            $info['model']		=	\think\Db::name('Model')->count();
            $this->assign('info',$info);
            return $this->fetch('info');
        }
    }
}