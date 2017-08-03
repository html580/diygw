<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络 <82550565@qq.com>
// +----------------------------------------------------------------------

namespace addons\editorforadmin\controller;
use app\home\controller\Addons;

class Upload extends Addons{

	public $uploader = null;

    public function upload($is_single_file=TRUE){
        // 获取表单上传文件
        //返回一个二维数组: ['fullpath'=>'','imagFile'=>['savepath'=>'','savename'='']]
        \Think\Config::load(APP_PATH.'admin/config.php');
            session('upload_error', null);
        $files = request()->file();
            $root_url= config('editor_upload.rootPath');
            $return= array();

        foreach($files as $key=>$file){
            //接收上传的文件
            $info = $file->move($root_url);
            if($info){
                // 成功上传后 获取上传信息
                $url =  __ROOT__.$info->getPath();
                $url = str_replace('./', '/', $url);
                $savepath= basename($url);
                $savename = $info->getFilename();
                $fullpath= __ROOT__.$url.'/'.$savename;
                $return[$key]['imgFile']['name']=$info->getInfo('name');//原文件名
                $return[$key]['imgFile']['savepath']=$savepath;
                $return[$key]['imgFile']['savename']=$savename;
                $return[$key]['fullpath'] = $fullpath;
                if($is_single_file) return $return[$key];//如果是单个文件上传的话
            }else{
                // 上传失败获取错误信息
                session('upload_error', $file->getError());
            }
        }
        return $return;
    }

	//keditor编辑器上传图片处理
	public function ke_upimg(){
		/* 返回标准数据 */
		$return  = array('error' => 0, 'info' => '上传成功', 'data' => '');
		$img = $this->upload();
		/* 记录附件信息 */
		if($img){
			$return['url'] = $img['fullpath'];
			unset($return['info'], $return['data']);
		} else {
			$return['error'] = 1;
			$return['message']   = session('upload_error');
		}

		/* 返回JSON数据 */
		return json($return);
	}

	//ueditor编辑器上传图片处理
	public function ue_upimg(){
		$img = $this->upload();
		$return = array();
		$return['url'] = $img['fullpath'];
		$title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
		$return['title'] = $title;
		$return['original'] = $img['imgFile']['name'];
		$return['state'] = ($img)? 'SUCCESS' : session('upload_error');
		/* 返回JSON数据 */
		return json($return);
	}

}
