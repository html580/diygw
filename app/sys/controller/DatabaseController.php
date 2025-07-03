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
use think\facade\Console;
use think\facade\Db;
use think\helper\Str;

/**
 * @package app\sys\controller
 */
class DatabaseController extends BaseController
{
    //是否初始化模型
    public $isModel = false;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = [];
    //是否返回所有数据
    public $isAll = true;

    public function list()
    {
        $tables = Db::query('SHOW TABLE STATUS');
        $result = [];
        $searchName = $this->request->param('name');
        foreach ($tables as $table){
            foreach ($table as $key=>$value){
                $table[Str::camel($key)] = $value;
                unset($table[$key]);
            }
            //查找某张表
            if(!empty($searchName)){
                if (strpos($table['name'], $searchName) !== false) {
                    $result[]=$table;
                }
            }else{
                $result[]=$table;
            }
        }
        return $this->pageData(['rows'=>$result,'total'=>count($result)]);
    }

    public  function  generate(){
        $names= $this->request->param('names');
        $type= $this->request->param('type');
        if(!is_array($names)){
            $names = [$names];
        }
        $datas = [];
        foreach ($names as $name){
            $name = strtolower($name);
            $firstLetter= getFirstLetter($name);
            if(!empty($firstLetter)){
                $result = $firstLetter.'@'.Str::studly(getAfterFirstUnderscore($name));
                $output = Console::call($type,['name' => $result]);
                $data = $output->fetch();
                $datas= array_merge($datas,explode("\n",$data));
            }
        }
        return $this->successData($datas);
    }
}
