<?php
namespace app\install\controller;
use app\BaseController;
use think\Db;
use think\db\Query;
use think\DbManager;
use think\helper\Str;

class IndexController extends BaseController
{
    //判断是否全部不需要登录
    public $notNeedLoginAll = true;
    public $isModel = false;

    /**
     * 主页
     */
    public function index()
    {
        return view();
    }

    /**
     * 安装环境检测
     */
    public function setup1()
    {
        $this->assign('check_env', check_env());
        $this->assign('check_func', check_func());
        $this->assign('check_dirfile', check_dirfile());
        return view();
    }

    /**
     * 安装程序
     */
    public function setup2()
    {
        return view();
    }

    /**
     * 开始安装
     */
    public function setup3()
    {
        echo $this->fetch();
        if (file_exists('../config/install.lock')) {
            install_show_msg('安装程序执行完毕！重新安装需要删除config/install.lock', false);
            return;
        }
        //检测信息
        $data = input('post.');
      
        if (!$data['db']['hostname']) {
            install_show_msg('请填写数据库地址！', false);
        }
        if (!$data['db']['hostport']) {
            install_show_msg('请填写数据库端口！', false);
        }
        if (!$data['db']['database']) {
            install_show_msg('请填写数据库名称！', false);
        }
        if (!$data['db']['username']) {
            install_show_msg('请填写数据库用户名！', false);
        }
        if (!$data['username']) {
            install_show_msg('请填写用户名/邮箱！', false);
        }
        if (!$data['password']) {
            install_show_msg('请填写密码！', false);
        }
        if (!$data['password2']) {
            install_show_msg('请填写重复密码！', false);
        }
        if ($data['password'] != $data['password2']) {
            install_show_msg('重复密码不匹配！', false);
        }

        // 缓存数据库配置
        session('db_config', $data['db']);
        $db_name = $data['db']['database'];
        $config = [
            // 默认使用的数据库连接配置
            'default'         => 'mysql',
            // 自定义时间查询规则
            'time_query_rule' => [],
            // 自动写入时间戳字段
            'auto_timestamp'  => false,
            // 时间字段取出后的默认时间格式
            'datetime_format' => 'Y-m-d H:i:s',
            // 数据库连接配置信息
            'connections'     => [
                'mysql' => [
                    // 数据库类型
                    'type'              => 'mysql',
                    // 服务器地址
                    'hostname'          => $data['db']['hostname'],
                    // 数据库名
                    'database'          => '',
                    // 用户名
                    'username'          => $data['db']['username'],
                    // 密码
                    'password'          => $data['db']['password'],
                    // 端口
                    'hostport'          => $data['db']['hostport'],
                    // 数据库连接参数
                    'params'            => [],
                    // 数据库编码默认采用utf8
                    'charset'           => 'utf8',
                    // 数据库表前缀
                    'prefix'            => 'mk_',
                    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
                    'deploy'            => 0,
                    // 数据库读写是否分离 主从式有效
                    'rw_separate'       => false,
                    // 读写分离后 主服务器数量
                    'master_num'        => 1,
                    // 指定从服务器序号
                    'slave_no'          => '',
                    // 是否严格检查字段是否存在
                    'fields_strict'     => true,
                    // 是否需要断线重连
                    'break_reconnect'   => false,
                    // 监听SQL
                    'trigger_sql'       => env('app_debug', true),
                    // 开启字段缓存
                    'fields_cache'      => false,
                    // 字段缓存路径
                    'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
                ]
            ],
        ];

        // 创建数据库连接
        $db = new DbManager();
        $db->setConfig($config);
        $db_instance = $db->connect('mysql');

        try{
            $db_instance->execute('select version()');
        }catch(\Exception $e){
            install_show_msg('数据库连接失败，请检查连接信息是否正确！', false);
        }

        $result = $db_instance->execute('SELECT * FROM information_schema.schemata WHERE schema_name="'.$db_name.'"');

        if ($result && isset($data['db']['create'])) {
            install_show_msg('该数据库'.$db_name.'已存在，请更换名称！');
            return;
        }else if(!$result && !isset($data['db']['create'])){
            install_show_msg('该数据库'.$db_name.'不存在，请更换名称！');
            return;
        }

        if(isset($data['db']['create'])){
            // 创建数据库
            $sql2 = "CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8";
            $db_instance->execute($sql2) || install_show_msg($db_instance->getError(), false);
        }

        //修改数据库配置文件
        write_config(session('db_config'));

        $db = new DbManager();
        $config['connections']['mysql']['database'] = $db_name;
        $db->setConfig($config);
        $db_instance = $db->connect('mysql');
        // 开始安装
        $file = root_path().'diygw.sql';
        $sqlData = get_mysql_data($file, '', '');
        foreach ($sqlData as $sql) {
            $db_instance->execute($sql);
        }

        //创建超级管理员
        $salt =  Str::random(6);
        $db_instance->table('sys_user')->where('user_id',1)->update(['username' => $data['username'],'salt'=>$salt,'password'=>md5($data['password'].$salt)]);
        install_show_msg('超级管理员创建完成...');

        //创建文件锁
        file_put_contents('../config/install.lock', '');

        //安装完毕
        install_show_msg('安装程序执行完毕！重新安装需要删除config/install.lock');
        $adminUrl = url('/super/index');
        echo "<script type=\"text/javascript\">insok(\"{$adminUrl}\")</script>";
    }


}