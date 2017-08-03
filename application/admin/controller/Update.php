<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +---------------------------------------------------------------------- 

namespace app\admin\controller;
use com\File;
use com\PclZip; 
use think\Request;

/**
 * 在线更新
 * @author 艺品网络  <twothink.cn>
 */
class Update extends Admin{

	/**
	 * 初始化页面
	 * @author 艺品网络  <twothink.cn>
	 */
	public function index(){ 
		$this->assign('meta_title','在线更新');
		if(request()->isPost()){
			echo $this->fetch();
			//检查新版本
			$version = $this->checkVersion();
			//在线更新
			$this->update($version);
		}else{
			return $this->fetch();
		}
	}

	/**
	 * 检查新版本
	 * @author 艺品网络  <twothink.cn>
	 */
	private function checkVersion(){ 
		if(extension_loaded('curl')){
			$url = 'http://www.twothink.cn/index.php/index/index/check_version';
			$params = array(
					'version' => TWOTHINK_VERSION,
					'domain'  => $_SERVER['HTTP_HOST'],
					'auth'    => sha1(config('data_auth_key')),
			);
			$vars = http_build_query($params);   
			//获取版本数据
			$data = $this->getRemoteUrl($url, 'post', $vars);  
			if(!empty($data) && strlen($data)<400 ){
				$this->showMsg('发现新版本：'.$data, 'success');
				return $data;
			}else{
				$this->showMsg("未发现新版本", 'error');
				exit;
			}
		}else{
			$this->error('请配置支持curl');
		}
	}

	/**
	 * 在线更新
	 * @author 艺品网络  <twothink.cn>
	 */
	private function update($version){
		//PclZip类库不支持命名空间
		import('OT/PclZip');

		$date  = date('YmdHis');
		$backupFile = input('post.backupfile');
		$backupDatabase = input('post.backupdatabase');
		sleep(1);

		$this->showMsg('系统原始版本:'.TWOTHINK_VERSION);
		$this->showMsg('TwoThink在线更新日志：');
		$this->showMsg('更新开始时间:'.date('Y-m-d H:i:s'));
		sleep(1);

		/* 建立更新文件夹 */
		$folder = 'public/'.$this->getUpdateFolder();  
		File::mk_dir($folder);
		$folder = $folder.'/'.$date;
		File::mk_dir($folder);

		//备份重要文件
		if($backupFile){
			$this->showMsg('开始备份重要程序文件...');
			debug('start1'); 
			$backupallPath =$folder.'/backupall.zip'; 
			$zip = new PclZip($backupallPath);   
			$dd=$zip->create('addons,extend,application,thinkphp,.htaccess,install.php,index.php');  
			$this->showMsg('成功完成重要程序备份,备份文件路径:<a href=\''.'/'.$backupallPath.'\'>'.$backupallPath.'</a>, 耗时:'.debug('start1','stop1').'s','success');
		} 
		/* 获取更新包 */
		//获取更新包地址
		$updatedUrl = 'http://www.twothink.cn/index.php/index';
		$params = array('version' => TWOTHINK_VERSION);
		$updatedUrl = $this->getRemoteUrl($updatedUrl, 'post', http_build_query($params)); dump($updatedUrl);
		if(empty($updatedUrl)){
			$this->showMsg('未获取到更新包的下载地址', 'error');
			exit;
		}
		//下载并保存
		$this->showMsg('开始获取远程更新包...');
		sleep(1);
		$zipPath = $folder.'/update.zip'; 
		$downZip = $this->getRemoteUrl($updatedUrl);
		if(empty($downZip)){
			$this->showMsg('下载更新包出错，请重试！', 'error');
			exit;
		}
		File::write_file($zipPath, $downZip);
		$this->showMsg('获取远程更新包成功,更新包路径：<a href=\''.'/'.ltrim($zipPath,'.').'\'>'.$zipPath.'</a>', 'success');
		sleep(1);
 
		/* 解压缩更新包 */ //TODO: 检查权限
		$this->showMsg('更新包解压缩...');
		sleep(1);
		$zip = new PclZip($zipPath);
		$res = $zip->extract(PCLZIP_OPT_PATH,'./');
		if($res === 0){
			$this->showMsg('解压缩失败：'.$zip->errorInfo(true).'------更新终止', 'error');
			exit;
		}
		$this->showMsg('更新包解压缩成功', 'success');
		sleep(1);

		/* 更新数据库 *///后期扩展
// 		$updatesql = './update.sql';
// 		if(is_file($updatesql))
// 		{
// 			$this->showMsg('更新数据库开始...');
// 			if(file_exists($updatesql))
// 			{
// 				$Model = db();
// 				$sql = File::read_file($updatesql);
// 				$sql = str_replace("\r\n", "\n", $sql);
// 				foreach(explode(";\n", trim($sql)) as $query)
// 				{
// 					$Model->query(trim($query));
// 				}
// 			}
// 			unlink($updatesql);
// 			$this->showMsg('更新数据库完毕', 'success');
// 		}

		/* 系统版本号更新 */
		$file = File::read_file(APP_PATH.'common/common/function.php');  
		$file = str_replace(TWOTHINK_VERSION, $version, $file); 
		$res = File::write_file(APP_PATH.'common/common/function.php', $file);
		if($res === false){
			$this->showMsg('更新系统版本号失败', 'error');
		}else{
			$this->showMsg('更新系统版本号成功', 'success');
		}
		sleep(1);

		$this->showMsg('##################################################################');
		$this->showMsg('在线更新全部完成，如有备份，请及时将备份文件移动至非web目录下！', 'success');
	}

	/**
	 * 获取远程数据
	 * @author 艺品网络  <twothink.cn>
	 */
	private function getRemoteUrl($url = '', $method = '', $param = ''){
		$opts = array(
			CURLOPT_TIMEOUT        => 20,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL            => $url,
			CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
		);
		if($method === 'post'){
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = $param;
		}

		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data  = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		return $data;
	}

	/**
	 * 实时显示提示信息
	 * @param  string $msg 提示信息
	 * @param  string $class 输出样式（success:成功，error:失败）
	 * @author 艺品网络  <twothink.cn>
	 */
	private function showMsg($msg, $class = ''){
		echo "<script type=\"text/javascript\">showmsg(\"{$msg}\",\"{$class}\")</script>";
		flush();
		ob_flush();
	}

	/**
	 * 生成更新文件夹名
	 * @author 艺品网络  <twothink.cn>
	 */
	private function getUpdateFolder(){
		$key = sha1(config('data_auth_key'));
		return 'update_'.$key;
	}
}