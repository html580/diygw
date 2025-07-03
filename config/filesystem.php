<?php

return [
    // 默认磁盘
    'default' => 'local',
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRootPath() . 'public/storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        /**
         * 上传设置
         */
        'upload' => [
            'image' => 'fileSize:' . 1024 * 1024 * 5 . '|fileExt:jpg,png,gif,jpeg',
            'video' => 'fileSize:' . 1024 * 1024 * 500 . '|fileExt:rm,rmvb,wmv,avi,mpg,mpeg,mp4',
            'mp3' => 'fileSize:' . 1024 * 1024 * 500 . '|fileExt:mp3,wma,wav,amr',
            'file' => 'fileSize:' . 1024 * 1024 * 500 . '|fileExt:doc,docx,xls,xlsx,ppt,pptx,txt,pdf,zip,rar'
        ],
        // 更多的磁盘配置信息
        'qiniu' => [
            'bucket' => '',
            'access_key' => '',
            'secret_key' => '',
            'domain' => 'http://'
        ],
        'aliyun' => [
            'bucket' => '',
            'access_key_id' => '',
            'access_key_secret' => '',
            'domain' => 'http://'
        ],
        // 腾讯云配置
        'qcloud' => [
            'bucket' => '',
            'region' => '',
            'secret_id' => '',
            'secret_key' => '',
            'domain' => 'http://'
        ]
    ],
];
