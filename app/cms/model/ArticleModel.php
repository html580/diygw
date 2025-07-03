<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2024 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\cms\model;

use diygw\model\DiygwModel;

/**
 * @package app\cms\model
 */
class ArticleModel extends DiygwModel
{
    // 表名
    public $name = 'cms_article';

    // 相似查询字段
    protected $likeField=[];
}
