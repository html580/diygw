<?php

// +----------------------------------------------------------------------
// | DiygwApp
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

use controller\BasicAdmin;
use think\addons\AddonsException;
use think\addons\Service;
use think\Db;
use think\Exception;
use think\modelinfo\System;

/**
 * 扩展后台管理页面
 * Class Addons
 * @package app\admin\controller
 * @author LK <diygwcom@foxmail.com>
 * @date 2018/11/15
 */
class Addons extends BasicAdmin {
    public $currentMenuUrl='addons/index';
    public $formSuccessUrl='menu/index';
    /*
     * @title 动态扩展菜单
     */
    protected function extra_menu(){
        $menu[] = ['title'=>'已装插件后台','operater'=>model('Addons')->getAdminList ()];
        return $menu;
    }
    // 创建向导首页
    public function create() {
        if (! is_writable ( DIYGW_ADDON_PATH ))
            $this->error ( '您没有创建目录写入权限，无法使用此功能' );

        $hooks = model ( 'Hooks' )->field ( 'name,description' )->select ();
        $this->assign( 'Hooks', $hooks );
        $this->assign( 'title', '创建向导' );
        return $this->fetch ( 'create' );
    }

    // 预览
    public function preview($output = true) {
        $data = $this->request->post();
        if(!isset($data['hook'])){
            $this->error('请选择实现的钩子方法');
        }
        $data ['info'] ['status'] = ( int ) $data ['info'] ['status'];
        $extend = array ();
        $custom_config = trim ( $data ['custom_config'] );

        if (isset ( $data ['has_config'] ) && $custom_config) {
            $custom_config = <<<str


        public \$custom_config = '{$custom_config}';
str;
            $extend [] = $custom_config;
        }
        $admin_list = trim ( $data ['admin_list'] );
        if (isset ( $data ['has_adminlist'] ) && $admin_list) {
            $admin_list = <<<str


        public \$admin_list = array(
            {$admin_list}
        );
str;
            $extend [] = $admin_list;
        }

        $custom_adminlist = trim ( $data ['custom_adminlist'] );
        if (isset ( $data ['has_adminlist'] ) && $custom_adminlist) {
            $custom_adminlist = <<<str


        public \$custom_adminlist = '{$custom_adminlist}';
str;
            $extend [] = $custom_adminlist;
        }

        $extend = implode ( '', $extend );
        $hook = '';
        foreach ( $data ['hook'] as $value ) {
            $hook .= <<<str
        //实现的{$value}钩子方法
        public function {$value}(\$param){

        }

str;
        }
        $classname=ucfirst($data['info']['name']);
        $tpl = <<<str
<?php

namespace addons\\{$data['info']['name']};
use think\Addons;

/**
 * {$data['info']['title']}插件
 * @author {$data['info']['author']}
 */

    class {$classname} extends Addons{

        public \$info = array(
            'name'=>'{$data['info']['name']}',
            'title'=>'{$data['info']['title']}',
            'description'=>'{$data['info']['description']}',
            'status'=>{$data['info']['status']},
            'author'=>'{$data['info']['author']}',
            'version'=>'{$data['info']['version']}'
        );{$extend}

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

{$hook}
    }
str;
        if ($output)
            exit ( $tpl );
        else
            return $tpl;
    }
    public function checkForm() {
        $data = $this->request->post();
        $data ['info'] ['name'] = trim ( $data ['info'] ['name'] );
        if (! $data ['info'] ['name'])
            $this->error ( '插件标识必须' );
        // 检测插件名是否合法
        $addons_dir = DIYGW_ADDON_PATH;
        if (file_exists ( "{$addons_dir}{$data['info']['name']}" )) {
            $this->error ( '插件已经存在了' );
        }
        $this->success ( '可以创建' );
    }
    public function build() {
        $data = $this->request->post();
        $data ['info'] ['name'] = trim ( $data ['info'] ['name'] );
        $addonFile = $this->preview ( false );
        $addons_dir = DIYGW_ADDON_PATH;
        // 创建目录结构
        $files = array ();
        $addon_dir = "$addons_dir{$data['info']['name']}/";
        $files [] = $addon_dir;
        $addon_name = ucfirst($data['info']['name']).".php";
        $files [] = "{$addon_dir}{$addon_name}";
        // 如果有配置文件
        if (isset ( $data ['has_config'] ) && $data ['has_config'] == 1)
            $files [] = $addon_dir . 'config.php';

        if (isset ( $data ['has_outurl'] )) {
            $files [] = "{$addon_dir}controller/";
            $files [] = "{$addon_dir}controller/".ucfirst($data['info']['name']).".php";
            $files [] = "{$addon_dir}model/";
            $files [] = "{$addon_dir}model/".ucfirst($data['info']['name']).".php";
        }

        $custom_config = trim ( $data ['custom_config'] );
        if ($custom_config)
            $data [] = "{$addon_dir}{$custom_config}";

        $custom_adminlist = trim ( $data ['custom_adminlist'] );
        if ($custom_adminlist)
            $data [] = "{$addon_dir}{$custom_adminlist}";

        if (! create_dir_or_files ( $files ))
            return $this->error ( '插件' . $data ['info'] ['name'] . '目录存在' );

        // 写文件
        file_put_contents ( "{$addon_dir}{$addon_name}", $addonFile );
        if (isset ( $data ['has_outurl'] ) && $data ['has_outurl']) {
            $addonController = <<<str
<?php

namespace addons\\{$data['info']['name']}\controller; 
use think\addons\Controller;

class {$data['info']['name']} extends Controller{

}

str;
            file_put_contents ( "{$addon_dir}controller/".ucfirst($data['info']['name']).".php", $addonController );
            $addonModel = <<<str
<?php

namespace addons\\{$data['info']['name']}\model;
use app\common\model\AddonsBase;

/**
 * {$data['info']['name']}模型
 */
class {$data['info']['name']} extends AddonsBase{
    public \$model_info = [
        'name' => '{$data['info']['name']}',
        'button' => [
            ['title'=>'新增','url'=>'edit?name={$data['info']['name']}','icon'=>'','class'=>'list_add btn-success','ExtraHTML'=>''],
            ['title'=>'删除','url'=>'del?name={$data['info']['name']}','icon'=>'','class'=>'btn-danger ajax-post confirm','ExtraHTML'=>'']
        ],
        //特殊字符串替换用于列表定义解析
        'replace_string' => [['[DELETE]','[EDIT]','[ADDON]'],['del?ids=[id]&name=[ADDON]','edit?id=[id]&name=[ADDON]','{$data['info']['name']}']],
        'field_group'=>'1:基础,2:扩展',//表单显示分组
        "fields"=>[
            '1'=>[
                [
                    'name'=>'id',//字段名
                    'title'=>'ID',//显示标题
                    'type'=>'num',//字段类型
                    'remark'=>'',// 备注，相当于配置里的tip
                    'is_show'=>3,// 1-始终显示 2-新增显示 3-编辑显示 0-不显示
                    'value'=>0,//默认值
                ], 
            ],
            '2'=>[
                [
                    'name'=>'id',//字段名
                    'title'=>'ID',//显示标题
                    'type'=>'num',//字段类型
                    'remark'=>'',// 备注，相当于配置里的tip
                    'is_show'=>3,// 1-始终显示 2-新增显示 3-编辑显示 0-不显示
                    'value'=>0,//默认值
                ], 
            ]
        ],
        'list_grid' => [        //这里定义的是除了id序号外的表格里字段显示的表头名和模型一样支持函数和链接
            'title:广告位名称',
            'type:广告位类型',
            'width:广告位宽度',
            'height:广告位高度',
            'status:位置状态',
            'id:操作:[EDIT]|编辑,[DELETE]|删除'
        ]
    ]; 
}

str;
            file_put_contents ( "{$addon_dir}model/".ucfirst($data['info']['name']).".php", $addonModel );
        }

        if (isset ( $data ['has_config'] ) && $data ['has_config'] == 1)
            file_put_contents ( "{$addon_dir}config.php", $data ['config'] );

        $this->success ( '创建成功', url ( 'index' ) );
    }



    /**
     * 插件列表
     */
    public function index() {
        $model = model('Addons');
        $list = $model->getList();
        $page = input ( 'page', 1 );
        $number = 25; // 每页显示
        $voList = Db::name ( 'Addons' )->paginate ( $number, false, array (
            'page' => $page
        ) );
        $_page = $voList->render (); // 获取分页显示
        $voList = array_slice ( $list, bcmul ( $number, $page ) - $number, $number );

        // 模板变量赋值
        $this->assign( 'title', '插件列表' );
        $this->assign( 'page', $_page );
        $this->assign( 'list', $voList );
        // 记录当前列表页的cookie
        Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
        $this->request->isPjax();
        return $this->fetch ();

    }

    /**
     * 插件后台显示页面
     *
     * @param string $name
     *        	插件名
     */
    public function adminList($name) {
        // 记录当前列表页的cookie
        Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
        $this->assign( 'name', $name );
        $class = get_addon_class ( $name );
        if (! class_exists ( $class ))
            $this->error ( '插件不存在' );
        $addon = new $class ();
        $this->assign( 'addon', $addon );
        $param = $addon->admin_list;
        if (! $param)
            $this->error ( '插件列表信息不正确' );
        $this->assign( 'title', $addon->info ['title'] );
        extract ( $param );
        $this->assign( 'title', $addon->info ['title'] );
        $this->assign( $param );
        if (! isset ( $fields ))
            $fields = '*';
        if (! isset ( $search_key ))
            $key = 'title';
        else
            $key = $search_key;

        $uri_param= $this->request->param();
        $uri_get=$this->request->get();

        $map = array();
        if (isset ( $uri_param [$key] )) {
            $map [$key] = ['like',	'%' . $uri_get [$key] . '%'];
            unset ( $uri_param [$key] );
        }
        if (isset ( $model )) {
            $model_name = $model;
            $class = get_addon_model ( $name, $model );
            $model = new $class();
            //模型定义
            if(isset($model->model_info) && !empty($model->model_info)){
                if(is_numeric($model->model_info)){
                    $model_obj = new ModelSystem();
                    $model_obj = $model_obj->info($model->model_info);
                }else{
                    $model_obj = Modelinfo()->info($model->model_info);
                }
                $model_info = $model_obj->getListField()->getSearchList()->setInit()->getParam('info');

                $model_info['url'] = $this->request->url();
                if(!isset($model_info['button'])){
                    $model_info['button'] = [
                        ['title'=>'新增','url'=>'edit?name='.$name,'icon'=>'','class'=>'list_add btn-success','ExtraHTML'=>''],
                        ['title'=>'删除','url'=>'del?name='.$name,'icon'=>'','class'=>'btn-danger ajax-post confirm','ExtraHTML'=>''],
                    ];
                }
                $this->assign( 'model_info', $model_info );
            }
            if($this->request->isPost()){
                $list = $model_obj->getWhere()->getViewList()->parseList()->parseListIntent()->getParam('info.data');
                $list['code'] = 1;
                return json($list);
            }
        }
        if ($addon->custom_adminlist){
            //获取记录
            $this->assign( 'custom_adminlist', $this->fetch ( $addon->addon_path . $addon->custom_adminlist ) );
        }
        return $this->fetch ('adminlist' ); //$addon->addon_path  相对路径
    }

    /**
     * 启用插件
     */
    public function enable() {
        $id = input ( 'id' );
        cache ( 'hooks', null );
        if ( Db::name ( 'Addons' )->where ( array (
            'id' => $id
        ) )->update ( [
            'status' => 1
        ] )) {
            $this->success ( '启用成功',url('index') );
        } else {
            $this->error ( '启用失败' );
        }
    }

    /**
     * 禁用插件
     */
    public function disable() {
        $id = input ( 'id' );
        cache ( 'hooks', null );
        if ( Db::name( 'Addons' )->where ( array (
            'id' => $id
        ) )->update ( [
            'status' => 0
        ] )) {
            $this->success ( '禁用成功',url('index') );
        } else {
            $this->error ( '禁用失败' );
        }
    }

    /**
     * 设置插件页面
     */
    public function config() {
        $id = ( int ) input ( 'id' );
        $addon = model ( 'Addons' )->find ( $id )->toArray ();
        if (! $addon)
            $this->error ( '插件未安装' );
        $addon_class = get_addon_class ( $addon ['name'] );
        if (! class_exists ( $addon_class ))
            trace ( "插件{$addon['name']}无法实例化,", 'ADDONS', 'ERR' );
        $data = new $addon_class ();
        $addon ['addon_path'] = $data->addon_path;
        $addon ['custom_config'] = isset($data->custom_config)?$data->custom_config:'';
        $this->assign( 'title', '设置插件-' . $data->info ['title'] );
        $db_config = $addon ['config'];
        $addon ['config'] = include $data->config_file;
        if ($db_config) {
            $db_config = json_decode ( $db_config, true );
            foreach ( $addon ['config'] as $key => $value ) {
                if ($value ['type'] != 'group') {
                    $addon ['config'] [$key] ['value'] = isset($db_config [$key])?$db_config [$key]:'';
                } else {
                    foreach ( $value ['options'] as $gourp => $options ) {
                        foreach ( $options ['options'] as $gkey => $value ) {
                            $addon ['config'] [$key] ['options'] [$gourp] ['options'] [$gkey] ['value'] = $db_config [$gkey];
                        }
                    }
                }
            }
        }
        $this->assign ( 'data', $addon );
        $this->assign ( 'title', $addon['title'] );
        if ($addon ['custom_config'])
            $this->assign ( 'custom_config', $this->fetch ( $addon ['addon_path'] . $addon ['custom_config'] ) );

        return $this->fetch ();
    }

    /**
     * 保存插件设置
     */
    public function saveConfig() {
        $id = ( int ) input ( 'id' );
        $config = input ( 'config/a' );

        $flag = Db::name ( 'Addons' )->where ( "id={$id}" )->setField ( 'config', json_encode ( $config ) );
        if ($flag !== false) {
            $this->success ( '保存成功',url('index') );
        } else {
            $this->error ( '保存失败' );
        }
    }

    /**
     * 解析数据库语句函数
     *
     * @param string $sql
     *        	sql语句 带默认前缀的
     * @param string $tablepre
     *        	自己的前缀
     * @return multitype:string 返回最终需要的sql语句
     */
    public function sql_split($sql, $tablepre) {
        if ($tablepre != "diygw_")
            $sql = str_replace ( "diygw_", $tablepre, $sql );
        $sql = preg_replace ( "/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql );

        // if ($r_tablepre != $s_tablepre)
        // 	$sql = str_replace ( $s_tablepre, $r_tablepre, $sql );
        $sql = str_replace ( "\r", "\n", $sql );
        $ret = array ();
        $num = 0;
        $queriesarray = explode ( ";\n", trim ( $sql ) );
        unset ( $sql );
        foreach ( $queriesarray as $query ) {
            $ret [$num] = '';
            $queries = explode ( "\n", trim ( $query ) );
            $queries = array_filter ( $queries );
            foreach ( $queries as $query ) {
                $str1 = substr ( $query, 0, 1 );
                if ($str1 != '#' && $str1 != '-')
                    $ret [$num] .= $query;
            }
            $num ++;
        }
        return $ret;
    }

    /**
     * 获取插件所需的钩子是否存在，没有则新增
     *
     * @param string $str
     *        	钩子名称
     * @param string $addons
     *        	插件名称
     * @param string $addons
     *        	插件简介
     */
    public function existHook($str, $addons, $msg = '') {
        $hook_mod = Db::name( 'Hooks' );
        $where ['name'] = $str;
        $gethook = $hook_mod->where ( $where )->find ();
        if (! $gethook || empty ( $gethook ) || ! is_array ( $gethook )) {
            $data ['name'] = $str;
            $data ['description'] = $msg;
            $data ['type'] = 1;
            $data ['update_time'] = time();
            $data ['addons'] = $addons;
            if (false !== $hook_mod->create ( $data )) {

            }
        }
    }

    /**
     * 删除钩子
     *
     * @param string $hook
     *        	钩子名称
     */
    public function deleteHook($hook) {
        $model = Db::name ( 'hooks' );
        $condition = array (
            'name' => $hook
        );
        $model->where ( $condition )->delete ();
        cache ( 'hooks', null );
    }
    /**
     * 安装插件
     */
    public function install() {
        $param = $this->request->param();
        $addon_name = trim ( $param['addon_name'] );
        $iscover = $param['iscover'];
        //插件资源文件安装
        try{
            Service::install($addon_name,$iscover);
        }
        catch (AddonsException $e){
            $this->result($e->getData(), $e->getCode(), $e->getMessage());
        }
        catch (Exception $e){
            $this->error($e->getMessage());
        }

        //插件数据更新
        $class = get_addon_class ( $addon_name );
        $addons = new $class ();
        $info = $addons->info;
        $addonsModel = model ( 'Addons' );
        if (isset($addons->admin_list) && is_array ( $addons->admin_list ) && $addons->admin_list !== array ()) {
            $info ['has_adminlist'] = 1;
        } else {
            $info ['has_adminlist'] = 0;
        }
        $info ['config'] = json_encode ( $addons->getConfig () );
        if ($addonsModel->save ( $info )) {
            $hooks_update = model ( 'Hooks' )->updateHooks ( $addon_name );
            if ($hooks_update) {
                cache ( 'hooks', null );
                $this->success ( '安装成功', url ( 'index' ) );
            } else {
                $addonsModel->where ( "name='{$addon_name}'" )->delete ();
                $this->error ( '更新钩子处插件失败,请卸载后尝试重新安装' );
            }
        } else {
            $this->error ( '写入插件数据失败' );
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall() {
        $param = $this->request->param();

        $addonsModel =  Db::name  ( 'Addons' );
        $id = trim ( $param['id'] );
        $db_addons = $addonsModel->find ( $id );
        $class = get_addon_class ( $db_addons ['name'] );
        try{
            Service::uninstall($db_addons ['name'],$param['iscover']);
        }
        catch (AddonsException $e){
            $this->result($e->getData(), $e->getCode(), $e->getMessage());
        }
        catch (Exception $e){
            $this->error($e->getMessage());
        }
//        $this->assign ( 'jumpUrl', url ( 'index' ) );
//        if (! $db_addons || ! class_exists ( $class ))
//            $this->error ( '插件不存在' );
        session ( 'addons_uninstall_error', null );
        $addons = new $class ();
        $uninstall_flag = $addons->uninstall ();
        if (! $uninstall_flag)
            $this->error ( '执行插件预卸载操作失败' . session ( 'addons_uninstall_error' ) );
        $hooks_update = model ( 'Hooks' )->removeHooks ( $db_addons ['name'] );
        if ($hooks_update === false) {
            $this->error ( '卸载插件所挂载的钩子数据失败' );
        }
        cache ( 'hooks', null );
        $delete = $addonsModel->where ( "name='{$db_addons['name']}'" )->delete ();
        if ($delete === false) {
            $this->error ( '卸载插件失败' );
        } else {
            $this->success ( '卸载成功', url ( 'index' ) );
        }
    }



    public function execute() {
        return (new \think\addons\Route())->execute();
    }


    public function edit($name, $id = 0) {
        $this->assign ( 'name', $name );
        $class = get_addon_class ( $name );
        if (! class_exists ( $class ))
            $this->error ( '插件不存在' );
        $addon = new $class ();
        $this->assign ( 'addon', $addon );
        $param = $addon->admin_list;
        if (! $param)
            $this->error ( '插件列表信息不正确' );
        extract ( $param );
        $this->assign ( 'title', $addon->info ['title'] );
        if (isset ( $model )) {
            $model_name = $model;
            $class = get_addon_model ( $name, $model );
            if (class_exists ( $class )) { // 实例化插件模型成功执行
                $addonModel = new $class ();
            }
            if (! $addonModel)
                $this->error ( '模型无法实列化' );
            //模型定义
            if(isset($addonModel->model_info) && !empty($addonModel->model_info)){
                if(is_numeric($model->model_info)){
                    $model_obj = new ModelSystem();
                    $model_obj = $model_obj->info($addonModel->model_info)->getFields();
                }else{
                    $model_obj = Modelinfo()->info($addonModel->model_info);
                }
                $model_info = $model_obj->FieldDefaultValue()->setInit()->getParam('info');

                if(!$model_info['url'])
                    $model_info['url'] = $this->request->url();
                $this->assign( 'model_info', $model_info );
            }
        }
        if ($id) {
            $data = $addonModel->find ( $id );
            $data || $this->error ( '数据不存在！' );
            $this->assign ( 'data', $data );
        }else{
            $data = $model_info['field_default_value'];
            $this->assign ( 'data', $data );
        }

        if ($this->request->isPost()) {
            // 获取模型的字段信息
            $data = $this->request->param();
            $flag = $addonModel->editData();
            $flag || $this->error($addonModel->getError());
            $this->success(!empty($data['id'])?"编辑{$model_info['title']}成功！":"添加{$model_info['title']}成功！", Cookie('__forward__'));
        } else {
            $this->assign ( 'title', $id ? '编辑' . $model_info ['title'] : '新增' . $model_info ['title'] );

            if ($id)
                $template = $model_info ['template_edit'] ? $model_info ['template_edit'] : '';
            else
                $template = $model_info ['template_add'] ? $model_info ['template_add'] : '';
            if ($template)
                return $this->fetch ( $addon->addon_path . $template );
            else
                return $this->fetch ();
        }
    }
    public function del($id = '', $name) {
        $ids = array_unique ( ( array ) input ( 'ids/a') );
        if (empty ( $ids )) {
            $this->error ( '请选择要操作的数据!' );
        }

        $class = get_addon_class ( $name );
        if (! class_exists ( $class ))
            $this->error ( '插件不存在' );
        $addon = new $class ();
        $param = $addon->admin_list;
        if (! $param)
            $this->error ( '插件列表信息不正确' );
        extract ( $param );
        if (isset ( $model )) {
            $class = get_addon_model ( $name, $model );
            if (class_exists ( $class )) { // 实例化插件模型成功执行
                $addonModel = new $class ();
            }
            if (!$addonModel)
                $this->error ( '模型无法实列化' );
        }

        $map = array (
            'id' => array (
                'in',
                $ids
            )
        );
        if ($addonModel->where ( $map )->delete ()) {
            $this->success ( '删除成功' );
        } else {
            $this->error ( '删除失败！' );
        }
    }
}
