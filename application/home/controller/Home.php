<?php


namespace app\home\controller;
use think\Controller;


/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class Home extends Controller {
	public function __construct(){
		/* 读取站点配置 */
		$config = api('Config/lists');$config['home_view_path']='default';
		$config ['template']['taglib_pre_load'] =   'app\common\taglib\Think,app\common\taglib\Article';
		$config ['template']['view_path'] = APP_PATH.'home/view/'.$config['home_view_path'].'/';
		config($config); //添加配置
		parent::__construct();
	}

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}


    protected function _initialize(){
        if(!config('web_site_close')){
            $this->error('站点已经关闭，请稍后访问~');
        }
    }
}
