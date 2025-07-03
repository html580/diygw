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
use app\sys\model\UserModel;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\sys\controller
 */
class UserController extends BaseController
{
    //是否初始化模型
    public $isModel = true;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];
    protected $pkField = "";
    protected $orderField ='';
    protected $equalField="";
    protected $likeField="";

    /**
     * 查询列表之前增加一些过滤条件
     */
    public function beforeList(&$param)
    {
        return true;
    }

   /**
     * 对查询结果进行处理
     */
    public function afterList(&$pageData){
        return true;
    }

    /**
     * 查询所有结果之前增加一些过滤条件，默认不开启直接查询全部结果，请自行开启
     */
    public function beforeAll(&$model)
    {
        return false;
    }

    /**
     * 对查询所有结果进行处理
     */
    public function afterAll(&$pageData){
        return true;
    }

    /**
     * 新增之前进行处理
     */
    public function beforeAdd(&$dataArr){
        return true;
    }

    /**
     * 新增之后进行处理
     */
    public function afterAdd(&$dataArr){
        return true;
    }

    /**
     * 新增之前进行处理
     */
    public function beforeUpdate(&$dataArr){
        return true;
    }

    /**
     * 新增之后进行处理
     */
    public function afterUpdate(&$dataArr){
        return true;
    }

    /**
     * 获取数据之前进行处理
     */
    public function beforeGet(&$param){
        return true;
    }

    /**
     * 获取数据进行处理
     */
    public function afterGet(&$param){
        return true;
    }


   /**
    * 删除之前进行处理
    */
   public function beforeDelete(&$id){
       return true;
   }

   /**
    * 删除之后进行处理
    */
   public function afterDelete(&$id){
       return true;
   }

   public function status(){
       $this->model->where('user_id',$this->request->param('userId'))->update(['status'=>$this->request->param('status')]);
       return $this->success("修改成功");
   }
}
