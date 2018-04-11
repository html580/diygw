<?php
namespace app\data\controller;
use think\Controller;
use wlt\wxmini\WXLoginHelper;
/**
 * 加载数据方法
 * User: 邓志锋 <280160522@qq.com> <http://www.diygw.com>
 * Date: 2017-05-27
 * Time: 下午 4:20
 */
class Data extends Controller
{

    public function __construct(){
        $config = api('Config/lists');
        config($config); //添加配置
        parent::__construct();
    }
    public function _initialize()
    {
        // SESSION_ID设置的提交变量,解决flash上传跨域
        $session_id=input(config('session.var_session_id'));
        if($session_id){
            session_id($session_id);
        }
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
    }

    protected  function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =  (array)input('request.');
        if(is_string($model)){
            $model  =   \think\Db::name($model);
        }
        $pk         =   $model->getPk();

        if($order===null){
            $options['order'] = $pk.' desc';
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
            $order = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $total        =   $model->where($options['where'])->count();
        if( isset($REQUEST['r'])){
            $listRows = (int)$REQUEST['r'];
        }else if( isset($REQUEST['row'])){
            $listRows = (int)$REQUEST['row'];
        }else{
            $listRows = config('list_rows') > 0 ? config('list_rows') : 10;
        }
        // 分页查询
        $list = $model->where($options['where'])->order($order)->field($field)->paginate($listRows);
        if($list && !is_array($list)){
            $list=$list->toRowArray();
        }
        return $list;//TODO 可以返回带分页的$list
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTable(){
        //实际环境中，大家要处理好这个表名的问题，要不然所有的数据都会被盗哟。本环境只是提供一个测试环境，所以简化了写法
        //大家可以做个表名影射，限制表的访问规则，哪些表不能访问，大家在实际环境可别这样用哟。
        $dashboardid = input("dashboardid", '');
        if(empty($dashboardid)){
            $dashboardid = input("dashboardId", '');
        }
        $formid = input("formid", '');
        if(empty($formid)){
            $formid = input("formId", '');
        }

        $map["name"] = $dashboardid."_".$formid;
        $result = db("model")->where($map)->find();
        if(!empty($result)){
            return $result["name"];
        }
        $map["dashboard_id"] = $dashboardid;
        $map["form_id"] = $formid;
        $result = db("model")->where($map)->find();
        //$table = input("table", '');
        return $result["name"];
    }
    /**
     * 获取分页数据
     * @return string
     */
    public function data()
    {
        try {
            /*$Authorization = $_SERVER['HTTP_AUTHORIZATION'];

            if(cache($Authorization)==null){
                $list['Authorization']=$Authorization;
                $list['code']='-1';
                $list['message']='请求鉴权 API 失败，网络异常或鉴权服务器错误';
                return json_encode($list);
            }*/
            $type = input("type", '');
			$columns = input("columns", '');
            if(empty($columns)){
                //去掉不必要的属性值，否则查询数据库会出错
                $map = request()->except(['formid','dashboardid','isinfinite']);
                $table = $this->getTable();//获取不带前缀的表名
                $map = $this->removeMap($table, $map);
                $order = input("order", '');
                $list = $this->lists ($table,$map,$order);
                $link_table = input("link_table", null);
                if($list['total'] >0 && !is_null($link_table)){
                    $datas = $list['data'];
                    $attr = array();
                    foreach ($datas as $item) {
                        $attr[]=$item['link_id'];
                    }
                    $id_list = implode(',',$attr);
                    $linkDatas = \think\Db::name($link_table)->where ( "id in({$id_list}) " )->select();
                    $gdata = array();
                    foreach ($linkDatas as $ritem) {//遍历实际表数据
                        foreach ($datas as $item) {
                            if($ritem['id']==$item['link_id']){//遍历主表数据
                                $tmp=array_merge($ritem,$item);
                                $gdata[]=$tmp;
                            }
                        }
                    }
                    $list['rows']=$gdata;
                }
                $list['status']='success';
                return $list;
            }else{
                $columns = json_decode($columns);
                $count_json = count($columns);
                $tables = array();
                $dashboardid = input("dashboardid", '');
                for ($i = 0; $i < $count_json; $i++){
                    $column = $columns[$i];
                    $dashboardid = $column->dashboard_id;
                    $columnList = null;
                    if(!empty($tables[$column->form_id])){
                        $columnList =$tables[$column->form_id];
                        $columnList[] = $column;
                        $tables[$column->form_id] = $columnList;
                    }else{
                        $columnList = array();
                        $columnList[] = $column;
                        $tables[$column->form_id] = $columnList;
                    }

                }

                $sql = "select ";
                $selectTables = array();
                $wheres = array();
                foreach ($tables as $formid => $columns) {

                    $map=[];
                    $map["name"]=$dashboardid."_".$formid;
                    $result = db("model")->where($map)->find();
                    if(empty($result)){
                        $map["dashboard_id"] = $dashboardid;
                        $map["form_id"] = $formid;
                        $result = db("model")->where($map)->find();
                    }
                    $tableName =  $result["name"];
                    $tableAlias["name"]=$tableName;
                    $alias = "t".$formid;
                    $tableAlias["alias"]=$alias;
                    $selectTables[] = $tableAlias;

                    $idExist=false;//判断ID值是否存在，如果存在则不加ID值
                    $fields = array();
                    foreach ($columns as $column) {//遍历配置字段拼接SQL
                        $field = $column->field;
                        if(in_array($field, $fields)){//判断查询字段是否已经存在，如果存在，跳过
                            continue;
                        }
                        $fields[]=$field;
                        if($field=="id"){
                            $sql=$sql." ".$alias.".id"." id_".$formid."_1 , ";
                            $idExist= true;
                        }else if($field=="user_id"){
                            $sql=$sql." ".$alias.".create_time"." id_".$formid."_2 , ";
                            $idExist= true;
                        }else if($field=="create_time"){
                            $sql=$sql." ".$alias.".create_time"." id_".$formid."_3 , ";
                            $idExist= true;
                        }else{
                            $sql=$sql." ".$alias.".".$field.", ";
                        }
                    }
                    if(!$idExist){
                        $sql=$sql." ".$alias.".id"." id_".$formid."_1 , ";
                    }
                }
                $sql=substr($sql,0,strripos($sql,","));
                $sql.=" from ";
                $prefix=config('database.prefix');
                foreach ($selectTables as $i=>$alias) {
                    $sql=$sql." ".$prefix.$alias["name"]." as ".$alias["alias"].", ";
                }
                $sql=substr($sql,0,strripos($sql,","));

                $links = input("links", '');
                $links = json_decode($links);

                foreach ($links as $i=>$link) {
                    $fromOperator = $link->fromOperator;
                    $fromOperator =substr($fromOperator,4);
                    $fromConnector = $link->fromConnector;
                    $fromConnector =substr($fromConnector,7);
                    $toOperator = $link->toOperator;
                    $toOperator =substr($toOperator,4);
                    $toConnector = $link->toConnector;
                    $toConnector =substr($toConnector,6);
                    $sql.=" where t$fromOperator.$fromConnector=t$toOperator.$toConnector";
                }

                $sql .=" order by ";
                foreach ($selectTables as $i=>$alias) {
                    $sql=$sql." ".$alias["alias"].".create_time, ";
                }
                $sql=substr($sql,0,strripos($sql,","));
                $sql .=" desc ";

                if( isset($REQUEST['limit'])){
                    $listRows = (int)$REQUEST['limit'];
                }else if( isset($REQUEST['row'])){
                    $listRows = (int)$REQUEST['row'];
                }else{
                    $listRows = config('list_rows') > 0 ? config('list_rows') : 10;
                }
                $offset = (int)$REQUEST['offset'];
                $countSql = " select count(*) total from ($sql) t";
                $totalResult = \think\Db::query($countSql);

                $sql.=" limit $offset , $listRows ";
                $resultSet = \think\Db::query($sql);
                $results = array();
                foreach ($resultSet as $key => $result) {
                    $results[] = $result;
                }
                $total = $totalResult[0]['total'];

                return [
                    'status'       => "success",
                    'message'       => "获取数据成功",
                    'total'        => $total,
                    'rows'         =>$results
                ];
            }

        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'获取数据失败'.$e];
            return $info;
        }
    }



    /**
     * 删除数据
     * @return string
     */
    public function remove(){
        try {
            $table = $this->getTable();//获取不带前缀的表名
            $ids    =   input('values/a');
            $map['id'] = array('in',$ids);
            db($this->getTable())->where($map)->delete();
            $info = ['status'=>'success', 'message'=>'删除数据成功'];
            return $info;
        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'删除数据失败'];
            return $info;
        }
    }


    /**
     * 创建UUID
     * @return string
     */
    function create_guid(){
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = substr($charid, 0, 8).substr($charid, 8, 4).substr($charid,12, 4).substr($charid,16, 4).substr($charid,20,12);
        return $uuid;
    }

    /**
     * 保存数据
     * @return string
     */
    public function save(){
        try {
            $table = $this->getTable();
            $map = request()->except(['table','page','row','limit']);
            $map = $this->removeMap($table, $map);
            if(empty($map["id"])){
                $map["id"]=$this->create_guid();
                $session_id=is_login();
                if($session_id){
                    session_id($session_id);
                }
                $user_id = is_login();;
                if(!empty($user_id)){
                    $map["user_id"] =$user_id;
                }
                $map["create_time"] =  date("Y-m-d H:i:s", time());
                $map["update_time"] =  date("Y-m-d H:i:s", time());
                $map["status"] = "1";
                $id = db($table)->insert($map);
                $info = ['id'=>$map["id"],'status'=>'success', 'message'=>'保存数据成功'];
            }else{
                $map["update_time"] = time();
                db($table)->update($map);
                $info = ['status'=>'success', 'message'=>'更新数据成功'];
            }
            return $info;
        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'更新数据失败'];
            return $info;
        }
    }

    /**
     * 删除表格非自己的属性
     * @param $table
     * @param $map
     */
    public function removeMap($table, $map)
    {
        $tableFullName = config('database.prefix') . $table;//获得带前缀的表名
        //获取表所有字段
        $columns = db()->query('SHOW FULL COLUMNS FROM ' . $tableFullName);
        $fields = array();
        foreach ($columns as $key => $value) {
            $fields[] = $value["Field"];
        }
        //去掉不必要的属性值，否则查询数据库会出错
        foreach ($map as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($map[$key]);
            }
        }
        return $map;
    }

}
