# Tree
> 构建Tree数据

## 类库引入 
> use diygw\helper\Tree;
 
```

   [
       ["id"=>"1","city"=>"中国", "parent_id"=>"0"],
       ["id"=>"2","city"=>"北京", "parent_id"=>"1"],
       ["id"=>"3","city"=>"北京市", "parent_id"=>"2"],
       ["id"=>"4","city"=>"东城区", "parent_id"=>"3"],
       ["id"=>"5","city"=>"西城区", "parent_id"=>"3"],
       ["id"=>"6","city"=>"丰台区", "parent_id"=>"3"],
       ["id"=>"7","city"=>"海淀区", "parent_id"=>"3"],
       ["id"=>"8","city"=>"房山区", "parent_id"=>"3"],
       ["id"=>"9","city"=>"通州区", "parent_id"=>"3"],
       ["id"=>"10","city"=>"昌平区", "parent_id"=>"3"],
       ["id"=>"11","city"=>"上海", "parent_id"=>"1"],
       ["id"=>"12","city"=>"上海市", "parent_id"=>"11"],
       ["id"=>"13","city"=>"黄浦区", "parent_id"=>"11"],
       ["id"=>"14","city"=>"长宁区", "parent_id"=>"11"],
       ["id"=>"15","city"=>"卢湾区", "parent_id"=>"11"],
       ["id"=>"16","city"=>"徐汇区", "parent_id"=>"11"],
       ["id"=>"17","city"=>"普陀区", "parent_id"=>"11"],
       ["id"=>"18","city"=>"闸北区", "parent_id"=>"11"],
       ["id"=>"19","city"=>"虹口区", "parent_id"=>"11"],
   ];
   //设置主键、节点名称
   (new Tree())->setAttr(['child'=>'operater'])->getTree($data);
    
```


