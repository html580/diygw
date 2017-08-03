<?php
//配置文件
return [
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'session_id',
        // SESSION 前缀
        'prefix'         => '',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => 'diygw_home_',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],
	// +----------------------------------------------------------------------
	// | 模板替换
	// +----------------------------------------------------------------------
	'view_replace_str'  =>  [
		'__PUBLIC__'=>__ROOT__.'/static',
		'__STATIC__' => __ROOT__.'/static/static',
		'__ADDONS__' => '/addons',
		'__IMG__'    =>__ROOT__.'/static/home/images',
		'__CSS__'    => __ROOT__.'/static/home/css',
		'__JS__'     => __ROOT__.'/static/home/js',
	],
		// +----------------------------------------------------------------------
		// | 模板设置
		// +----------------------------------------------------------------------

		'template'               => [
				// 模板引擎类型 支持 php think 支持扩展
				'type'         => 'Think',
				// 模板路径
 				'view_path'    => APP_PATH.'user/view/default/',
				// 模板后缀
				'view_suffix'  => 'html',
				// 模板文件名分隔符
				'view_depr'    => DS,
				// 模板引擎普通标签开始标记
				'tpl_begin'    => '{',
				// 模板引擎普通标签结束标记
				'tpl_end'      => '}',
				// 标签库标签开始标记
				'taglib_begin' => '{',
				// 标签库标签结束标记
				'taglib_end'   => '}',
				// 预先加载的标签库
				'taglib_pre_load'     =>    'app\common\taglib\Think,app\common\taglib\Article',
		],

        /**
         * 附件相关配置
         * 附件是规划在插件中的，所以附件的配置暂时写到这里
         * 后期会移动到数据库进行管理
         */
        'ATTACHMENT_DEFAULT' => [
            'is_upload'     => true,
            'allow_type'    => '0,1,2', //允许的附件类型 (0-目录，1-外链，2-文件)
            'driver'        => 'Local', //上传驱动
            'driver_config' => null, //驱动配置
        ], //附件默认配置

        'ATTACHMENT_UPLOAD' => [
            'mimes'    => '', //允许上传的文件MiMe类型
            'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
            'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
            'autoSub'  => true, //自动子目录保存文件
            'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Uploads/Attachment/', //保存根路径
            'savePath' => '', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt'  => '', //文件保存后缀，空则使用原后缀
            'replace'  => false, //存在同名是否覆盖
            'hash'     => true, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
        ], //附件上传配置（文件上传类配置）
];
