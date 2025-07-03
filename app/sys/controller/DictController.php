<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2022 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\sys\controller;

use app\BaseController;
use app\sys\model\DictDataModel;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\controller
 */
class DictController extends BaseController
{
    //是否初始化模型
    public $isModel = true;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];

    /**
     * 获取导出表对应字段转换的数据
     * @return array
     */
    public function getExportFieldsData(){
        //比如字典转换数据 可以参照导出角色里的getExportFieldsData方法的处理。
        $fieldsData = [];
        $dictDataModel  = new DictDataModel();
        $fieldsData['status']= $dictDataModel->getDicts('sys_normal_disable');
        return $fieldsData;
    }
}
