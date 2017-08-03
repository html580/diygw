<?php

namespace addons\thirdlogin;
use app\common\controller\Addon;

/**
 * 快捷登录插件插件
 * @author thinkphp
 */

class Thirdlogin extends Addon{

    public $info = array(
        'name'=>'thirdlogin',
        'title'=>'快捷登录插件',
        'description'=>'目前登录平台为：腾讯QQ，其它请自行添加',
        'status'=>1,
        'author'=>'thinkphp',
        'version'=>'0.1'
    );

    public function install(){
      $db_prefix = config('database.prefix');
      $table_name = "{$db_prefix}login";
    	$sql =<<<SQL
      CREATE TABLE `{$table_name}` (
        `login_id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) NOT NULL COMMENT '用户UID',
        `type_uid` varchar(255) NOT NULL COMMENT '授权登陆用户名 第三方分配的appid',
        `type` char(80) NOT NULL COMMENT '登陆类型 qq|sina',
        `oauth_token` varchar(150) DEFAULT NULL COMMENT '授权账号',
        `oauth_token_secret` varchar(150) DEFAULT NULL COMMENT '授权密码',
        PRIMARY KEY (`login_id`),
        KEY `uid` (`uid`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;
      \think\Db::execute($sql);
      if(count(db()->query("SHOW TABLES LIKE '{$table_name}'")) != 1){
          session('addons_install_error', ',login表未创建成功，请手动检查插件中的sql，修复后重新安装');
          return false;
      }

      return true;
    }

    public function uninstall(){
    	// 数据库表前缀
    	$db_prefix = config('database.prefix');
      $table_name = "{$db_prefix}login";
    	$sql = "DROP TABLE IF EXISTS `{$table_name}`;";
      \think\Db::execute($sql);
      return true;

    }

    //实现的Login钩子方法
    public function thirdLogin($param){
        $config = $this->getConfig();
        $this->assign('third_login',$config['login_plugin']);
        return $this->fetch('oauth');
    }

    //实现的pageHeader钩子方法
    public function pageHeader($param){
			$config = $this->getConfig();
			return $config['platformMeta'];
    }


}
