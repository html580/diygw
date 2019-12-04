<?php

// +----------------------------------------------------------------------
// | Diygw
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 DIY官网 [ http://www.diygw.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.diygw.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/html580/diygw
// +----------------------------------------------------------------------

namespace app\admin\controller;


use think\Controller;

/**
 * 云版本下载
 * Class Cloud
 * @package app\admin\controller
 * @author LK <diygwcom@foxmail.com>
 * @date 2019/11/15
 */
class Cloud extends Controller
{

    /**
     * 获取升级版本
     * @author LK <280160522@qq.com>
     * @return array
     */
    public function getVersion()
    {
        $dir = \app\common\util\Dir::getList(ROOT_PATH.'update','zip');
        sort($dir);
        $result['dir']=  $dir;
        echo(json_encode($result));
        return null;
    }

    public function getZipVersion(){
        $version= $this->request->param("version");
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$version);
        header('Content-Length: ' . filesize(ROOT_PATH.'update/'.$version));
        readfile(ROOT_PATH.'update/'.$version);
    }

}
