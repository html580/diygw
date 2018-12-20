<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace think;

use think\Request;
use think\modelinfo\Quiet;
use think\modelinfo\System;
/**
 * 模型解析通用类
 * @author diygw <diygwcom@foxmail.com>
 */
class ModelInfo{
    /*
     * 模型解析 快速 实例化对象
     * @param $model_info 模型ID或模型定义规则
     * @param $returnmodel   true 是否返回当前模型信息
     * @param $status true 是否查询父级模型(模型ID时有效)
     * @return obj 返回实例化对象
     */
    public function info($model_info,$returnmodel=false,$status=true){

        if(is_array($model_info)){
            $class = (new Quiet())->info($model_info,$returnmodel);
        }else{
            $class = (new System())->info($model_info,$returnmodel,$status);
        }
        return $class;
    }
    /**
     * 通用列表查询
     * @param array|int    $model_info 模型定义规则 或者  系统模型ID
     * @param int          $type       模式 1单线继承模型 2为V类型模型
     * return  array
     * @author diygw diygwcom@foxmail.com <diygw.cn>
     */
    public function getList($model_info=false,$type=true){
        $model_obj = $this->info($model_info,true);
        if(is_numeric($model_info)){
            $model_obj->type =$type;
        }
        if(Request()->isPost()) {
            $Modelinfo = $model_obj->scene(Request()->action())
                ->getSearchList()
                ->getWhere()
                ->getViewList()
                ->parseIntTostring()
                ->parseList()
                ->parseListIntent()
                ->getParam('info');
        }else{
            $Modelinfo = $model_obj->scene(Request()->action())
                ->getButton()
                ->getListField()
                ->getSearchList()
                ->getParam('info');
        }
        return $Modelinfo;
    }
    /**
     * 通用模型新增
     * @param $model_info 模型定义规则 或者  系统模型ID
     * @return $model_info 解析后的模型规则
     * @author diygw diygwcom@foxmail.com
     */
    public function getAdd($model_info){
        $model_obj = $this->info($model_info,true,false);
        $model_info = $model_obj->getFields()->FieldDefaultValue()->setInit()->getParam('info');
        return $model_info;
    }
    /**
     * 通用模型编辑
     * @param $model_info 模型定义规则 或者  系统模型ID
     * @param $where      查询条件
     * @param $layer      业务层名称
     * @author diygw <diygw.cn>
     */
    public function getEdit($model_info,$where = false,$layer='model'){
        $param = Request()->param();
        if(!$where){
            $where = ['id'=>$param['id']];
        }
        $model_obj = $this->info($model_info,true,true);
        $model_info = $model_obj->getFields()->getQueryModel($layer)->getFind($where)->setInit()->getParam('info');
        return $model_info;
    }
    /**
     * 新增和更新数据
     * $model_info 模型定义规则 或者  系统模型ID
     * $laye       模型分层
     * @return 返回状态 成功返回操作状态信息 失败返回 false
     * @author diygw <diygw.cn>
     */
    public function getUpdate($model_info,$laye = 'model'){
        //获取模型信息
        $model_obj = $this->info($model_info);

        //自动验证
        if(!$validate = $model_obj->getFields()->checkValidate()){
            $this->error = $model_obj->getError();
            return false;
        }

        if(!$res = $model_obj->getQueryModel($laye)->getUpdate()){
            $this->error = $model_obj->getError();
            return false;
        }
        return true;
    }
    /**
     * 返回模型的错误信息
     * @access public
     * @return string|array
     */
    public function getError()
    {
        return $this->error;
    }
}