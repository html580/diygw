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

use app\diygw\common\PclZip;
use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\App;
use think\Db;
use think\Exception;

/**
 * 后台入口
 * Class Index
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 10:41
 */
class Index extends BasicAdmin
{

    /**
     * 后台框架布局
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
//        NodeService::applyAuthNode();
//        $list = (array)Db::name('SystemMenu')->where(['status' => '1'])->order('sort asc,id asc')->select();
//        $menus = $this->buildMenuData(ToolsService::arr2tree($list), NodeService::get(), !!session('user'));
        if (!session('user.id')) {
            $this->redirect('@admin/login');
        }
        $headers = array('content-type' => 'application/x-www-form-urlencoded');
        $vresion = ihttp_request('http://cloud.diygw.com/admin/cloud/getVersion.html', '', $headers, 1);
        $versions = json_decode($vresion['content'],true);
        $version  = sysconf("diygw_cloud_version");
        if(empty($version)){$version=20191201000000;};
        $this->assign('version',$version);
        if(!empty($versions['dir'])){
            $cloundVersion = explode('.',$versions['dir'][count($versions['dir'])-1])[0];
            if($version<$cloundVersion){
                $this->assign('diygw_cloud_version', $cloundVersion);
            }else{
                $this->assign('version',$version.'最新版');
            }
        }
        return $this->fetch('main', ['title' => '系统管理']);
    }


    /**
     * 主机信息显示
     * @return string
     */
    public function main()
    {

        $_version = Db::query('select version() as ver');
        return $this->fetch('', [
            'title'     => '后台首页',
            'think_ver' => App::VERSION,
            'mysql_ver' => array_pop($_version)['ver'],
        ]);
    }

    /**
     * 修改密码
     * @return array|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pass()
    {
        if (intval($this->request->request('id')) !== intval(session('user.id'))) {
            $this->error('只能修改当前用户的密码！');
        }
        if ($this->request->isGet()) {
            $this->assign('verify', true);
            return $this->_form('SystemUser', 'user/pass');
        }
        $data = $this->request->post();
        if ($data['password'] !== $data['repassword']) {
            $this->error('两次输入的密码不一致，请重新输入！');
        }
        $user = Db::name('SystemUser')->where('id', session('user.id'))->find();
        if (md5($data['oldpassword']) !== $user['password']) {
            $this->error('旧密码验证失败，请重新输入！');
        }
        if (DataService::save('SystemUser', ['id' => session('user.id'), 'password' => md5($data['password'])])) {
            $this->success('密码修改成功，下次请使用新密码登录！', '');
        }
        $this->error('密码修改失败，请稍候再试！');
    }

    /**
     * 修改资料
     * @return array|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info()
    {
        if (intval($this->request->request('id')) === intval(session('user.id'))) {
            return $this->_form('SystemUser', 'user/form');
        }
        $this->error('只能修改当前用户的资料！');
    }

    public function clear(){
        try{
            $R = RUNTIME_PATH.DS.'cache';
            $this->_deleteDir($R);
            $R = RUNTIME_PATH.DS.'log';
            $this->_deleteDir($R);
            $R = RUNTIME_PATH.DS.'temp';
            $this->_deleteDir($R);
            !empty($_SESSION) && $_SESSION = [];
            [session_unset(), session_destroy()];
            return $this->success('清除缓存成功!','admin/index/index');
        }catch (Exception $e){
            return $this->error('清除缓存失败!');
        }

    }

    private function _deleteDir($dirName){


        if(! is_dir($dirName))
        {
            return false;
        }
        $handle = @opendir($dirName);
        while(($file = @readdir($handle)) !== false)
        {
            if($file != '.' && $file != '..')
            {
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? $this->_deleteDir($dir) : @unlink($dir);
            }
        }
        closedir($handle);

        return rmdir($dirName) ;
    }


    public function update(){
        $headers = array('content-type' => 'application/x-www-form-urlencoded');
        $vresion = ihttp_request('http://cloud.diygw.com/admin/cloud/getVersion.html', '', $headers, 300);
        $versions = json_decode($vresion['content'],true);
        if(!empty($versions['dir'])){
            foreach ($versions['dir'] as $dir){
                $destination = ROOT_PATH.'/update/'.$dir;
                $dat = ihttp_request('http://cloud.diygw.com/admin/cloud/getZipVersion.html', ['version'=>$dir], $headers, 300);
                try{
                    @file_put_contents($destination,$dat['content']);
                }catch (\Exception $e){
                    return $this->error('升级失败，请开启'.ROOT_PATH.'文件夹权限'.$e);
                }
                $archive = new PclZip();
                $archive->PclZip($destination);
                if(!$archive->extract(PCLZIP_OPT_PATH, ROOT_PATH, PCLZIP_OPT_REPLACE_NEWER)) {
                    return $this->error('升级失败，请开启'.ROOT_PATH.'文件夹权限');
                }
                $version = explode('.',$dir)[0];
                $sql = ROOT_PATH.'/update/'.$version.'/update.sql';
                $sqlData = get_mysql_data($sql, '', '');
                foreach ($sqlData as $sql) {
                    try{
                        Db::execute($sql);
                    }catch (\Exception $e){
                    }
                }
                sysconf("diygw_cloud_version",$version);
            }
        }
        return $this->success('更新版本成功!','admin/index/index');
    }
}
