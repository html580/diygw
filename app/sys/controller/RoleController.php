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
use app\sys\model\DictModel;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\controller
 */
class RoleController extends BaseController
{
    //是否初始化模型
    public $isModel = true;
    //是否导出
    public $isExport = false;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];

    /**
     * 加工导出表结构
     * @param $fields
     * @return mixed
     */
    public function getExportFields($fields){
        //去除多余的字段
        $excludeFields = ['flag'];
        $exportFields = [];
        foreach ($fields as $field){
            if(in_array($field['name'],$excludeFields)){
                continue;
            }
            if($field['name']=='role_d'){
                $field['comment'] = '编号';
            }
            if($field['name']=='remark'){
                $field['comment'] = '备注';
            }
            if($field['name']=='data_scope'){
                $field['comment'] = '数据权限范围';
            }
            $exportFields[] = $field;
        }
        //比如字典转换数据 可以参照导出角色RoleController里的getExportFields方法的处理。
        return $exportFields;
    }

    /**
     * 获取导出表对应字段转换的数据
     * @return array
     */
    public function getExportFieldsData(){
        //比如字典转换数据 可以参照导出角色里的getExportFieldsData方法的处理。
        $fieldsData = [];
        $fieldsData['dataScope']= [['value'=>'1','label'=>'全部数据权限'],['value'=>'2','label'=>'自定数据权限'],['value'=>'3','label'=>'本部门数据权限'],['value'=>'4','label'=>'本部门及以下数据权限'],['value'=>'5','label'=>'仅本人数据权限']];
        $dictDataModel  = new DictDataModel();
        $fieldsData['status']= $dictDataModel->getDicts('sys_normal_disable');
        return $fieldsData;
    }
}
