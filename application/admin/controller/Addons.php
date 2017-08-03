<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Modelmodel;
use think\Request;
/**
 * 扩展后台管理页面
 *
 * @author 艺品网络  <twothink.cn>
 *
 */
class Addons extends Admin {
    public function _initialize() {
        $this->assign ( '_extra_menu', array (
            '已装插件后台' => model ( 'Addons' )->getAdminList ()
        ) );
        parent::_initialize ();
    }

    // 创建向导首页
    public function create() {
        if (! is_writable ( TWOTHINK_ADDON_PATH ))
            $this->error ( '您没有创建目录写入权限，无法使用此功能' );

        $hooks = model ( 'Hooks' )->field ( 'name,description' )->select ();
        $this->assign ( 'Hooks', $hooks );
        $this->assign ( 'meta_title', '创建向导' );
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
use app\common\controller\Addon;

/**
 * {$data['info']['title']}插件
 * @author {$data['info']['author']}
 */

    class {$classname} extends Addon{

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
        $addons_dir = TWOTHINK_ADDON_PATH;
        if (file_exists ( "{$addons_dir}{$data['info']['name']}" )) {
            $this->error ( '插件已经存在了' );
        }
        $this->success ( '可以创建' );
    }
    public function build() {
        $data = $this->request->post();
        $data ['info'] ['name'] = trim ( $data ['info'] ['name'] );
        $addonFile = $this->preview ( false );
        $addons_dir = TWOTHINK_ADDON_PATH;
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
use app\home\controller\Addons;

class {$data['info']['name']} extends Addons{

}

str;
            file_put_contents ( "{$addon_dir}controller/".ucfirst($data['info']['name']).".php", $addonController );
            $addonModel = <<<str
<?php

namespace addons\\{$data['info']['name']}\model;
use think\Model;

/**
 * {$data['info']['name']}模型
 */
class {$data['info']['name']} extends Model{
    public \$model = array(
        'title'=>'',//新增[title]、编辑[title]、删除[title]的提示
        'template_add'=>'',//自定义新增模板自定义html edit.html 会读取插件根目录的模板
        'template_edit'=>'',//自定义编辑模板html
        'search_key'=>'',// 搜索的字段名，默认是title
        'field_group'=>'1:基础,2:扩展',//表单显示分组
        'extend'=>1,
    );

    public \$_fields = array(
        '1'=>array(
                    array(
                        'name'=>'id',//字段名
                        'title'=>'ID',//显示标题
                        'type'=>'num',//字段类型
                        'remark'=>'',// 备注，相当于配置里的tip
                        'is_show'=>3,// 1-始终显示 2-新增显示 3-编辑显示 0-不显示
                        'value'=>0,//默认值
                    ),
                    array(
                        'name'=>'title',
                        'title'=>'书名',
                        'type'=>'string',
                        'remark'=>'',
                        'is_show'=>1,
                        'value'=>0,
                        'is_must'=>1,
                    ),
        ),
        '2'=>array(
                    array(
                        'name'=>'id',//字段名
                        'title'=>'ID',//显示标题
                        'type'=>'num',//字段类型
                        'remark'=>'',// 备注，相当于配置里的tip
                        'is_show'=>3,// 1-始终显示 2-新增显示 3-编辑显示 0-不显示
                        'value'=>0,//默认值
                    ),
                    array(
                        'name'=>'title',
                        'title'=>'书名',
                        'type'=>'string',
                        'remark'=>'',
                        'is_show'=>1,
                        'value'=>0,
                        'is_must'=>1,
                    ),
        ),
    );
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
        $this->assign ( 'meta_title', '插件列表' );
        $list = model ( 'Addons' )->getList ();
        $page = input ( 'page', 1 );
        $number = 25; // 每页显示
        $voList = \think\Db::name ( 'Addons' )->paginate ( $number, false, array (
            'page' => $page
        ) ); // 分页查询
        $_page = $voList->render (); // 获取分页显示
        $voList = array_slice ( $list, bcmul ( $number, $page ) - $number, $number );

        // 模板变量赋值
        $this->assign ( '_page', $_page );
        $this->assign ( '_list', $voList );
        // 记录当前列表页的cookie
        Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
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
        $this->assign ( 'name', $name );
        $class = get_addon_class ( $name );
        if (! class_exists ( $class ))
            $this->error ( '插件不存在' );
        $addon = new $class ();
        $this->assign ( 'addon', $addon );
        $param = $addon->admin_list;
        if (! $param)
            $this->error ( '插件列表信息不正确' );
        $this->assign ( 'meta_title', $addon->info ['title'] );
        extract ( $param );
        $this->assign ( 'title', $addon->info ['title'] );
        $this->assign ( $param );
        if (! isset ( $fields ))
            $fields = '*';
        if (! isset ( $search_key ))
            $key = 'title';
        else
            $key = $search_key;

        $request = \think\Request::instance();
        $uri_param= $request->param();
        $uri_get=$request->get();

        $map = array();
        if (isset ( $uri_param [$key] )) {
            $map [$key] = ['like',	'%' . $uri_get [$key] . '%'];
            unset ( $uri_param [$key] );
        }
        if (isset ( $model )) {
            $model_name = $model;
            $class = get_addon_model ( $name, $model );
            $model = new $class();
            // 条件搜索
//            $map = array();
            $tbFields = \think\Db::getTableInfo( config ( 'database.prefix' ) . strtolower($model_name),'fields' );//表名强制为小写
            foreach ( $uri_param as $name => $val ) {
                if ($fields == '*') $fields = $tbFields;
                if (in_array ( $name, $fields )) {
                    $map [$name] = $val;
                }
            }
            if (! isset ( $order ))	$order = '';
            if(empty($fields)) $fields= true;
            $list = $this->lists( $model, $map, $order,$fields);
            $fields = array ();
            foreach ( $list_grid as &$value ) {
                // 字段:标题:链接
                $val = explode ( ':', $value );
                // 支持多个字段显示
                $field = explode ( ',', $val [0] );
                $value = array ('field' => $field,'title' => $val [1]);
                if (isset ( $val [2] )) {
                    // 链接信息
                    $value ['href'] = $val [2];
                    // 搜索链接信息中的字段信息
                    preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
                        $fields [] = $match [1];
                    }, $value ['href'] );
                }
                if (strpos ( $val [1], '|' )) {
                    // 显示格式定义
                    list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
                }
                foreach ( $field as $val ) {
                    $array = explode ( '|', $val );
                    $fields [] = $array [0];
                }
            }
            $this->assign ( 'list_grid', $list_grid );
            $this->assign ( 'model', $model->model);
        }
        $this->assign ( '_list', $list );
        if ($addon->custom_adminlist){
            //获取记录
            $this->assign ( 'custom_adminlist', $this->fetch ( $addon->addon_path . $addon->custom_adminlist ) );
        }
        return $this->fetch ('adminlist' ); //$addon->addon_path  相对路径
    }

    /**
     * 启用插件
     */
    public function enable() {
        $id = input ( 'id' );
        cache ( 'hooks', null );
        if ( \think\Db::name ( 'Addons' )->where ( array (
            'id' => $id
        ) )->update ( [
            'status' => 1
        ] )) {
            $this->success ( '启用成功' );
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
        if ( \think\Db::name( 'Addons' )->where ( array (
            'id' => $id
        ) )->update ( [
            'status' => 0
        ] )) {
            $this->success ( '禁用成功' );
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
        $addon ['custom_config'] = $data->custom_config;
        $this->assign ( 'meta_title', '设置插件-' . $data->info ['title'] );
        $db_config = $addon ['config'];
        $addon ['config'] = include $data->config_file;
        if ($db_config) {
            $db_config = json_decode ( $db_config, true );
            foreach ( $addon ['config'] as $key => $value ) {
                if ($value ['type'] != 'group') {
                    $addon ['config'] [$key] ['value'] = $db_config [$key];
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

        $flag = \think\Db::name ( 'Addons' )->where ( "id={$id}" )->setField ( 'config', json_encode ( $config ) );
        if ($flag !== false) {
            $this->success ( '保存成功', Cookie ( '__forward__' ) );
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
        if ($tablepre != "twothink_")
            $sql = str_replace ( "twothink_", $tablepre, $sql );
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
        $hook_mod = \think\Db::name( 'Hooks' );
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
        $model = \think\Db::name ( 'hooks' );
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
        $addon_name = trim ( input ( 'addon_name' ) );
        $class = get_addon_class ( $addon_name );
        if (! class_exists ( $class ))
            $this->error ( '插件不存在' );
        $addons = new $class ();
        $info = $addons->info;

        if (! $info) // 检测信息的正确性
            $this->error ( '插件信息缺失' );
        session ( 'addons_install_error', null );
        $install_flag = $addons->install ();
        if (! $install_flag) {
            $this->error ( '执行插件预安装操作失败' . session ( 'addons_install_error' ) );
        }

        $addonsModel = model ( 'Addons' );
        if (is_array ( $addons->admin_list ) && $addons->admin_list !== array ()) {
            $info ['has_adminlist'] = 1;
        } else {
            $info ['has_adminlist'] = 0;
        }
        $info ['config'] = json_encode ( $addons->getConfig () );
        if ($addonsModel->save ( $info )) {
            $hooks_update = model ( 'Hooks' )->updateHooks ( $addon_name );
            if ($hooks_update) {
                cache ( 'hooks', null );
                //复制插件移动的资源文件
                $File = new \com\File();
                $File->copy_dir(TWOTHINK_ADDON_PATH.$addon_name.'/public', './static/addons/'.$addon_name);
                $this->success ( '安装成功' );
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
        $addonsModel =  \think\Db::name  ( 'Addons' );
        $id = trim ( input ( 'id' ) );
        $db_addons = $addonsModel->find ( $id );
        $class = get_addon_class ( $db_addons ['name'] );
        $this->assign ( 'jumpUrl', url ( 'index' ) );
        if (! $db_addons || ! class_exists ( $class ))
            $this->error ( '插件不存在' );
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
            //删除移动的资源文件
            $File = new \com\File();
            $File->del_dir('./static/addons/'.$db_addons ['name']);
            $this->success ( '卸载成功' );
        }
    }

    /**
     * 钩子列表
     */
    public function hooks() {
        $this->assign ( 'meta_title', '钩子列表' );
        $map = $fields = array ();
        $list = $this->lists ( model ( "Hooks" )->field ( $fields ), $map );
        int_to_string ( $list, array (
            'type' => config ( 'hooks_type' )
        ) );
        // 记录当前列表页的cookie
        Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
        $this->assign ( 'list', $list );
        return $this->fetch ();
    }
    public function addhook() {
        $this->assign ( 'data', null );
        $this->assign ( 'meta_title', '新增钩子' );
        return $this->fetch ( 'edithook' );
    }

    // 钩子出编辑挂载插件页面
    public function edithook($id) {
        $hook =  \think\Db::name  ( 'Hooks' )->field ( true )->find ( $id );
        $this->assign ( 'data', $hook );
        $this->assign ( 'meta_title', '编辑钩子' );
        return $this->fetch ( 'edithook' );
    }

    // 超级管理员删除钩子
    public function delhook($id) {
        if ( \think\Db::name  ( 'Hooks' )->delete ( $id ) !== false) {
            $this->success ( '删除成功' );
        } else {
            $this->error ( '删除失败' );
        }
    }
    public function updateHook() {
        $hookModel = model ( 'Hooks' );
        $data = input ();
        $result = $this->validate ( $data, [
            [
                'name',
                'require',
                '钩子名称必须'
            ],
            [
                'description',
                'require',
                '钩子描述必须'
            ]
        ] );
        if (true !== $result) {
            $this->error ( $result );
        }
        if (! empty ( $data ['id'] )) {
            $flag = $hookModel->allowField ( true )->update ( $data );
            if ($flag !== false) {
                cache ( 'hooks', null );
                $this->success ( '更新成功', Cookie ( '__forward__' ) );
            } else {
                $this->error ( '更新失败' );
            }
        } else {
            $flag = $hookModel->allowField ( true )->save ( $data );
            if ($flag) {
                cache ( 'hooks', null );
                $this->success ( '新增成功', Cookie ( '__forward__' ) );
            } else {
                $this->error ( '新增失败' );
            }
        }
    }
    public function execute($_addons = null, $_controller = null, $_action = null) {
        if ( config( 'url_case_insensitive' )) {
            $_addons = ucfirst ( parse_name ( $_addons, 1 ) );
            $_controller = parse_name ( $_controller, 1 );
        }

        $TMPL_PARSE_STRING = config ( 'tmpl_parse_string' );
        $TMPL_PARSE_STRING ['__ADDONS__'] = "/Addons/{$_addons}";
        config( 'tmpl_parse_string', $TMPL_PARSE_STRING );

        if (! empty ( $_addons ) && ! empty ( $_controller ) && ! empty ( $_action )) {
            $Addons = controller ( "Addons://{$_addons}/{$_controller}" )->$_action ();
        } else {
            $this->error ( '没有指定插件名称，控制器或操作！' );
        }
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
            $model = $addonModel->model;
            $this->assign ( 'model', $model );
        }
        if ($id) {
            $data = $addonModel->find ( $id );
            $data || $this->error ( '数据不存在！' );
            $this->assign ( 'data', $data );
        }

        if (Request::instance()->isPost()) {
            // 获取模型的字段信息
            $data = Request::instance()->post();
            $flag = $addonModel->updates ();
            $flag || $this->error($addonModel->getError());
            $this->success(!empty($data['id'])?"编辑{$model['title']}成功！":"添加{$model['title']}成功！", Cookie('__forward__'));
        } else {
            $fields = $addonModel->_fields;
            $this->assign ( 'fields', $fields );
            $this->assign ( 'meta_title', $id ? '编辑' . $model ['title'] : '新增' . $model ['title'] );

            if ($id)
                $template = $model ['template_edit'] ? $model ['template_edit'] : '';
            else
                $template = $model ['template_add'] ? $model ['template_add'] : '';
            if ($template)
                return $this->fetch ( $addon->addon_path . $template );
            else
                return $this->fetch ();
        }
    }
    public function del($id = '', $name) {
        $ids = array_unique ( ( array ) input ( 'ids/a', 0 ) );

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
            // $addonModel = model("Addons://{$name}/{$model}");
            if (! $addonModel)
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
