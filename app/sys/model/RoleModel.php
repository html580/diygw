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
class RoleModel extends DiygwModel
{
    // 表名
    public $name = 'sys_role';

    protected $likeField=['roleName','roleKey'];

    public function afterGet($data){
        //获取角色对应菜单权限ID
        $roleMenus =  RoleMenuModel::where('role_id',$data['roleId'])->select()->toArray();
        $menus = [];
        foreach ($roleMenus as $menu){
            $menus[]=$menu['menuId'];
        }
        $data['menuIds'] = $menus;

        //获取角色对应部门权限ID
        $roleDepts =  RoleDeptModel::where('role_id',$data['roleId'])->select()->toArray();
        $depts = [];
        foreach ($roleDepts as $dept){
            $depts[]=$dept['deptId'];
        }
        $data['deptIds'] = $depts;
        return $data;
    }


    public function setRoleMenu($data){
        RoleMenuModel::where('role_id',$data['roleId'])->delete();
        $menuIds = $data['menuIds'];
        $roleMenus = [];
        foreach ($menuIds as $menuId){
            $roleMenus[]=['menuId'=>$menuId,'roleId'=>$data['roleId']];
        }
        $roleMenuModel = new RoleMenuModel();
        $roleMenuModel->saveAll($roleMenus);
    }

    public function setRoleDept($data){
        RoleDeptModel::where('role_id',$data['roleId'])->delete();
        $deptIds = $data['deptIds'];
        $roleDepts = [];
        foreach ($deptIds as $deptId){
            $roleDepts[]=['deptId'=>$deptId,'roleId'=>$data['roleId']];
        }
        $roleDeptModel = new RoleDeptModel();
        $roleDeptModel->saveAll($roleDepts);
    }
    public function afterAdd(&$data){
        $this->setRoleMenu($data);
    }

    public function afterEdit(&$data){
        $this->setRoleMenu($data);
        $this->setRoleDept($data);
    }


}
