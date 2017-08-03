<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------
namespace app\common\controller;
class CategoryApi {
    /**
     * 获取分类信息并缓存分类
     * @param  integer $id    分类ID
     * @param  string  $field 要获取的字段名
     * @return string         分类信息
     */
    public static function get_category($id, $field = null){
        static $list;

        /* 非法分类ID */
        if(empty($id) || !is_numeric($id)){
            return '';
        }

        /* 读取缓存数据 */
        if(empty($list)){
            $list = config('sys_category_list');
        }

        /* 获取分类名称 */
        if(!isset($list[$id])){
            $cate = \think\Db::name('Category')->find($id);
            if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
                return '';
            }
            $list[$id] = $cate;
            cache('sys_category_list', $list); //更新缓存
        }
        return is_null($field) ? $list[$id] : $list[$id][$field];
    }

    /* 根据ID获取分类标识 */
    public static function get_category_name($id){
        return get_category($id, 'name');
    }

    /* 根据ID获取分类名称 */
    public static function get_category_title($id){
        return get_category($id, 'title');
    }

    /**
     * 获取参数的所有父级分类
     * @param int $cid 分类id
     * @return array 参数分类和父类的信息集合
     * @author 艺品网络  <twothink.cn>
     */
    public static function get_parent_category($cid){
        if(empty($cid)){
            return false;
        }
        $cates  =   \think\Db::name('Category')->where(array('status'=>1))->field('id,title,pid')->order('sort')->select();
        $child  =   get_category($cid);	//获取参数分类的信息
        $pid    =   $child['pid'];
        $temp   =   array();
        $res[]  =   $child;
        while(true){
            foreach ($cates as $key=>$cate){
                if($cate['id'] == $pid){
                    $pid = $cate['pid'];
                    array_unshift($res, $cate);	//将父分类插入到数组第一个元素前
                }
            }
            if($pid == 0){
                break;
            }
        }
        return $res;
    }
}
