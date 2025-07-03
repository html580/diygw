<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2022 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\sys\model;

use diygw\model\DiygwModel;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\model
 */
class StorageModel extends DiygwModel
{
    // 表名
    public $name = 'sys_storage';

    // 相似查询字段
    protected $likeField=['name'];
}
