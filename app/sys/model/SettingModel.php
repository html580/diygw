<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2024 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\sys\model;


use diygw\model\DiygwModel;

/**
 * @package app\sys\model
 */
class SettingModel extends DiygwModel
{
    // 表名
    public $name = 'sys_setting';

    // 相似查询字段
    protected $likeField=[];

    public function add(&$data){
        $settingModel = new SettingModel();
        $config = $settingModel->where('key',$data['key'])->find();
        if($config){
           parent::update($this->filterData($data), ['key' => $data['key']]);
        }else{
           parent::add($data);
        }
        return $data;
    }
}
