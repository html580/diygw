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
use app\sys\model\MenuModel;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\controller
 */
class MenuController extends BaseController
{
    //是否初始化模型
    public $isModel = true;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];

    public function copy(){
        $data = $this->request->param();
        $pageData = $this->model->copy($data,false);
        if($pageData){
            $datas =  $this->model->where('parent_id',$data['id'])->select()->toArray();
            foreach ($datas as $tmp){
                unset($tmp['menuId']);
                $tmp['parentId'] = $pageData['menuId'];
                $model = new MenuModel();
                $model->save($tmp);
            }
            return $this->success("复制成功");
        }else{
            return $this->error("复制失败");
        }
    }

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
            if($field['name']=='menu_id'){
                $field['comment'] = '编号';
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
        $dictDataModel  = new DictDataModel();
        $fieldsData['status']= $dictDataModel->getDicts('sys_normal_disable');
        return $fieldsData;
    }
}
