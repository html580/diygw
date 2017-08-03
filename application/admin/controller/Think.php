<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 模型数据管理控制器
 * @author 艺品网络  <twothink.cn>
 */
class Think  extends Admin{

    /**
     * 显示指定模型列表数据
     * @param  String $model 模型标识
     * @author 艺品网络  <twothink.cn>
     */
    public function lists($model = null, $p = 0,$where=array(), $order = '', $field = true){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //获取模型信息
        $model = \think\Db::name('Model')->getByName($model);
        $model || $this->error('模型不存在！');
        if(empty($model['list_grid']))
        	$this->error('未定义:列表定义');
        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        foreach ($grids as &$value) {
        	if(trim($value) === ''){
        		continue;
        	}
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']	=	$val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array	=	explode('|',$val);
                $fields[] = $array[0];
            }
        }
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 关键字搜索
        $map	=	array();
        $key	=	$model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]	=	array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }

        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name]	=	$val;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        if($model['extend']){
            $name   = get_table_name($model['id']);
            $parent = get_table_name($model['extend']);
            $fix    = config("database.prefix");

            $key = array_search('id', $fields);
            if(false === $key){
                array_push($fields, "b.id as id");
            } else {
                $fields[$key] = "b.id as id";
            }
            /* 查询记录数 */
           $count = \think\Db::name($parent)->alias('a')
                 ->join("{$fix}{$name} b",'a.id = b.id')
                 ->where($map)->count();
            // 查询数据
            $data   =\think\Db::name($parent)->alias('a')
            ->join("{$fix}{$name} b",'a.id = b.id')
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order("b.id DESC")
            ->paginate($row);
            /* 数据分页 */
            $page = $data->render();
            $data = $data->toArray();
        } else {
            if($model['need_pk']){
                in_array('id', $fields) || array_push($fields, 'id');
            }
            $name = parse_name(get_table_name($model['id']), true);
            $data = \think\Db::name($name)
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order($model['need_pk']?'id DESC':'')
                /* 执行查询 */
                ->paginate($row);
               /* 数据分页 */
               $page = $data->render();
               $data = $data->toArray();
            /* 查询记录总数 */
            $count = \think\Db::name($name)->where($map)->count();
        }
        //分页
        if($count > $row){
            $this->assign('_page', $page);
        }

        $data = $data['data'];
        $data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->assign('meta_title' , $model['title'].'列表');
        return $this->fetch($model['template_list']);
    }

    public function del($model = null, $ids=null){
        $model = \think\Db::name('Model')->find($model);
        $model || $this->error('模型不存在！');

        $ids = input('ids/a');

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $ids) );
        if(!\think\Db::name(get_table_name($model['id']))->where($map)->delete())
            $this->error('删除失败！');
        if($model['extend']){
            //删除基础模型
            if(!\think\Db::name(get_table_name($model['extend']))->where($map)->delete())
                $this->error('删除失败！');
        }
        $this->success('删除成功');
    }

    /**
     * 设置一条或者多条数据的状态
     * @author 艺品网络  <twothink.cn>
     */
    public function setStatus($model='Document'){
        return parent::setStatus($model);
    }

    public function edit($model = null, $id = 0){
        //获取模型信息
        $model = \think\Db::name('Model')->find($model);
        $model || $this->error('模型不存在！');

        if($this->request->isPost()){
            if($model['extend']){
                //更新基础模型
                $logic = logic($model['extend']);
                $res = $logic->updates();
                $res || $this->error($logic->getError());
            }
            //更新当前模型
            $logic = logic($model['id']);
            $res = $logic->updates();
            $res || $this->error($logic->getError());
            $this->success('保存'.$model['title'].'成功！', url('lists?model='.$model['name']));
        } else {
            $fields     = get_model_attribute($model['id']);
            //读取模型数据列表
            if($model['extend']){
                $name   = get_table_name($model['id']);
                $parent = get_table_name($model['extend']);
                $fix    = config("database.prefix");
                //获取数据
                $data   =\think\Db::name($parent)->alias('a')
                    ->join("{$fix}{$name} b",'a.id = b.id')
                    ->where(['b.id'=>$id])
                    ->find();
            } else {
                $data       = \think\Db::name(get_table_name($model['id']))->where(['id'=>$id])->find();
            }
            $data || $this->error('数据不存在！');

            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->assign('meta_title','编辑'.$model['title']);
            return $this->fetch($model['template_edit']?$model['template_edit']:'');
        }
    }

    public function add($model = null){
        //获取模型信息
        $model = \think\Db::name('Model')->where(array('status' => 1))->find($model);
        $model || $this->error('模型不存在！');
        if($this->request->isPost()){
            if($model['extend']){
                //新增基础模型
                $logic = logic($model['extend']);
                $res = $logic->updates();
                $res || $this->error($logic->getError());
            }
            //新增当前模型
            $logic = logic($model['id']);
            $res = $logic->updates();
            $res || $this->error($logic->getError());
            $this->success('添加'.$model['title'].'成功！', url('lists?model='.$model['name']));

        } else {
            $fields = get_model_attribute($model['id']);
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->meta_title = '新增'.$model['title'];
            return $this->fetch($model['template_add']?$model['template_add']:'');
        }
    }
}
