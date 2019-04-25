<?php
return [
    'title' => [//配置在表单中的键名 ,这个会是config[title]
        'title' => '显示标题:',//表单的文字
        'type' => 'text',         //表单的类型：text、textarea、checkbox、radio、select等
        'value' => '测试例子',             //表单的默认值
    ],
    'display' => [//配置在表单中的键名 ,这个会是config[display]
        'title' => '是否显示:',//表单的文字
        'type' => 'radio',         //表单的类型：text、textarea、checkbox、radio、select等
        'options' => [         //select 和radion、checkbox的子选项
            '1' => '显示',         //值=>文字
            '0' => '不显示',
        ],
        'value' => '1',             //表单的默认值
    ],
];
					