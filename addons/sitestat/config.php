<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:diygw <diygwcom@foxmail.com>
// +----------------------------------------------------------------------

return [
	'title'=>[//配置在表单中的键名 ,这个会是config[title]
		'title'=>'显示标题:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'系统信息',			 //表单的默认值
    ],
	'width'=>[
		'title'=>'显示宽度:',
		'type'=>'select',
		'options'=>[
			'1'=>'1格',
			'2'=>'2格',
			'4'=>'4格'
		],
		'value'=>'2'
	],
	'display'=>[
		'title'=>'是否显示:',
		'type'=>'radio',
		'options'=>[
			'1'=>'显示',
			'0'=>'不显示'
		],
		'value'=>'1'
	]
];
