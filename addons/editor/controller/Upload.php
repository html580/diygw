<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace addons\editor\controller;
use app\home\controller\Addons;

class Upload extends Addons{

	public $uploader = null;

	/**
	 *上传图片
	 *生成一个数组，key分别为 fullpath，imgFile['savepath','savename']
	 * 换成 tp5.0.4的文件上传功能
  * 修改： 洋洋洋, xdeepbreath@qq.com , 2017.1.6
	 **/

		public function upload($is_single_file=TRUE){
		    // 获取表单上传文件
				//返回一个二维数组: ['fullpath'=>'','imagFile'=>['savepath'=>'','savename'='']]
			 \Think\Config::load(APP_PATH.'admin/config.php');
				session('upload_error', null);
		    $files = request()->file();
				$root_url= config('editor_upload.rootPath');
				$return= array();
				// \Think\Log::write('editor_upload rootPath: '.$root_url);

		    foreach($files as $key=>$file){
						//接收上传的文件
						$info = $file->move($root_url);
		        if($info){
		            // 成功上传后 获取上传信息

								$url =  __ROOT__.$info->getPath();
								$url = str_replace('./', '/', $url);
								$savepath= basename($url);
								$savename = $info->getFilename();
								$fullpath= __ROOT__.$root_url.$savepath.'/'.$savename;

								$return[$key]['imgFile']['name']=$info->getInfo('name');//原文件名
								$return[$key]['imgFile']['savepath']=$savepath;
								$return[$key]['imgFile']['savename']=$savename;
								$return[$key]['fullpath'] = $fullpath;
								if($is_single_file) return $return[$key];//如果是单个文件上传的话
		        }else{
		            // 上传失败获取错误信息
		            $session('upload_error', $file->getError());
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
