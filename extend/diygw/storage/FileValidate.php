<?php
declare (strict_types=1);

namespace diygw\storage;

/**
 * 文件上传验证类
 * Class FileValidate
 * @package diygw\storage
 */
class FileValidate extends \think\Validate
{
    // 验证规则
    protected $rule = [
        // 图片文件: jpg,jpeg,png,bmp,gif
        // 文件大小: 2MB = (1024 * 1024 * 2) = 2097152 字节
        'image' => 'filesize:104857600|fileExt:jpg,jpeg,png,bmp,gif',
        // 视频文件: mp4
        // 文件大小: 10MB = (1024 * 1024 * 10) = 10485760 字节
        'video' => 'filesize:104857600|fileExt:mp4',
    ];

    // 错误提示信息
    protected $message = [
        'image.filesize' => '图片文件大小不能超出100MB',
        'image.fileExt' => '图片文件扩展名有误',
        'video.filesize' => '视频文件大小不能超出100MB',
        'video.fileExt' => '视频文件扩展名有误',
    ];

    // 验证场景
    protected $scene = [
        'image' => ['image'],
        'video' => ['video'],
    ];
}
