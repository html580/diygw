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
class DeptModel extends DiygwModel
{
    // 表名
    public $name = 'sys_dept';
    protected $likeField=['deptName'];
    //不分页，全部数据直接返回
    protected $paginate = false;

    public function beforeAdd(&$data){
       
        return true;
    }

    public function afterAdd(&$data){
        if($data['parentId']=='0'){
            $data['deptPath'] = '0';
        }else{
            $parent = $this->find(['deptId'=>$data['parentId']]);
            if(empty($parent['deptPath'])){
                $parent['deptPath'] = "0";
            }
            $data['deptPath'] = $parent['deptPath'].",".$data['deptId'];
            static::update(['deptPath'=>$data['deptPath']], ['dept_id'=>$data['deptId']]);
        }
        return true;
    }
    
}
