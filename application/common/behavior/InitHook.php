<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
namespace app\common\behavior;
 

// 初始化钩子信息
class InitHook{

    // 行为扩展的执行入口必须是run
    public function run(&$content){
	    // 获取系统配置
	    $data = \think\Config::get('app_debug') ? [] : cache('hooks');
	    if (empty($data)) {
	    	$hooks = \think\Db::name('Hooks')->column('name,addons');
	    	foreach ($hooks as $key => $value) {
	    		if($value){
	    			$map['status']  =   1;
	    			$names          =   explode(',',$value);
	    			$map['name']    =   array('IN',$names);
	    			$data = \think\Db::name('Addons')->where($map)->column('id,name'); 
	    			if($data){
	    				$addons_arr = array_intersect($names, $data); 
	    				$addons[$key] = array_map('get_addon_class',$addons_arr);
	    				\think\Hook::add($key,$addons[$key]);
	    			}
	    		}
	    	}
	    	cache('hooks',$addons);
	    } else {
	        \think\Hook::import($data, false);
	    }
    }
}