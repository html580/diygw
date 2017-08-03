<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.TwoThink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络 <http://www.TwoThink.cn>
// +----------------------------------------------------------------------

namespace app\home\widget;
use think\controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class Category extends Controller{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($cate, $child = false){
		$field = 'id,name,pid,title,link_id';
		if($child){
			$category = model('Category')->getTree($cate, $field);
			$category = $category['_'];
		} else {
			$category = model('Category')->getSameLevel($cate, $field);
		}
		$this->assign('category', $category);
		$this->assign('current', $cate);
		return $this->fetch('category/lists');
	}
	
}
