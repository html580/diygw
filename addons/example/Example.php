<?php
    
namespace addons\example;
use think\Addons;

/**
 * 示列插件
 * @author diygw.com
 */

class Example extends Addons{

    public $info = array(
        'name'=>'example',
        'title'=>'示列',
        'description'=>'这是一个测试例子',
        'status'=>0,
        'author'=>'diygw.com',
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
            return $this->fetch('widget');
        }
    }

}