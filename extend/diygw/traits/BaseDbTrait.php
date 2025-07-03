<?php
declare(strict_types=1);

namespace diygw\traits;

use diygw\Utils;
use think\facade\Db;
use think\helper\Str;

trait BaseDbTrait
{
    //查询相似字段时，要同时查询多个字段['title|remark']表示查询title或者remark相等的字段
    protected $likeField=[];
    //数据查询字段 用JSON查询
    protected $arrayField=[];

    protected $showField = "*";

    protected $paginate = true;

    //是否数据权限查询
    protected $isdataright = false;

    protected $isdatadeptright = false;

    public function afterList(&$list){
        return true;
    }

    /**
     * 查询列表
     *
     * @time 2022年03月28日
     * @return mixed
     */
    public function getList()
    {
        $field= $this->showField;
        // 不分页
        if (property_exists($this, 'paginate') && $this->paginate === false) {
            $data =   $this->quickSearch($this->likeField,$this->arrayField)
                ->field($field)
                ->diygwOrder()
                ->select()->toArray();
            $list['data']= $data;
            $list['total']= count($data);
        }else{
            $pageSize = 10;
            $requestParams = \request()->param();
            if(isset($requestParams['pageSize'])){
                $pageSize = $requestParams['pageSize'];
            }
            // 分页列表
            $list =  $this->quickSearch($this->likeField,$this->arrayField,$this->isdataright,$this->isdatadeptright)
                ->field($field)
                ->diygwOrder()
                ->paginate([
                    'list_rows'=> $pageSize,
                    'var_page' => 'pageNum',
                ])->toArray();

        }
//        $this->getLastSql()
        //对结果返回前进行处理
        if ($this->afterList($list)) {
            return ['rows'  => $list['data'],'total' => $list['total']];
        }else{
            return ['rows'  => [],'total' => 0];
        }
    }

    /**
     * 查询获取所有数据列表
     *
     * @time 2022年03月28日
     * @return mixed
     */
    public function getAllList()
    {
        $field= $this->showField;
        $data =   $this->quickSearch()
            ->field($field)
            ->diygwOrder()
            ->select()->toArray();
        $list['data']= $data;
        $list['total']= count($data);
        //对结果返回前进行处理
        if ($this->afterList($list)) {
            return ['rows'  => $list['data'],'total' => $list['total']];
        }else{
            return ['rows'  => [],'count' => 0];
        }
    }

//    public function getPk(){
//        return  $this->pk;
//    }


    public function beforeAdd(&$data){
        return true;
    }

    public function afterAdd(&$data){
        return true;
    }


    public function beforeEdit(&$data){
        return true;
    }

    public function afterEdit(&$data){
        return true;
    }

    public function beforeDel(&$data){
        return true;
    }

    public function afterDel(&$data){
        return true;
    }

    /**
     *
     * @param array $data
     * @return bool
     */
    public function add(&$data)
    {
        try {
            $this->startTrans();
            $pk =  $this->pk;
            if ($this->beforeAdd($data) && $this->allowField($this->field)->save($this->filterData($data))) {
                $pkvalue =  $this->getLastInsID();
                $data[Str::camel($pk)] = $pkvalue;
                $this->afterAdd($data);
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
            $pk =  $this->pk;
            $id = $data[Str::camel($pk)];
            if ($this->beforeEdit($data) && static::update($this->filterData($data), [$pk => $id])) {
                $this->updateChildren($id, $data);
                $this->afterEdit($data);
//                $this->getLastSql()
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


    public function copy($data){
        $pk =  $this->pk;
        $id = $data['id'];
        $data = static::where($pk, $id)->find()->toArray();
        unset($data[Str::camel($pk)]);
        static::save($data);
        $pkvalue = $this->getLastInsID();
        $data[Str::camel($pk)] = $pkvalue;
        if(isset($data['parentId'])){

        }
        return $data;
    }

    /**
     *
     * @param $id
     * @param bool $force
     * @return mixed
     */
    public function del($id, bool $force = false)
    {
        try {
            $this->startTrans();
            $ids = is_array($id['id']) ? $id['id'] : Utils::stringToArrayBy($id['id']);
            if ($this->beforeDel($ids) ) {
                $flag = static::destroy($ids, $force);
                $this->afterDel($ids);
                $this->commit();
                return $flag;
            }else{
                if(empty($this->error)){
                    $this->error = "删除失败";
                }
                return  false;
            }
        }catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return false;
    }


    /**
     * 用于循环插入
     *
     * @time 2022年03月21日
     * @param array $data
     * @return mixed
     */
    public function createData(array $data)
    {
        $model = static::create($data, $this->field, true);
        return $model->{$this->pk};
    }

    public function afterGet($data){
        return $data;
    }

    public function get($id){
        $pk =  $this->pk;
        $data = static::where($pk, $id['id'])->find();
        if($data){
            return $this->afterGet($data->toArray());
        }else{
            return $this->afterGet([]);
        }
    }
    /**
     *
     * @param $id
     * @param array $field
     * @param bool $trash
     * @return mixed
     */
    public function findData($id, array $field = ['*'], bool $trash = false)
    {
        if ($trash) {
            return static::onlyTrashed()->find($id);
        }
        return static::where($this->pk, $id)->field($field)->find();
    }


    /**
     * 批量插入
     *
     * @param array $data
     * @return mixed
     */
    public function insertAllData(array $data)
    {
        $newData = [];
        foreach ($data as $item) {
            foreach ($item as $field => $value) {
                if (!in_array($field, $this->field)) {
                    unset($item[$field]);
                }

                if (in_array($this->createTime, $this->field)) {
                    $item[$this->createTime] = time();
                }

                if (in_array($this->updateTime, $this->field)) {
                    $item[$this->updateTime] = time();
                }
            }
            $newData[] = $item;
        }
        return $this->insertAll($newData);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function recover($id)
    {
        return static::onlyTrashed()->find($id)->restore();
    }

    /**
     * 获取删除字段
     *
     * @time 2022年03月18日
     * @return mixed
     */
    public function getDeleteAtField()
    {
        return $this->deleteTime;
    }

    /**
     * 更新下级
     *
     * @time 2022年03月28日
     * @param $parentId
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @return void
     */
    protected function updateChildren($parentId, $data)
    {
        if (property_exists($this, 'updateChildrenFields')) {
            $parentIdField = property_exists($this, 'parentId') ? $this->$parentId : 'parent_id';

            if (!empty($this->updateChildrenFields)) {
                if (is_array($this->updateChildrenFields)) {
                    foreach ($data as $field => $value) {
                        if (! in_array($field, $this->updateChildrenFields)) {
                            unset($data[$field]);
                        }
                    }

                    $this->recursiveUpdate($parentId, $parentIdField, $data);
                }

                if (is_string($this->updateChildrenFields) && isset($data[$this->updateChildrenFields])) {
                    $this->recursiveUpdate($parentId, $parentIdField, [
                        $this->updateChildrenFields => $data[$this->updateChildrenFields]
                    ]);
                }
            }
        }
    }

    /**
     * 递归更新子级
     *
     * @time 2022年03月25日
     * @param $parentId
     * @param $parentIdField
     * @param $updateData
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @return void
     */
    public function recursiveUpdate($parentId, $parentIdField, $updateData)
    {
        $this->where($parentIdField, $parentId)->update($updateData);

        $children = $this->where($parentIdField, $parentId)->select();

        if ($children->count()) {
            foreach ($children as $child) {
                $this->recursiveUpdate($child->id, $parentIdField, $updateData);
            }
        }
    }

    /**
     * 别名
     *
     * @time 2022年03月18日
     * @param $field
     * @param string $table
     * @return array|string
     */
    public function aliasField($field, $table = '')
    {
        $table = $table ? Utils::tableWithPrefix($table) : $this->getTable();

        if (is_string($field)) {
            return sprintf('%s.%s', $table, $field);
        }

        if (is_array($field)) {
            foreach ($field as &$value) {
                $value = sprintf('%s.%s', $table, $value);
            }

            return $field;
        }

        return $field;
    }

    /**
     * 禁用/启用
     *
     * @time 2020年06月29日
     * @param $id
     * @param string $field
     * @return mixed
     */
    public function disOrEnable($id, string $field='status')
    {
        $model = $this->findBy($id);

        $status = $model->{$field} == self::DISABLE ? self::ENABLE : self::DISABLE;

        $model->{$field} = $status;

        return $model->save();
    }

    /**
     * 过滤数据
     *
     * @time 2021年02月28日
     * @param array $data
     * @return mixed
     */
    public function filterData(array $data)
    {
        $pk = $this->pk;
        foreach ($data as $field => $value) {
            if ((is_null($value))||$value=='null'||$value=='undefined') {
                if($value!='0'){
                    unset($data[$field]);
                }
            }
            if(is_array($value)){
                $data[$field] = json_encode($value,JSON_UNESCAPED_UNICODE);
            }

            if ($field == $pk ||$field== Str::camel($pk) ) {
                unset($data[$field]);
            }
            $excludeTimeField =  [$this->createTime, $this->updateTime, $this->deleteTime,Str::camel($this->createTime), Str::camel($this->updateTime)];
            if($this->deleteTime){
                $excludeTimeField[] = Str::camel($this->deleteTime);
            }
            if (in_array($field, $excludeTimeField)) {
                unset($data[$field]);
            }
        }

        return $data;
    }


    /**
     * 对某个字段增加值
     * @param $id
     * @param $field
     * @param $value
     * @return bool
     */
    public function incValue($id,$key,$value){
        $data = compact('id','key','value');
        return $this->incOrDec($data);
    }
    /**
     *
     * @param $id
     * @param $field
     * @param $value
     * @return bool
     */
    public function decValue($id,$key,$value){
        $value = 0 - $value;
        $data = compact('id','key','value');
        return $this->incOrDec($data);
    }


    public function incOrDec($data){
        $id = $data['id'];
        $value = $data['value'];
        $key = $data['key'];
        if (is_numeric($value)) {
            $value = floatval($value);
        }
        $operation = "+";
        if($value<0){
            $operation = " ";
        }
        $table = $this->getName();
        $pk = $this->pk;
        $sql = "update $table set $key=$key $operation $value where $pk=$id";
        Db::execute($sql);
        return true;
    }


    /**
     * 对某个字段进行排序
     * @param $data
     * @return bool
     */
    public function sortnum($data){
        $pk =  $this->pk;
        $id = $data[Str::camel($pk)];
        //如果有传入排序字段，对排序字段操作
        $sortfield = 'sortnum';
        if(isset($data['sortfield'])){
            $orderfield = $data['sortfield'];
        }
        $sortnum = $this->max($sortfield);
        if(empty($sortnum)){
            $sortnum = 1;
        }else{
            $sortnum = $sortnum+1;
        }
        static::update([$sortfield=>$sortnum], [$pk => $id]);
        return true;
    }

}
