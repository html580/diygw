<?php
return array(
	'login_plugin'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'开启同步登录功能',//表单的文字
		'type'=>'checkbox',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'sina'=>'新浪微博',		 //值=>文字
			'qq'=>'QQ互联',
		),
		'value'=>array('sina','qq'),			 //表单的默认值
	),
    'platformMeta' => array(
        'title'=>'接口验证代码',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'<meta />',			 //表单的默认值
        'tip' => '需要在Meta标签中写入验证信息时，拷贝代码到这里。'
    ),
    'group'=>array(
		'type'=>'group',
		'options'=>array(
			'sina'=>array(
				'title'=>'新浪微博',
				'options'=>array(
					'sina_wb_akey'=>array(
						'title'=>'新浪微博KEY:',
						'type'=>'text',
						'value'=>'',
						'tip'=>'申请地址：http://open.weibo.com/'
					),
					'sina_wb_skey'=>array(
						'title'=>'新浪微博密匙:',
						'type'=>'text',
						'value'=>''
					)
				)
			),
            'qq'=>array(
				'title'=>'QQ互联',
				'options'=>array(
					'qq_qzone_akey'=>array(
						'title'=>'QQ互联KEY:',
						'type'=>'text',
						'value'=>'',
						'tip'=>'申请地址：http://connect.qq.com'
					),
					'qq_qzone_skey'=>array(
						'title'=>'QQ互联密匙:',
						'type'=>'text',
						'value'=>''
					)
				)
			)
		)
    )
);
					