# Sort
> 数据移动和排序 

## 类库引入 
> use think\diygw\helper\Sort;
## 初始化
```
    $sort = new Sort(['name'=>'menu','id'=>'id','pid'=>'pid','sort'=>'sort']);
```
## 移动
```
   $sort->move(['id'=>11,'pid'=>1]);
```
## 排序
```
   $sort->sort(['pid'=>$data['pid']],$id,$sort);
```


