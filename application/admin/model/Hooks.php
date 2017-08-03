<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络 
// +----------------------------------------------------------------------

namespace app\admin\model;
use think\Model;

/**
 * 插件模型
 * @author 艺品网络  <twothink.cn>
 * @date    2013-08-14 11:31:21
 */

class Hooks extends Model {  
	protected $autoWriteTimestamp = false;
    /**
     * 文件模型自动完成
     * @var array
     */
    protected $auto = ['update_time']; 
    protected function setUpdateTimeAttr($value)
    {
        return time();
    }

    /**
     * 更新插件里的所有钩子对应的插件
     */
    public function updateHooks($addons_name){
        $addons_class = get_addon_class($addons_name);//获取插件名
        if(!class_exists($addons_class)){
            $this->error = "未实现{$addons_name}插件的入口文件";
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->column('name');
        $common = array_intersect($hooks, $methods);
        if(!empty($common)){
            foreach ($common as $hook) {
                $flag = $this->updateAddons($hook, array($addons_name));
                if(false === $flag){
                    $this->removeHooks($addons_name);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 更新单个钩子处的插件
     */
    public function updateAddons($hook_name, $addons_name){
        $o_addons = $this->where("name='{$hook_name}'")->value('addons');
        if($o_addons)
            $o_addons = str2arr($o_addons);
        if($o_addons){
            $addons = array_merge($o_addons, $addons_name);
            $addons = array_unique($addons);
        }else{
            $addons = $addons_name;
        }
        $flag = model('Hooks')->where("name='{$hook_name}'")
        ->setField('addons',arr2str($addons));
        if(false === $flag)
            model('Hooks')->where("name='{$hook_name}'")->setField('addons',arr2str($o_addons));
        return $flag;
    }

    /**
     * 去除插件所有钩子里对应的插件数据
     */
    public function removeHooks($addons_name){
        $addons_class = get_addon_class($addons_name);
        if(!class_exists($addons_class)){
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->column('name');
        $common = array_intersect($hooks, $methods);
        if($common){
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if(false === $flag){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 去除单个钩子里对应的插件数据
     */
    public function removeAddons($hook_name, $addons_name){
        $o_addons = $this->where("name='{$hook_name}'")->value('addons');
        $o_addons = str2arr($o_addons);
        if($o_addons){
            $addons = array_diff($o_addons, $addons_name);
        }else{
            return true;
        }
        $flag = model('Hooks')->where("name='{$hook_name}'")
                          ->setField('addons',arr2str($addons));
        if(false === $flag)
            model('Hooks')->where("name='{$hook_name}'")
                      ->setField('addons',arr2str($o_addons));
        return $flag;
    }
}