<?php
namespace app\xcx\controller;
use think\Controller;
use wlt\wxmini\WXLoginHelper;
/**
 * User: 邓志锋 <280160522@qq.com> <http://www.diygw.com>
 * Date: 2017-05-27
 * Time: 下午 4:20
 */
class Index extends Controller
{
    public function index()
    {
        $code = input("code", '', 'htmlspecialchars_decode');
        $rawData = input("rawData", '', 'htmlspecialchars_decode');
        $signature = input("signature", '', 'htmlspecialchars_decode');
        $encryptedData = input("encryptedData", '', 'htmlspecialchars_decode');
        $iv = input("iv", '', 'htmlspecialchars_decode');

        $wxHelper = new WXLoginHelper();
        $data = $wxHelper->checkLogin($code, $rawData, $signature, $encryptedData, $iv);
        return $this->fetch();
    }

    public function wechatSignIn()
    {

    }

    public function wechatSignUp()
    {

    }

    public function signOut()
    {

    }

    public function decryptData()
    {
        $debug = input("debug", '', 'htmlspecialchars_decode');
       if($debug=="1"){
            $data['code']=0;
            $data['openId']='diygw_com';
            $data['message']='登录成功';
            return json_encode($data);
        }else{
            $code = input("code", '', 'htmlspecialchars_decode');
            $rawData = input("rawData", '', 'htmlspecialchars_decode');
            $signature = input("signature", '', 'htmlspecialchars_decode');
            $encryptedData = input("encryptedData", '', 'htmlspecialchars_decode');
            $iv = input("iv", '', 'htmlspecialchars_decode');

            $wxHelper = new WXLoginHelper();
            $data = $wxHelper->checkLogin($code, $rawData, $signature, $encryptedData, $iv);
            return json_encode($data);
        }
    }


    protected  function lists ($model,$where=array(),$order='',$field=true){

        $$_SERVER[''];
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
            $list=$list->toArray();
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
        $table = input("table", '');
        return $table;
    }
    /**
     * 获取分页数据
     * @return string
     */
    public function data()
    {
        try {

            $map = request()->except(['table','page','row','limit','order','formid','dashboardid']);
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
