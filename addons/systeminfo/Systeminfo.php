<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@foxmail.com>
// +----------------------------------------------------------------------
 
namespace addons\systeminfo;

/**
 * 系统环境信息插件
 */

    class Systeminfo extends \think\Addons {

        public $info = array(
            'name'=>'systeminfo',
            'title'=>'系统环境信息',
            'description'=>'用于显示一些服务器的信息',
            'status'=>1,
            'author'=>'diygw',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的AdminIndex钩子方法
        public function AdminIndex($param){
            $config = $this->getConfig();
            
//            if(extension_loaded('curl')){
//                $url = 'http://www.diygw.com/index.php/index/index/check_version';
//                $params = array(
//                    'version' => diygw_VERSION,
//                    'domain'  => $_SERVER['HTTP_HOST'],
//                    'auth'    => sha1(config('data_auth_key')),
//                );
//
//                $vars = http_build_query($params);
//                $opts = array(
//                    CURLOPT_TIMEOUT        => 5,
//                    CURLOPT_RETURNTRANSFER => 1,
//                    CURLOPT_URL            => $url,
//                    CURLOPT_POST           => 1,
//                    CURLOPT_POSTFIELDS     => $vars,
//                    CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
//                );
//
//                /* 初始化并执行curl请求 */
//                $ch = curl_init();
//                curl_setopt_array($ch, $opts);
//                $data  = curl_exec($ch);
//                $error = curl_error($ch);
//                curl_close($ch);
//            }

            if(!empty($data) && strlen($data)<400 && diygw_VERSION != $data){
                $config['new_version'] = $data;
            }
            $this->assign('addons_config', $config);
            if($config['display']){
                return $this->fetch('widget');
            }
        }
    }