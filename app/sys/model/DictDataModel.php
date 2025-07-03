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
class DictDataModel extends DiygwModel
{
    // 表名
    public $name = 'sys_dict_data';

    public function getDicts($dictType){
        $dictDataModel  = new DictDataModel();
        $dicts = $dictDataModel->where('dict_type',$dictType)->field("dict_value value,dict_label label")->select()->toArray();
        return $dicts;
    }
}
