<?php
namespace app\diygw\controller;
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
use controller\BasicAdmin;

use service\HttpService;
use service\LogService;
use service\WechatService;
use app\diygw\common\PclZip;
use think\Exception;
use think\Db;
/**
 * User: 邓志锋 <diygwcom@foxmail.com> <http://www.diygw.com>
 * Date: 2018-07-28
 * Time: 下午 6:06
 */
class Dashboard extends BasicAdmin{

    public  $title='应用管理';
    protected function initialize(){
        parent::initialize();
        $this->assign('title',$this->title);
    }
    /**
     * @param $package
     * @return mixed
     * 获取签名
     */
    function sign($package){
        $config = Db::name("AppConfig")->where(['name' => 'auth'])->find();
        if(!$config){
            $this->redirect('auth');
        }
        $config=json_decode($config['value'],true);

        $package['appid'] = $config['appid'];
        $package['nonce_str'] = random(32);
        $package['time_start'] = date('YmdHis', time());
        $package['time_expire'] = date('YmdHis', time() + 3600);
        ksort($package, SORT_STRING);
        $string1 = '';
        foreach ($package as $key => $v ){
            if (empty($v)){
                continue;
            }
            $string1 .= $key . '=' . $v . '&';
        }
        $string1 .= 'appsecret=' . $config['appsecret'];
        $package['sign'] = strtoupper(md5($string1));
        return $package;
    }

    /**
     * @return mixed
     * 应用中心
     */
    public function index()
    {
        $this->title='应用管理';

        $package = $this->sign([]);
        $response =ihttp_post('http://www.diygw.com/auth/update.html', $package);

        if(isset($response['content'])&& !empty($response['content'])){
            $remotes=json_decode($response['content'],true)['content'];
        }

        $datas 	= Db::name("AppDashboard")->where('mpid',$this->mpid)->select();;

        $data=[];
        if(isset($remotes)&&!empty($remotes)){
            $remotedata=[];
            $existdata=[];

            foreach ($remotes  as $rkey => &$ritem){
                $flag = true;
                foreach ($datas  as $key => &$item){
                    if($item['id']==$ritem['id']){
                        $existdata[]=$item['id'];
                        $ritem['isnew']=0;
                        if(strtotime($item['update_time'])<strtotime($ritem['update_time'])){
                            $ritem['isupdate']=1;
                            $ritem['local_update_time']=strtotime($item['update_time']);
                        }

                        $ritem['update_time']=strtotime($ritem['update_time']);
                        $ritem['ismanage']=1;
                        $data[]=$ritem;
                        $flag = false;
                    }
                }
                if($flag){
                    $ritem['isnew']=1;
                    $ritem['update_time']=strtotime($ritem['update_time']);
                    $remotedata[]=$ritem;
                }
            }
            //获取原有的数据,可能是别的用户创建的系统
            foreach ($datas  as $key => &$item){
                if(!in_array($item['id'],$existdata)){
                    $item['isnew']=0;
                    $item['ismanage']=1;
                    $item['update_time']=strtotime($item['update_time']);
                    $data[]=$item;
                }
            }
        }else{
            foreach ($datas  as $key => &$item){
                $item['isnew']=0;
                $item['ismanage']=1;
                $item['update_time']=strtotime($item['update_time']);
                $data[]=$item;
            }
        }

        $this->assign('list',$data);
        $this->assign('remotedata',$remotedata);

        return $this->fetch();

    }


    /**
     * @return mixed
     * 应用授权
     */
    public function auth(){
        $config = Db::name("AppConfig")->where(['name' => 'auth'])->find();
        if($this->request->isPost()){
            $auth = $this->request->post();
            $auth['referrer']=$this->request->host();
            $query = base64_encode(json_encode($auth));
            $auth_url = 'http://www.diygw.com/auth/profile.html';
            $content = ihttp_post($auth_url,['query'=>$query]);
            if(isset($content['content'])&& !empty($content['content'])){
                $auths=json_decode($content['content'],true);
                if($auths['status']=='error'){
                    return $this->error($auths['message']);
                }else{
                    try {
                        $value=$auths['content'];
                        $value['referrer']=$auth['referrer'];
                        $value['username']=$auth['username'];
                        $data['value'] = json_encode($value);
                        $data['name'] ='auth';
                        if(!$config){
                            $data['update_time'] =  date("Y-m-d H:i:s",time());
                            Db::name("AppConfig")->insertGetId($data);
                        }else{
                            $data['create_time'] = date("Y-m-d H:i:s",time());
                            $data['update_time'] = $data['create_time'];
                            Db::name("AppConfig")->where('id',$config['id'])->update($data);
                        }
                        $this->success("授权成功",url('index'));
                    } catch (Exception $e){
                        $this->error("授权失败");
                    }
                }
            }else{
                $this->error("远程授权调用失败");
            }
        }
        $this->assign("auth",json_decode($config['value'],true));
        return $this->fetch();

    }

    function download($package) {
        $package = $this->sign($package);
        $headers = array('content-type' => 'application/x-www-form-urlencoded');
        $dat = ihttp_request('http://www.diygw.com/auth/file.html', $package, $headers, 300);
        if(is_error($dat)) {
            return error(-1, '网络存在错误， 请稍后重试。' . $dat['message']);
        }
        if($dat['content'] == 'success') {
            return true;
        }
        $ret = @json_decode($dat['content'], true);
        if(is_error($ret)) {
            return $ret;
        } else {
            $post = @json_decode($ret['content'], true);
            $data = base64_decode($post);
            if (base64_encode($data) !== $post) {
                $data = $post;
            }
            $ret = iunserializer($data);
            $gz = function_exists('gzcompress') && function_exists('gzuncompress');
            $file = base64_decode($ret['file']);
            if($gz) {
                $file = gzuncompress($file);
            }
            $path = IA_ROOT . $ret['path'];
            load()->func('file');
            @mkdirs(dirname($path));
            if (file_put_contents($path, $file)) {
                return true;
            } else {
                return error(-1, '写入失败');
            }
        }
    }

    /**
     * 生成目录
     * @param  string  $path 目录
     * @param  integer $mode 权限
     * @return boolean
     */
    public function createDir($path, $mode = 0755) {
        if(is_dir($path)) return TRUE;
        $path = str_replace("\\", "/", $path);
        if(substr($path, -1) != '/') $path = $path.'/';
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for($i=0; $i<$max; $i++) {
            $cur_dir .= $temp[$i].'/';
            if (@is_dir($cur_dir)) continue;
            @mkdir($cur_dir, $mode, true);
            @chmod($cur_dir, $mode);
        }
        return is_dir($path);
    }

    public function setup(){
        $package = [];
        $package['id'] = $this->request->request('id');
        $destination = ROOT_PATH.'/static/diygw/update/'.date('Ymd', time()).'.zip';

        $package['type'] = 'Dashboard';
        $package = $this->sign($package);
        $headers = array('content-type' => 'application/x-www-form-urlencoded');
        $dat = ihttp_request('http://www.diygw.com/auth/file.html', $package, $headers, 300);
        if(is_error($dat)) {
            return $this->error('请求服务器错误！' . $dat['message']);
        }
        $this->createDir(ROOT_PATH.'/static/diygw/update');

        if(!$dat['content']){
            return $this->error('请求服务器错误！' . $dat['message']);
        }

        switch ($dat['headers']['Content-Type']) {
            case 'application/octet-stream;charset=ISO-8859-1':
                @file_put_contents($destination,$dat['content']);
                break;
            default:
                $ret = @json_decode($dat['content'], true);
                break;
        }
        if(isset($ret)&&array_key_exists('status', $ret)) {
            return $this->error($ret['message']);
        } else {

            $dashboardDb = Db::name("AppDashboard");
            try{
                $MODULE_ASSETS = '/static/diygw/assests/';
                $archive = new PclZip();
                $archive->PclZip($destination);
                $this->createDir(ROOT_PATH.'/static/diygw/template');
                $this->createDir(ROOT_PATH.'/static/diygw/template/attachment');
                $this->createDir(ROOT_PATH.'/static/diygw/template/data/'.$package['id']);

                if(!$archive->extract(PCLZIP_OPT_PATH, ROOT_PATH.'/static/diygw/', PCLZIP_OPT_REPLACE_NEWER)) {
                    return $this->error('升级失败，请开启template文件夹权限');
                }
                unlink($destination);
                $file = realpath(ROOT_PATH.'/static/diygw/template/data/'.$package['id'].'/'.date('Ymd', time()).'.txt');

                if (is_file($file)) {
                    $content = file_get_contents($file);
                    $content = @json_decode($content, true);

                    if($content){
                        // 开启一个事务，关闭自动提交
                        $dashboardDb->startTrans();
                        $db= $content['db'];//获取远程数据库配置
                        $dashboard= $content['dashboard'];//获取应用
                        $dashboardscene= $content['dashboardscene'];//获取场景
                        $dashboardextend= $content['dashboardextend'];//获取扩展
                        $dashboardpage= $content['dashboardpage'];//获取页面
                        $dashboardtable= $content['dashboardtable'];//获取表格
                        $dashboardmodel= $content['dashboardmodel'];
                        $dashboardattribute= $content['dashboardattribute'];
                        $modeldata= $content['modeldata'];

                        foreach ($dashboardtable as $key => &$remote )
                        {

                            $remote['tablename'] = getTableName($remote['tablename']);
                            $local = db_table_schema($remote['tablename']);
                            $mpidField['null']='yes';
                            $mpidField['name']='mpid';
                            $mpidField['type']='int';
                            $mpidField['length']='11';
                            $remote['fields']['mpid']=$mpidField;
                            $sqls = db_table_fix_sql($local, $remote);
                            $error = false;
                            foreach ($sqls as $sql) {
                                //$tablename=getTableName($name);
                                //$sql = str_replace($name,$tablename,$sql);
                                if (Db::query($sql) === false) {
                                    $error = true;
                                    continue;
                                }
                            }
                        }
                        //插入模型数据
                        Db::name('AppDashboard')->where('id',$dashboard['id'])->where('mpid',$this->mpid)->delete();
                        $dashboard['mpid']=$this->mpid;
                        Db::name('AppDashboard')->insert($dashboard);

                        foreach ($db as $key => &$item )
                        {
                            $find = Db::name('Db')->where('id',$item['id'])->delete();
                            Db::name('Db')->insert($item);
                        }

                        foreach ($dashboardscene as $key => &$item )
                        {
                            if($key==0){
                                Db::name('AppDashboardScene')->where('dashboard_id',$dashboard['id'])->where('mpid',$this->mpid)->delete();
                            }
                            $item['mpid']=$this->mpid;
                            $item['id']=$this->mpid.'_'.$item['id'];
                            Db::name('AppDashboardScene')->insert($item);
                        }

                        foreach ($dashboardextend as $key => &$item )
                        {
                            if($key==0){
                                Db::name('AppDashboardExtend')->where('dashboard_id',$dashboard['id'])->where('mpid',$this->mpid)->delete();
                            }
                            $item['mpid']=$this->mpid;
                            $item['id']=$this->mpid.'_'.$item['id'];
                            Db::name('AppDashboardExtend')->insert($item);
                        }

                        foreach ($dashboardmodel as $key => &$item )
                        {
                            //$item['id']=$this->mpid.'_'.$item['id'];
                            Db::name('AppModel')->where('id',$item['id'])->where('mpid',$this->mpid)->delete();
                            $item['mpid']=$this->mpid;
                            Db::name('AppModel')->insert($item);

                        }

                        foreach ($dashboardattribute as $key => &$item )
                        {
                            //$item['id']=$this->mpid.'_'.$item['id'];
                            Db::name('AppAttribute')->where('id',$item['id'])->where('mpid',$this->mpid)->delete();
                            $item['mpid']=$this->mpid;
                            Db::name('AppAttribute')->insert($item);
                        }

                        $pagewebname=[];
                        $pagemobilename=[];
                        //电脑端网页替换
                        $pagewebname['/static/images'] = $MODULE_ASSETS.'images';
                        $pagewebname['/data/data.html'] = url('@diygw/data/data');
                        $pagewebname['/data/save.html'] = url('@diygw/data/save');
                        $pagewebname['/data/remove.html'] =url('@diygw/data/remove');


                        //手机端网页替换
                        $pagemobilename['/static/images'] = $MODULE_ASSETS.'images';
                        $pagemobilename['/static/img'] = $MODULE_ASSETS.'images';
                        $pagemobilename['/data/data.html'] = url('@index/data/data');
                        $pagemobilename['/data/save.html'] = url('@index/data/save');
                        $pagemobilename['/data/remove.html'] =url('@index/data/remove');
                        $pagemobilename['/xcx/cart/save.html'] =url('@index/cart/save');

                        $pagemobilename['cart'] =url('@index/cart/index');
                        $pagemobilename['order'] =url('@index/order/index');//DiygwcomUtil::to_mobile_url($this->createMobileUrl('order_index', array('m'=>'diygwcom_app','AppDashboardid' => $AppDashboard['id'])));
                        $pagemobilename['address'] =url('@index/address/index');//DiygwcomUtil::to_mobile_url($this->createMobileUrl('address_index', array('m'=>'diygwcom_app','AppDashboardid' => $AppDashboard['id'])));
                        $pagemobilename['empty'] =url('@index/page/emptypage');//DiygwcomUtil::to_mobile_url($this->createMobileUrl('page_emptypage', array('m'=>'diygwcom_app','AppDashboardid' => $AppDashboard['id'])));
                        $pagemobilename['/xcx/cart/index.html?'] =url('@index/cart/index')."?";

                        foreach ($dashboardpage as $key => &$item )
                        {
                            if($item['template']=='mobile'){
                                $pagemobilename[$item["name"]]=url('@index/page/index',['pid'=>$this->mpid.'_'.$item["id"]]);
                            }else{
                                $pagewebname[$item["name"]]=url('@diygw/page/index',['pid'=>$this->mpid.'_'.$item["id"]]);
                            }
                        }

                        foreach ($dashboardpage as $key => &$item )
                        {
                            if($key==0){
                                Db::name('AppPage')->where('Dashboard_id',$dashboard['id'])->where('mpid',$this->mpid)->delete();
                            }
                            if($item['template']=='mobile'){
                                $pagename=$pagemobilename;
                            }else{
                                $pagename=$pagewebname;
                            }
                            foreach ($pagename as $page => $value )
                            {
                                $item['content'] = str_replace("\"".$page."\"","\"".$value."\"",$item['content']);
                                $item['content'] = str_replace("url=\"".$page,"url=\"".$value,$item['content']);
                                $item['content'] = str_replace("src=\"".$page,"src=\"".$value,$item['content']);
                                $item['content'] = str_replace("img=\"".$page,"img=\"".$value,$item['content']);
                            }

                            $item['mpid']=$this->mpid;
                            $item['id']=$this->mpid.'_'.$item['id'];
                            Db::name('AppPage')->insert($item);
                        }

                        foreach ($modeldata as $tablekey => $items )
                        {
                            foreach ($items as $item){
                                $item['mpid']=$this->mpid;
                                if(Db::name($tablekey)->where(['id'=>$item['id'],'mpid'=>$this->mpid])->find()){
                                    continue;
                                }else{
                                    Db::name($tablekey)->insert($item);
                                }

                            }
                        }

                        // 操作成功则提交更改
                        $dashboardDb->commit();
                        unlink($file);
                        return $this->success('安装成功！',url('index'));
                    }else{

                        $this->error('安装失败，请先设计应用');
                    }
                }

            }catch (Exception $e) {
                var_dump($e);
                $dashboardDb->rollback();
                $this->error('安装失败，请复试');
            }
        }
    }

    public function delete(){

        $Dashboard_id= $this->request->request('id');
        Db::name('AppDashboard')->where('id',$Dashboard_id)->delete();
        Db::name('AppDashboardScene')->where('Dashboard_id',$Dashboard_id)->delete();
        Db::name('AppDashboardExtend')->where('Dashboard_id',$Dashboard_id)->delete();
        Db::name('AppPage')->where('Dashboard_id',$Dashboard_id)->delete();
        Db::name('AppAddress')->where('Dashboard_id',$Dashboard_id)->delete();
        return $this->success('删除成功',url('index'));

    }
}