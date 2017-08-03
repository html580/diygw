<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络   <http://www.TwoThink.cn>
// +----------------------------------------------------------------------

namespace app\install\controller;

class Index extends \think\Controller{
    //安装首页
    public function index(){
    	session('update',null);
        if(is_file(APP_PATH . 'database.php')){
            // 已经安装过了 执行更新程序
            session('update',true);
            $msg = '请删除install.lock文件后再运行升级!';
        }else{
            $msg = '已经成功安装了DiyGw，请不要重复安装!';
        }

        if(is_file('./static/data/install.lock')){
            $this->error($msg);
        }
        return $this->fetch();
    }

    //安装完成
    public function complete(){
        $step = session('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("Install/step{$step}");
        }

        // 写入安装锁定文件 
        file_put_contents('./static/data/install.lock', 'lock'); 
        if(!session('update')){
            //创建配置文件
            $this->assign('info',session('config_file'));
        }
        session('step', null);
        session('error', null);
        session('update',null);
        return $this->fetch();
    }
}
