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


    /**
     * 获取表名
     * @return string
     */
    public function getTable(){
        //实际环境中，大家要处理好这个表名的问题，要不然所有的数据都会被盗哟。本环境只是提供一个测试环境，所以简化了写法
        //大家可以做个表名影射，限制表的访问规则，哪些表不能访问，大家在实际环境可别这样用哟。
        $dashboardid = input("dashboardid", '');
        $formid = input("formid", '');
        $map["dashboardid"] = $dashboardid;
        $map["formid"] = $formid;
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

            $map = request()->except(['table','page','row','limit','order','formid','dashboardid','isinfinite']);
            $order = input("order", '');
            $formid = input("formid", '');
            $list = $this->lists ($this->getTable(),$map,$order);
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
                $list['data']=$gdata;
            }
            $list['code']=0;
            $list['formid']=$formid;

            return json_encode($list);
        } catch (\Exception $e) {
            $info = ['code'=>-1, 'message'=>'获取数据失败'.$e.message];
            return json_encode($info);
        }
    }


    /**
     * 获取单条详情
     * @return string
     */
    public function detail()
    {
        $id = input("id", 0);
        $info = \think\Db::name($this->getTable())->field(true)->find($id);
        if($info==null){
            $info = ['code'=>-1, 'message'=>'获取数据失败'];
        }else{
            $info['code']=0;
        }
        return json_encode($info);
    }

    /**
     * 新增数据
     * @return string
     */
    public function add(){
        try {
            $table = input("table", '');
            $map = request()->except(['table','page','row','limit']);
            $id = db($this->getTable())->insertGetId($map);
            $info = ['id'=>$id,'code'=>0, 'message'=>'保存数据成功'];
            return json_encode($info);
        } catch (\Exception $e) {
            $info = ['code'=>-1, 'message'=>'保存数据失败'];
            return json_encode($info);
        }
    }

    /**
     * 删除数据
     * @return string
     */
    public function del(){
        try {
            $table = input("table", '');
            $map = request()->except(['table','page','row','limit']);
            db($this->getTable())->where($map)->delete();
            $info = ['code'=>0, 'message'=>'删除数据成功'];
            return json_encode($info);
        } catch (\Exception $e) {
            $info = ['code'=>-1, 'message'=>'删除数据失败'];
            return json_encode($info);
        }
    }

    /**
     * 删除数据
     * @return string
     */
    public function update(){
        try {
            $table = input("table", '');
            $map = request()->except(['table','page','row','limit']);
            db($this->getTable())->update($map);
            $info = ['code'=>0, 'message'=>'更新数据成功'];
            return json_encode($info);
        } catch (\Exception $e) {
            $info = ['code'=>-1, 'message'=>'更新数据失败'];
            return json_encode($info);
        }
    }


}
