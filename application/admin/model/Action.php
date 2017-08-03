<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络 
// +----------------------------------------------------------------------

namespace app\admin\model;
use think\Model;

/**
 * 行为模型
 * @author 艺品网络  <twothink.cn>
 */

class Action extends Model { 
	protected $autoWriteTimestamp = false;
	//新增及更新
	protected $auto = ['update_time'];
	// 新增
	protected $insert = ['status'=>1]; 
	protected function setUpdateTimeAttr($value)
	{
		return time();
	}

    /**
     * 新增或更新一个行为
     * @return boolean fasle 失败 ， int  成功 返回完整的数据 
     */
    public function updates(){
        /* 获取数据对象 */
    	$data = input();
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据
            $id = $this->save($data); //添加行为
            if(!$id){ 
                return false;
            }
        } else { //更新数据
            $status = $this->update($data); //更新基础内容
            if(false === $status){ 
                return false;
            }
        }
        //删除缓存
        cache('action_list', null); 
        //内容添加或更新完成
        return $data;

    }

}
