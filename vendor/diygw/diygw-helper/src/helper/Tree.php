<?php
// +----------------------------------------------------------------------
// | diygw [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw  diygwcom@foxmail.com <www.diygw.com> 
// +----------------------------------------------------------------------
namespace diygw\helper;

/*
 * Tree 构建tree状数据
 *
 * @Author: diygw  <diygwcom@foxmail.com>
 */
class Tree
{
    //主键名称
    public $pk = 'id';
    //父键名称
    private $pid = 'pid';
    //子节点名称
    private $child    = 'child';

    /**
     * 生成Tree
     * @param array $data
     * @param number $index
     * @return array
     */
    public  function  getTree($data, $index = 0)
    {
        $childs = $this->getFindChild($data, $index);
        if(empty($childs))
        {
            return $childs;
        }
        foreach($childs as $k => &$v)
        {
            if(empty($data)) break;
            $child = $this->getTree($data, $v[$this->pk]);
            if(!empty($child))
            {
                $v[$this->child] = $child;
            }
        }
//        unset($v);
        return $childs;
    }
    /**
     * 查找子类
     * @param array $data
     * @param number $index
     * @return array
     */
    public function getFindChild(&$data, $index)
    {
        $childs = [];
        foreach ($data as $k => $v){
            if($v[$this->pid] == $index){
                $childs[]  = $v;
                unset($v);
            }
        }
        return $childs;
    }
    /*
     * Tree 树还原成列表
     *
     * @param  array $tree  原来的树
     * @param  string $order 排序显示的键，一般是主键 升序排列
     * @param  array  $list  过渡用的中间数组，
     * @return array
     */
    public function getTreeToList($tree,$order='id', &$list = []){
        if(is_array($tree)) {
            foreach ($tree as $key => $value) {
                $reffer = $value;
                if(isset($reffer[$this->child])){
                    unset($reffer[$this->child]);
                    $this->getTreeToList($value[$this->child], $order, $list);
                }
                $list[] = $reffer;

            }
            $list = self::getListSort($list, $order, $sortby='asc');
        }
        return $list;
    }
    /**
     * 对查询结果集进行排序
     * @access public
     * @param array $list 查询结果
     * @param string $field 排序的字段名
     * @param array $sortby 排序类型
     * asc正向排序 desc逆向排序 nat自然排序
     * @return array
     */
    public function getListSort($list,$field, $sortby='asc') {
        if(is_array($list)){
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
                $refer[$i] = &$data[$field];
            switch ($sortby) {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc':// 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ( $refer as $key=> $val)
                $resultSet[] = &$list[$key];
            return $resultSet;
        }
        return false;
    }

    /**
     * 将格式数组转换为树
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree; //用于树型数组完成递归格式的全局变量
    private function _toFormatTree($list,$level=0,$title = 'title') {
        foreach($list as $key=>$val){
            $tmp_str=str_repeat("&nbsp;",$level*2);
            $tmp_str.="└";

            $val['level'] = $level;
            $val['title_show'] =$level==0?$val[$title]."&nbsp;":$tmp_str.$val[$title]."&nbsp;";
//             $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if(!array_key_exists($this->child,$val)){
                array_push($this->formatTree,$val);
            }else{
                $tmp_ary = $val[$this->child];
                unset($val[$this->child]);
                array_push($this->formatTree,$val);
                $this->_toFormatTree($tmp_ary,$level+1,$title); //进行下一层递归
            }
        }
        return;
    }

    public function toFormatTree($list,$title = 'title',$pk='id',$pid = 'pid',$index = 0){
        $list = $this->getTree($list,$index);
        $this->formatTree = [];
        $this->_toFormatTree($list,0,$title);
        return $this->formatTree;
    }

    /**
     * 修改器 设置数据对象值
     * @access public
     * @param string(array) $name  属性名
     * @param mixed  $value 属性值
     * @return $this
     */
    public function setAttr($name,$value=''){
        if(is_array($name)){
            foreach ($name as $key=>$value){
                $this->$key = $value;
            }
        }else{
            $this->$name = $value;
        }
        return $this;
    }
}
