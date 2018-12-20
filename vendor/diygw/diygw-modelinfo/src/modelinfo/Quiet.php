<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: DIY官网  diygwcom@foxmail.com <www.diygw.com>
// +----------------------------------------------------------------------

namespace think\modelinfo;

/*
 * @title静态模型定义处理类
 * @Author: DIY官网  diygwcom@foxmail.com <www.diygw.com>
 */
class Quiet extends Base
{
    public $replace_string = [['[DELETE]', '[EDIT]'], ['delete?ids=[id]', 'edit?id=[id]']]; //特殊字符串替换用于列表定义解析
    protected $Queryobj;//实列化查询对象

    /*
     * @title 模型规则解析
     * @$info 模型定义
     * @param $returnmodel   true 是否返回当前模型信息
     * @return obj
     * @author DIY官网 diygwcom@foxmail.com
     */
    public function info($info = false, $returnmodel = true)
    {
        if (!$info && !isset($this->info)) {
            $this->error = '模型配置信息不存在';
            return false;
        }
        $scene = $this->scene = $this->scene ?: request()->action();
        //当前操作模型信息
        if (isset($info[$scene]) && isset($info['default'])) {
            $info = array_merge($info['default'], $info[$scene]);
        } elseif (isset($info['default'])) {
            $info = $info['default'];
        }
        //$pk
        if ($info['pk']) {
            $this->pk = $info['pk'];
        }

        $this->model[] = $info;
        if ($returnmodel)
            $this->info = $info;
        //replace_string
        if (empty($info['replace_string'])) {
            $this->info['replace_string'] = $this->replace_string;
        }
        //Button
        if (!empty($info['button'])) {
            $this->getButton($info['button']);
        }
        $this->info['name'] = !empty($info['name']) ? $info['name'] : request()->controller();
        if (isset($info['url']) && $info['url'] !== false) {
            $this->info['url'] = $info['url'] !== true ? url($info['url']) : request()->url();
        }
        return $this;
    }

    /*
     * @title 获取button组
     * @param $button 按钮规则
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getButton($button = false)
    {
        if (!$button) {
            $button = isset($this->model[0]['button']) ? $this->model[0]['button'] : '';
        }

        if ($button) {
            $param = request()->param();
            foreach ($button as $key => &$value) {
                // 替换数据变量
                $url = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($param) {
                    return isset($param[$match[1]]) ? $param[$match[1]] : '';
                }, $value['url']);
                $value['url'] = $url;//url($url);
            }
            $this->info['button'] = $button;
        }
        return $this;
    }

    /*
     * @title 列表定义解析
     * @param $list_grid 列表定义规则
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getListField($list_grid = false)
    {
        if (!$list_grid) {
            $list_grid = $this->model[0]['list_grid'];
        }
        return parent::getListField($list_grid);
    }

    /*
     * @title 获取高级搜索配置
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getSearchList()
    {
        $search_arr = isset($this->model[0]['search_list']) ? $this->model[0]['search_list'] : [];
        //value extra规则解析
        foreach ($search_arr as $key => &$value) {
            if (0 === strpos($value['value'], ':') || 0 === strpos($value['value'], '[')) {
                $value['value'] = parse_field_attr($value['value']);
            }
            if (!empty($value['extra'])) {
                $value['extra'] = parse_field_attr($value['extra']);
            }
        }
        $this->info['search_list'] = $search_arr;
        $this->getSearchFixed();//调用固定搜索
        return $this;
    }

    /*
     * @title 获取固定搜索配置
     * @param $search_fixed 固定搜索配置
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getSearchFixed($search_fixed = false)
    {
        if (!$search_fixed) {
            $search_fixed = isset($this->model[0]['search_fixed']) ? $this->model[0]['search_fixed'] : [];
        }
        $param = request()->param();
        //value规则解析
        foreach ($search_fixed as $key => &$value) {
            if (0 === strpos($value['value'], ':') || 0 === strpos($value['value'], '[')) {
                $string = $value['value'];
                $str = substr($string, 1);
                if (0 === strpos($str, '[')) {
                    if (preg_match('/\[([a-z_]+)\]/', $str, $matches)) {
                        if (!isset($param[$matches['1']])) {
                            unset($search_fixed[$key]);
                            continue;
                        }
                    }
                }
                $value['value'] = parse_field_attr($string);
            }
        }

        $this->info['search_fixed'] = $search_fixed;
        return $this;
    }

    /*
     * 获取模型字段排序列表
     * @return $this
     * @Author: diygw  diygwcom@foxmail.com <www.diygw.com>
     */
    public function getFields($fields = false)
    {
        if(!$fields)
            $fields = isset($this->info['fields']) ? $this->info['fields'] : [];
        $new_arr = [];
        foreach ($fields as $key => $value) {
            $data_name = array_column($value,'name');
            if(count($data_name) == count(array_filter($data_name)))
                $new_arr[$key] = Array_mapping($fields[$key],'name');
            else
                $new_arr[$key] = $value;
        }
        $this->info['fields'] = $new_arr;
        return $this;
    }

}
?>