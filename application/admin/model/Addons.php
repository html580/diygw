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
 * 插件模型
 * @author 艺品网络  <twothink.cn>
 */

class Addons extends Model {
	protected $autoWriteTimestamp = false;
    protected $auto = [
			'create_time'
	];
	protected function setCreateTimeAttr($value) {
		return time ();
	}

	/**
	 * 获取插件列表
	 *
	 * @param string $addon_dir
	 */
	public function getList($addon_dir = '') {
		if (! $addon_dir)
			$addon_dir = TWOTHINK_ADDON_PATH;
		$dirs = array_map ( 'basename', glob ( $addon_dir . '*', GLOB_ONLYDIR ) );
		if ($dirs === FALSE || ! file_exists ( $addon_dir )) {
			$this->error = '插件目录不可读或者不存在';
			return FALSE;
		}
		$addons = array ();
		$where ['name'] = array (
				'in',
				$dirs
		);
		$list = $this->where ( $where )->field ( true )->select ();
		foreach ( $list as $key => $value ) {
			$list [$key] = $value->toArray ();
		}
		foreach ( $list as $addon ) {
			$addon ['uninstall'] = 0;
			$addons [$addon ['name']] = $addon;
		}
		foreach ( $dirs as $value ) {
			if (! isset ( $addons [$value] )) {
				$class = get_addon_class ( $value );
				if (! class_exists ( $class )) { // 实例化插件失败忽略执行
					trace($class);
					\think\Log::record ( '插件' . $value . '的入口文件不存在！' );
					continue;
				}
				$obj = new $class ();
				$addons [$value] = $obj->info;
				if ($addons [$value]) {
					$addons [$value] ['uninstall'] = 1;
					unset ( $addons [$value] ['status'] );
				}
			}
		}
		int_to_string ( $addons, array (
				'status' => array (
						- 1 => '损坏',
						0 => '禁用',
						1 => '启用',
						null => '未安装'
				)
		) );
		$addons = list_sort_by ( $addons, 'uninstall', 'desc' );
		return $addons;
	}

    /**
     * 获取插件的后台列表
     */
    public function getAdminList(){
        $admin = array();
        $db_addons = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if($db_addons){
            foreach ($db_addons as $value) {
            	$value=$value->toArray();
                $admin[] = array('title'=>$value['title'],'url'=>"Addons/adminList?name={$value['name']}");
            }
        }
        return $admin;
    }
}
