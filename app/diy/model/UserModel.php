<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2023 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\diy\model;

use diygw\FileUtil;
use diygw\model\DiygwModel;
use think\helper\Str;

/**
 * @mixin \diygw\model\DiygwModel
 * @package app\diy\model
 */
class UserModel extends DiygwModel
{
    // 表名
    public $name = 'diy_user';

    // 相似查询字段
    protected $likeField=[];

    public function beforeAdd(&$data){

        //如果是从微信小程序登录，没有此信息
        if(!isset($data['username'])&&empty($data['username'])){
            $data['username'] = getOrderNo();
        }
        //如果密码不为空，设置密码
        if(isset($data['password'])&&!empty($data['password'])){
            $salt =  Str::random(6);
            $data['salt'] = $salt;
            $data['password'] = md5($data['password'].$salt);
        }
        if(isset($data['avatar'])&&!empty($data['avatar'])){
            $data['avatar'] = $this->setBaseToImg($data['avatar']);
        }
        return true;
    }

    public function setBaseToImg($base64_image_content){
        //匹配出图片的格式
        $preg = preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result);
        if ($preg){
            $type = $result[2];
            $new_file = str_replace('\\', '/',  \config('filesystem.disks.local.root'). DIRECTORY_SEPARATOR .'avatar'.DIRECTORY_SEPARATOR);
            if(!file_exists($new_file))
            {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                FileUtil::mk_dirs($new_file);
            }
            $new_file = $new_file.uniqid().".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return  $url = $this->getLocalPath($new_file);;
            }else{
                return false;
            }
        }else{
            return $base64_image_content;
        }
    }

    public function beforeEdit(&$data){
        if(isset($data['avatar'])&&!empty($data['avatar'])){
            $data['avatar'] = $this->setBaseToImg($data['avatar']);
        }
        return true;
    }


    /**
     *
     * @param $data
     * @param string $field
     * @return bool
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function edit(&$data)
    {
        try {
            $this->startTrans();
            $pk = $this->pk;
            $id = $data[Str::camel($pk)];

            if(!empty($data['password'])){
                if(isset($data['newpassword'])){
                    if(empty($data['newpassword'])){
                        $this->error ="新密码不能为空";
                        return false;
                    }
                    $model = new UserModel();
                    $user = $model->withoutGlobalScope()->where('id',$id)->find();
                    if(empty($user)|| ($user && md5($data['password'].$user->salt) != $user->password)){
                        $this->error ="旧密码输入有误";
                        return false;
                    }
                    $salt =  Str::random(6);
                    $data['salt'] = $salt;
                    $data['password'] = md5($data['newpassword'].$salt);
                }else{
                    $salt =  Str::random(6);
                    $data['salt'] = $salt;
                    $data['password'] = md5($data['password'].$salt);
                }
            }else{
                unset($data['password']);
            }

            if ($this->beforeEdit($data) && static::update($this->filterData($data), [$pk => $id])) {
                $this->updateChildren($id, $data);
                $this->afterEdit($data);
                $this->commit();
                return $data;
            }
        }catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return false;
    }

    /**
     * 本地路径
     * @param $path
     * @return string
     */
    protected function getLocalPath($filePath): string
    {
        $path = str_replace(str_replace('\\', '/', root_path('public')),  '',str_replace('\\', '/',  $filePath)) ;
        return app()->request->domain() .'/'.  $path;
    }

}
