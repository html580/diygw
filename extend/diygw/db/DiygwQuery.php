<?php
declare(strict_types=1);

namespace diygw\db;

use diygw\model\DiygwModel;
use think\db\Query;
use think\helper\Str;
use think\Paginator;

class DiygwQuery extends Query
{

    /**
     *
     * @time 2022年03月18日
     * @param mixed $model
     * @param string $joinField
     * @param string $currentJoinField
     * @param array $field
     * @param string $type
     * @param array $bind
     * @return DiygwQuery
     */
    public function diygwJoin($model, string $joinField, string $currentJoinField, array $field = [], string $type = 'INNER', array $bind = []): DiygwQuery
    {
        $tableAlias = null;

        if (is_string($model)) {
            $table = app($model)->getTable();
        } else {
            list($model, $tableAlias) = $model;
            $table = app($model)->getTable();
        }

        // 合并字段
        $this->options['field'] = array_merge($this->options['field'] ?? [], array_map(function ($value) use ($table, $tableAlias) {
            return ($tableAlias ? : $table) . '.' . $value;
        }, $field));

        return $this->join($tableAlias ? sprintf('%s %s', $table, $tableAlias) : $table

            , sprintf('%s.%s=%s.%s', $tableAlias ? $tableAlias : $table, $joinField, $this->getAlias(), $currentJoinField), $type, $bind);
    }

    /**
     *
     * @time 2022年03月18日
     * @param mixed $model
     * @param string $joinField
     * @param string $currentJoinField
     * @param array $field
     * @param array $bind
     * @return DiygwQuery
     */
    public function diygwLeftJoin($model, string $joinField, string $currentJoinField, array $field = [], array $bind = []): DiygwQuery
    {
        return $this->diygwJoin($model, $joinField,  $currentJoinField,  $field,'LEFT', $bind);
    }

    /**
     *
     * @time 2022年03月18日
     * @param mixed $model
     * @param string $joinField
     * @param string $currentJoinField
     * @param array $field
     * @param array $bind
     * @return DiygwQuery
     */
    public function diygwRightJoin($model, string $joinField, string $currentJoinField, array $field = [], array $bind = []): DiygwQuery
    {
        return $this->diygwJoin($model, $joinField,  $currentJoinField, $field,'RIGHT', $bind);
    }

    /**
     * rewrite
     *
     * @time 2022年03月18日
     * @param array|string $field
     * @param bool $needAlias
     * @return $this|Query
     */
    public function withoutField($field, bool $needAlias = false)
    {
        if (empty($field)) {
            return $this;
        }

        if (is_string($field)) {
            $field = array_map('trim', explode(',', $field));
        }

        // 过滤软删除字段
        $field[] = $this->model->getDeleteAtField();

        // 字段排除
        $fields = $this->getTableFields();
        $field  = $fields ? array_diff($fields, $field) : $field;

        if (isset($this->options['field'])) {
            $field = array_merge((array) $this->options['field'], $field);
        }

        $this->options['field'] = array_unique($field);

        if ($needAlias) {
            $alias = $this->getAlias();
            $this->options['field'] = array_map(function ($field) use ($alias) {
                return $alias . '.' . $field;
            }, $this->options['field']);
        }

        return $this;
    }

    /**
     *
     * @time 2022年03月18日
     * @param array $params
     * @return DiygwQuery
     */
    public function diygwSearch(array $params = []): DiygwQuery
    {
        $params = empty($params) ? \request()->param() : $params;

        if (empty($params)) {
            return $this;
        }

        foreach ($params as $field => $value) {
            $method = 'search' . Str::studly($field) . 'Attr';
            // value in [null, '']
            if ($value !== null && $value !== '' && method_exists($this->model, $method)) {
                $this->model->$method($this, $value, $params);
            }
        }

        return $this;
    }

    /**
     * 快速搜索
     *
     * @param array $likeField
     * @return Query
     */
    public function quickSearch(array $likeField = [],array $arrayField = []): Query
    {
        $requestParams = \request()->param();
        if (empty($requestParams)) {
            return $this;
        }
        $fields = $this->getFields();
        $exclueFields = ['pageNum','pageSize','page','limit','orderby'];
        foreach ($requestParams as $field => $value) {
            if (in_array($field,$exclueFields) || empty($value)  || $value=='undefined'){
                if(!is_numeric($value)){
                    continue;
                }
            }
            if (isset($params[$field])) {
                // ['>', value] || value
                if (in_array($field, array_keys($fields))) {
                    if (is_array($params[$field])) {
                        $this->where(Str::snake($field), $params[$field][0], $params[$field][1]);
                    } else {
                        $this->where(Str::snake($field), $value);
                    }
                }
            } else {
                if($field=='searchfields_or'){
                    $values = explode('_or_',$value);
                    $this->where(function ($query) use ($values,$fields) {
                        foreach ($values as $keyvalue){
                            $keyvalues = explode('_',$keyvalue);
                            $field = $keyvalues[0];
                            if(count($keyvalues)==2){
                                if (in_array($field, array_keys($fields))) {
                                    $query->whereOr($field,$keyvalues[1]);
                                }
                            }
                            if(count($keyvalues)==3){
                                $value = $keyvalues[2];
                                $type = $keyvalues[1];
                                if (in_array($field, array_keys($fields))) {
                                    if($type=="gt"){
                                        $query->whereOr($field,">",$value);
                                    }else if($type=="gte"){
                                        $query->whereOr($field,">=",$value);
                                    }else if($type=="lt"){
                                        $query->whereOr($field,"<",$value);
                                    }else if($type=="lte"){
                                        $query->whereOr($field,"<=",$value);
                                    }else if($type=="like"){
                                        $query->whereLike($field,$value,'or');
                                    }else if($type=="neq"){
                                        $query->whereOr($field,'<>', $value);
                                    }else if($type=="neq"){
                                        $query->whereIn($field,explode(",",$value),'or');
                                    }else if($type=="null"){
                                        $query->whereRightLike($field,$value,'or');
                                    }else if($type=="notnull"){
                                        $query->whereNotNull($field,$value,'or');
                                    }else if($type=="rightlike"){
                                        $query->whereRightLike($field, $value,'or');
                                    }else if($type=="leftlike"){
                                        $query->whereLeftLike($field, $value,'or');
                                    }
                                }
                            }
                        }
                    });
                }

                // 区间范围 数据库字段_start & 数据库字段_end
                $endField = "_start";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '>=', $value);
                        continue;
                    }
                }

                $endField = "_gt";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '>', $value);
                        continue;
                    }
                }

                $endField = "_gte";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '>=', $value);
                        continue;
                    }
                }

                $endField = "_lt";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '<', $value);
                        continue;
                    }
                }

                $endField = "_lte";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '<=', $value);
                        continue;
                    }
                }

                $endField = "_end";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '<=', $value);
                        continue;
                    }
                }

                // 模糊搜索
                $endField = "_like";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->whereLike($field, $value);
                        continue;
                    }
                }
                // 模糊搜索
                $endField = "_leftlike";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->whereLeftLike($field, $value);
                        continue;
                    }
                }

                $endField = "_notnull";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->whereNotNull($field);
                        continue;
                    }
                }

                $endField = "_null";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->whereNull($field);
                        continue;
                    }
                }

                // 模糊搜索
                $endField = "_rightlike";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->whereRightLike($field, $value);
                        continue;
                    }
                }

                $endField = "_range";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        if(is_array($value)&&count($value)==2){
                            $this->where($field, '>=', $value[0].' 00:00:00');
                            $this->where($field, '<=', $value[1].' 23:59:59');
                        }else if(is_string($value) && strpos($value," - ")!== false){
                            $value =  explode(" - ",$value);
                            if(count($value)==2){
                                $this->where($field, '>=', $value[0].' 00:00:00');
                                $this->where($field, '<=', $value[1].' 23:59:59');
                            }
                        }
                        continue;
                    }
                }
                $endField = "_neq";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, '<>', $value);
                        continue;
                    }
                }

                $endField = "_eq";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if (in_array($field, array_keys($fields))) {
                        $this->where($field, $value);
                        continue;
                    }
//                    $searchfields = explode("_",$field);
//                    //查询某个字段是否有限
//                    $searchTablefields = [];
//                    $tablefields = array_keys($fields);
//                    foreach ($searchfields as $tfield){
//                        if(in_array($tfield,$tablefields)){
//                            $searchTablefields[] = Str::snake($tfield);
//                        }
//                    }
//                    $this->where(implode("|",$searchTablefields),'=',$value );
                    continue;
                }

                $endField = "_in";
                if (Str::endsWith($field,$endField)) {
                    $field = Str::snake(Str::substr($field,0,Str::length($field) - Str::length($endField)));
                    if(is_array($value)){
                        $this->whereIn($field,$value);
                    }else if(Str::startsWith($value,"[")&&Str::endsWith($value,"[")){
                        $this->whereIn($field,json_encode($value,true));
                    }else {
                        $this->whereIn($field,explode(",",$value));
                    }
                    continue;
                }

                if($field=='isself' && $value=='1' && (in_array('user_id',array_keys($fields))||in_array('userid',array_keys($fields)))){
                    if(in_array('user_id',array_keys($fields))){
                        $this->where('user_id',\request()->userId);
                    }else{
                        $this->where('userid',\request()->userId);
                    }
                    continue;
                }


//                // = 值搜索
//                if ($value || is_numeric($value)) {
//                    $tablefield = Str::snake($field);
//                    if (in_array($tablefield, array_keys($fields))) {
//                        if(in_array($field,$likeField)){
//                            $this->whereLike($tablefield, $value);
//                        }else{
//                            $this->where($tablefield, $value);
//                        }
//                    }
//                    if($field=='isself' && $value=='1' && in_array('user_id',array_keys($fields))){
//                        $this->where('user_id',\request()->userId);
//                    }
//                }

                // = 值搜索
                if ($value || is_numeric($value)) {
                    $tablefield = Str::snake($field);
                    if (in_array($tablefield, array_keys($fields))) {
                        $findLike = false;
                        //查找查似字段
                        foreach ($likeField as $searchfield) {
                            //查找每个字段是否存在|
                            $searchfields = explode("|", $searchfield);
                            if (in_array($field, $searchfields)) {
                                $tablefields = [];
                                foreach ($searchfields as $tfield) {
                                    $tablefields[] = Str::snake($tfield);
                                }
                                $findLike = true;
                                $this->where(implode("|", $tablefields), 'like', "%$value%");
                            }
                        }
                        //如果没找到
                        if (!$findLike) {
                            if (in_array($field, $arrayField)) {
                                $this->where("JSON_CONTAINS({$tablefield},'{$value}')");
                            } else {
                                $this->where($tablefield, $value);
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     *
     * @time 2022年03月18日
     * @return mixed
     */
    public function getAlias()
    {
        return isset($this->options['alias']) ? $this->options['alias'][$this->getTable()] : $this->getTable();
    }

    /**
     * rewrite
     *
     * @time 2022年03月18日
     * @param string $field
     * @param mixed $condition
     * @param string $option
     * @param string $logic
     * @return Query
     */
    public function whereLike(string $field, $condition, string $logic = 'AND', string $option = 'both'): Query
    {
        switch ($option) {
            case 'both':
                $condition = '%' . $condition . '%';
                break;
            case 'left':
                $condition = '%' . $condition;
                break;
            default:
                $condition .= '%';
        }

        if (strpos($field, '.') === false) {
            $field = $this->getAlias() . '.' . $field;
        }

        return parent::whereLike($field, $condition, $logic);
    }

    /**
     * @param string $field
     * @param $condition
     * @param string $logic
     * @return Query
     */
    public function whereLeftLike(string $field, $condition, string $logic = 'AND'): Query
    {
        return $this->where($field, $condition, $logic, 'left');
    }

    /**
     * @param string $field
     * @param $condition
     * @param string $logic
     * @return Query
     */
    public function whereRightLike(string $field, $condition, string $logic = 'AND'): Query
    {
        return $this->where($field, $condition, $logic, 'right');
    }

    /**
     * 额外的字段
     *
     * @time 2022年03月18日
     * @param $fields
     * @return DiygwQuery
     */
    public function addFields($fields): DiygwQuery
    {
        if (is_string($fields)) {
            $this->options['field'][] = $fields;

            return $this;
        }

        $this->options['field'] = array_merge($this->options['field'], $fields);

        return $this;
    }

    public function paginate($listRows = null, $simple = false): Paginator
    {
        if (!$listRows) {
            $limit = \request()->param('limit');

            $listRows = $limit ? : DiygwModel::LIMIT;
        }

        return parent::paginate($listRows, $simple); // TODO: Change the autogenerated stub
    }


    /**
     * 默认排序
     *
     * @time 2022年03月17日
     * @param string $order
     * @return $this
     */
    public function diygwOrder(string $order = 'desc'): DiygwQuery
    {
        $requestParams = \request()->param();
        //如果前台有传入排序字段
        if(isset($requestParams['orderby'])){
            if($requestParams['orderby'] =='rand'){
                $this->orderRand();
            }else{
                $this->order($requestParams['orderby']);
            }
        }else{
            $fields = $this->getFields();

            if (in_array('sortnum', array_keys($fields))) {
                $this->order($this->getTable() . '.sortnum desc');
            }

            if (in_array('sort', array_keys($fields))) {
                $this->order($this->getTable() . '.sort asc');
            }

            if (in_array('weight', array_keys($fields))) {
                $this->order($this->getTable() . '.weight', $order);
            }

            $this->order($this->getTable() . '.' . $this->getPk(), $order);
        }


        return $this;
    }

    /**
     * 获取当前数据表的主键
     * @access public
     * @return string|array
     */
    public function diygwPk()
    {
        return $this->getPk();
    }

    /**
     * 新增 Select 子查询
     *
     * @time 2022年03月17日
     * @param callable $callable
     * @param string $as
     * @return $this
     */
    public function  addSelectSub(callable $callable, string $as): DiygwQuery
    {
        $this->field(sprintf('%s as %s', $callable()->buildSql(), $as));

        return $this;
    }

    /**
     * 字段增加
     *
     * @time 2020年11月04日
     * @param $field
     * @param int $amount
     * @return int
     *@throws \think\db\exception\DbException
     */
    public function increment($field, int $amount = 1): int
    {
        return $this->inc($field, $amount)->update();
    }

    /**
     * 字段减少
     *
     * @time 2020年11月04日
     * @param $field
     * @param int $amount
     * @return int
     *@throws \think\db\exception\DbException
     */
    public function decrement($field, int $amount = 1): int
    {
        return $this->dec($field, $amount)->update();
    }
}
