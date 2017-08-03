<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
namespace app\common\validate;
use think\Validate;
/**
 *  文档模型 自动验证基础模型
 */
class Base extends Validate{
    /*
     * 模型验证规则和验证场景
     * @$fields 模型属性信息
     * ['rule'=>验证规则,scene=>验证场景,scene_fields=>验证字段(array)]
     */
    public function Validationrules($arr){
        //验证规则
        if($arr['rule'])
            $this->rule($arr['rule']);
        //验证场景
        $scene_field = $this->getScene($arr['scene']);//获取当前场景验证字段
        if($scene_field && $arr['scene_fields']){
            $new_arr[$arr['scene']] =implode(',',array_merge($scene_field, $arr['scene_fields']));
            $this->scene($new_arr);
        }
        $this->scene($arr['scene']);
    }
}