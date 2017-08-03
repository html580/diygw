<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\home\model;
use think\Model;

/**
 * 文件模型
 * 负责文件的下载和上传
 */

class File extends Model{
    protected $autoWriteTimestamp = false;

    /**
     * 文件上传
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting 文件上传配置
     */
    public function upload($files, $setting){
        /* 检测文件是否存在 */
        $isData=$this->isFile(array('md5'=>$files->hash('md5'),'sha1'=>$files->hash()));
        if($isData){
            return $isData; //文件上传成功
        }
        // 上传文件验证
        $info = $files->validate([
                'ext' => $setting['ext'],
                'size' => $setting['size']
            ]
        )->rule($setting['saveName'])->move($setting['rootPath'],true,$setting['replace']);


        if($info){
            /* 记录文件信息 */
            $value['name']  = $info->getInfo('name');
            $value['savename']  = $info->getBasename();
            $value['savepath']  = basename($info->getPath()).'/';
            $value['ext']      = $info->getExtension();
            $value['mime']   = $info->getInfo('type');
            $value['size'] = $info->getInfo('size');
            $value['md5']  = $files->hash('md5');
            $value['sha1']  = $files->hash('sha1');
            $value['location']  = 0;
            $value['create_time']  = time();
            if($add=$this->create($value)){
                $value['id'] = $add->id;
            }
            return $value; //文件上传成功
        } else {
            $this->error = $files->getError();
            return false;
        }
    }

    /**
     * 下载指定文件
     * @param  number  $root 文件存储根目录
     * @param  integer $id   文件ID
     * @param  string   $args     回调函数参数
     * @return boolean       false-下载失败，否则输出下载文件
     */
    public function download($root, $id, $callback = null, $args = null){
        /* 获取下载文件信息 */
        $file = $this->find($id);
        if(!$file){
            $this->error = '不存在该文件！';
            return false;
        }

        /* 下载文件 */
        switch ($file['location']) {
            case 0: //下载本地文件
                $file['rootpath'] = $root;
                return $this->downLocalFile($file, $callback, $args);
            case 1: //下载FTP文件
                $file['rootpath'] = $root;
                return $this->downFtpFile($file, $callback, $args);
                break;
            default:
                $this->error = '不支持的文件存储类型！';
                return false;

        }

    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array   $file 文件上传数组
     * @return boolean       文件信息， false - 不存在该文件
     */
    public function isFile($file){
        if(empty($file['md5'])){
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array('md5' => $file['md5'],'sha1'=>$file['sha1'],);
        if($data=$this->field(true)->where($map)->find()){
            return $data->toArray();
        }else{
            return false;
        }
    }

    /**
     * 下载本地文件
     * @param  array    $file     文件信息数组
     * @param  callable $callback 下载回调函数，一般用于增加下载次数
     * @param  string   $args     回调函数参数
     * @return boolean            下载失败返回false
     */
    private function downLocalFile($file, $callback = null, $args = null){
        $fullpath= $file['rootpath'].$file['savepath'].$file['savename'];
        if(is_file($fullpath)){
            /* 调用回调函数新增下载数 */
            is_callable($callback) && call_user_func($callback, $args);

            /* 执行下载 */ //TODO: 大文件断点续传
            header("Content-Description: File Transfer");
            header('Content-type: ' . $file['type']);
            header('Content-Length:' . $file['size']);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            }
            readfile($fullpath);
            exit;
        } else {
            $this->error = '文件已被删除！';
            return false;
        }
    }

    /**
     * 下载ftp文件
     * @param  array    $file     文件信息数组
     * @param  callable $callback 下载回调函数，一般用于增加下载次数
     * @param  string   $args     回调函数参数
     * @return boolean            下载失败返回false
     */
    private function downFtpFile($file, $callback = null, $args = null){
        /* 调用回调函数新增下载数 */
        is_callable($callback) && call_user_func($callback, $args);

        $host = config('download_host.host');
        $root = explode('/', $file['rootpath']);
        $file['savepath'] = $root[3].'/'.$file['savepath'];

        $data = array($file['savepath'], $file['savename'], $file['name'], $file['mime']);
        $data = json_encode($data);
        $key = think_encrypt($data, config('data_auth_key'), 600);

        header("Location:http://{$host}/twothink.php?key={$key}");
    }

    /**
     * 清除数据库存在但本地不存在的数据
     * @param $data
     */
    public function removeTrash($data){
        $this->where(array('id'=>$data['id'],))->delete();
    }

}
