<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace controller;

use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Exception;
use think\helper\Str;

/**
 * 后台权限基础控制器
 * Class BasicAdmin
 * @package controller
 */
class BasicData extends Controller
{

    public function getPageRow(){
        $psize 	= 10;
        $r=$this->request->request('r');
        if(isset($r)){
            $psize = intval($r);
        }else{
            $r=$this->request->request('row');
            if( isset($r)){
                $psize = intval($r);
            }
        }
        return $psize;
    }

    public function getPageNum(){
        $pagenum=1;
        $page = $this->request->request('page');
        if(isset($page)){
            $pagenum = intval($page);
        }else{
            $offset =$this->request->request('offset');
            if(isset($offset)){
                $pagenum = $offset/$this->getPageRow()+1;
            }
        }

        return $pagenum;
    }

    protected  function lists ($tablename,$where=array(),$order='',$field=true){


        $result = $this->getUserDb()->query("SHOW INDEX FROM " . $tablename);
        $pk="id";
        foreach($result as $value) {
            if($value['Key_name'] == 'PRIMARY'){
                $pk=$value['Column_name'];
            }
        }

        $orderArray=[];
        if($order===null||($order==='' && !empty($pk))){
            $orderArray[]= $pk.' desc';
        }elseif($order){
            $orderArray[]=$order;
        }

        $db = $this->getUserDb()->table($tablename)->where($where)->order($orderArray);
        $page = $db->paginate($this->getPageRow(), false, ['query' => $where,'page'=>$this->getPageNum()]);

        /*
        $rows=[];
        foreach ($plist as $item){
            foreach($item as $key => $value){
                $item[$key]=htmlspecialchars_decode($value);
            }
            $rows[]=$item;
        }*/

        $list['rows']=$page->all();
        $list['total']=$page->total();
        $list['totalPage']=$page->lastPage();
        return $list;

    }



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


            $type = $this->request->request("type");
            $columns =$this->request->request("columns");
            if(empty($columns)){
                //去掉不必要的属性值，否则查询数据库会出错
                $table = $this->getTable();//获取不带前缀的表名
                $map = $this->request->except(['formid','dashboardid','isinfinite','order']);
                $map = $this->removeMap($table,$map);
                $order = $this->request->request("order");

                $list = $this->lists ($table,$map,$order);

                $link_table = $this->request->request("link_table");
                if($list['total'] >0 && !empty($link_table)){

                    $datas = $list['rows'];
                    $attr = array();
                    foreach ($datas as $item) {
                        $attr[]=$item['link_id'];
                    }
                    $link_table= $this->getTbName($link_table);
                    if($link_table){
                        $linkDatas = $this->getUserDb()->table($link_table)->where(["id"=>$attr])->select();
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
                }
                $list['status']='success';
                echo json_encode($list);
            }else{
                $columns = json_decode(htmlspecialchars_decode($columns),true);
                $count_json = count($columns);
                $tables = array();
                $dashboardid = $this->request->request("dashboardid");
                for ($i = 0; $i < $count_json; $i++){
                    $column = $columns[$i];
                    $dashboardid = $column['dashboard_id'];
                    $columnList = null;
                    if(!empty($tables[$column['form_id']])){
                        $columnList =$tables[$column['form_id']];
                        $columnList[] = $column;
                        $tables[$column['form_id']] = $columnList;
                    }else{
                        $columnList = array();
                        $columnList[] = $column;
                        $tables[$column['form_id']] = $columnList;
                    }
                }

                $sql = "select ";
                $selectTables = array();
                $wheres = array();

                foreach ($tables as $formid => $columns) {
                    $map=[];
                    $map["name"]=$dashboardid."_".$formid;
                    $tableName = $this->getTbName($map["name"]);
                    if(!$tableName){
                        $message=[
                            'status'       => "error",
                            'message'       => "获取数据失败",
                            'total'        =>0,
                            'rows'         =>[]
                        ];
                        echo json_encode($message);
                        return;
                    }

                    $tableAlias["name"]=$tableName;
                    $alias = "t".$formid;
                    $tableAlias["alias"]=$alias;
                    $selectTables[] = $tableAlias;

                    $idExist=false;//判断ID值是否存在，如果存在则不加ID值
                    $fields = array();
                    foreach ($columns as $column) {//遍历配置字段拼接SQL
                        $field = $column['field'];
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
                foreach ($selectTables as $i=>$alias) {
                    $sql=$sql." ".$alias["name"]." as ".$alias["alias"].", ";
                }
                $sql=substr($sql,0,strripos($sql,","));

                $links = $this->request->request("links");
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

                $psize 	= 10;
                $r=$this->request->request('r');
                if(isset($r)){
                    $psize = intval($r);
                }else{
                    $r=$this->request->request('row');
                    if( isset($r)){
                        $psize = intval($r);
                    }
                }


                $offset = intval($this->request->request('offset'));

                $countSql = " select count(1) total from ($sql) t";

                $totalResult = \think\Db::query($countSql);

                $sql.=" limit $offset , $psize ";
                $resultSet = \think\Db::query($sql);
                $results = array();
                foreach ($resultSet as $key => $result) {
                    $results[] = $result;
                }
                $total = $totalResult[0]['total'];
                echo json_encode([
                    'status'       => "success",
                    'message'       => "获取数据成功",
                    'total'        => $total,
                    'rows'         =>$results
                ]);
                return;
            }
        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'获取数据失败'.$e];
            echo json_encode($info);
        }
    }



    public function save()
    {
        try {
            $table = $this->getTable();
            if(!$table){
                echo json_encode(['status'=>'error', 'message'=>'表不存在']);
                return;
            }
            $map = $this->request->except(['table','page','row','limit']);
            $map = $this->removeMap($table,$map);

            if(empty($map["id"])){
                $map["id"]=create_guid();
                $user_id = session('uid');
                if(!empty($user_id)){
                    $map["user_id"] =$user_id;
                }
                $map["mpid"] = session('mpid');
                $map["create_time"] =  date("Y-m-d H:i:s", time());
                $map["update_time"] =  date("Y-m-d H:i:s", time());
                $map["status"] = "1";
                try{
                    $this->getUserDb()->table($table)->insert($map);
                    $info = ['id'=>$map["id"],'status'=>'success', 'message'=>'保存数据成功'];
                    echo json_encode($info);
                } catch (Exception $e){
                    $info = ['status'=>'error', 'message'=>'保存数据失败'];
                    echo json_encode($info);
                }
            }else{
                $map["update_time"] = time();
                try{
                    $this->getUserDb()->table($table)->where('id',$map["id"])->update($map);
                    $info = ['status'=>'success', 'message'=>'更新数据成功'];
                    echo json_encode($info);
                } catch (Exception $e){
                    $info = ['status'=>'error', 'message'=>'更新数据失败'];
                    echo json_encode($info);
                }
            }
        } catch (Exception $e) {
            $info = ['status'=>'error', 'message'=>'更新数据失败'];
            echo json_encode($info);
            return;
        }
    }

    /**
     * 删除表格非自己的属性
     * @param $table
     * @param $map
     */
    public function removeMap($tableFullName,$map)
    {
        //获取表所有字段
        $columns = $this->getUserDb()->query('SHOW FULL COLUMNS FROM ' . $tableFullName);
        $fields = array();
        foreach ($columns as $key => $value) {
            $fields[] = $value["Field"];
        }
        //去掉不必要的属性值，否则查询数据库会出错
        foreach ($map as $key => $value) {
            if(strpos($key,'zdcs')!==false){
                $field = substr($key,4,strrpos($key,'_')-4);
                $condition = substr($key,strrpos($key,'_')+1);
                if(!empty($value)){
                    if($condition=='eq'){
                        $map[$field]=$value;
                    }if($condition=='like'){
                        $map[$field]=array('like',$value);
                    }
                }
            }
            if (!in_array($key, $fields)) {
                unset($map[$key]);
            }
        }
        return $map;
    }


    public function getTbName($table_name,$flag=true){
        $table_name = $flag?getTableName($table_name):$table_name;
        $sql = "SHOW TABLES LIKE '$table_name'";
        $res = count($this->getUserDb()->query($sql));
        if($res){
            return $table_name;
        }else{
            return null;
        }
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTable(){
        //实际环境中，大家要处理好这个表名的问题，要不然所有的数据都会被盗哟。本环境只是提供一个测试环境，所以简化了写法
        //大家可以做个表名影射，限制表的访问规则，哪些表不能访问。
        $formid = $this->request->request("formid");
        $dbid = $this->request->request("dbid");
        if(!empty($dbid)){
            $db = $this->getUserDb();
            $table_name=$formid;
            $sql = "SHOW TABLES LIKE '$table_name'";
            $res = count($db->query($sql));
            if($res){
                return $table_name;
            }else{
                return null;
            }
        }
        $dashboardid = $this->request->request("dashboardid");
        if(empty($dashboardid)){
            $dashboardid = $this->request->request("dashboardId");
        }

        if(empty($formid)){
            $formid = $this->request->request("formId");
        }

        $result = $this->getTbName($formid);
        if($result){
            return $result;
        }

        $result = $this->getTbName($dashboardid."_".$formid);

        if($result){
            return $result;
        }
        $tableName = $this->request->request("tableName");
        $result = $this->getTbName($dashboardid."_".$tableName);
        if($result){
            return $result;
        }
  
        return null;

    }

    public function remove()
    {
        try {
            $table = $this->getTable();//获取不带前缀的表名
            $ids    =   input('values/a');
            $ids    =   $this->request->request('values/a');
            $field    =   $this->request->request('field');
            if(Db::table($this->getTable())->find(substr($field,0,strpos($field,"_")),$ids)->delete()){
                $info = ['status'=>'success', 'message'=>'删除数据成功'];
                echo json_encode($info);
            }else{
                $info = ['status'=>'error', 'message'=>'删除数据失败'];
                echo json_encode($info);
            }

        } catch (\Exception $e) {
            $info = ['status'=>'error', 'message'=>'删除数据失败'];
            echo json_encode($info);
        }
    }

    protected function getUserDb($dbid=""){
        if(empty($dbid)){
            $dbid = $this->request->request("dbid");
        }
        if(!empty($dbid)){
            $db = Db::name("db")->where('id',$dbid)->find();
            $db['password']=aesDecrypt($db['password']);
            return Db::connect($db);
        }else{
            return db();
        }
    }
}
