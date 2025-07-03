<?php
declare (strict_types = 1);

namespace app;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use thans\jwt\exception\TokenBlacklistException;
use thans\jwt\exception\TokenExpiredException;
use thans\jwt\exception\TokenInvalidException;
use thans\jwt\facade\JWTAuth;
use think\App;
use think\Exception;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\View;
use think\helper\Str;
use think\Response;
use think\response\Json;
use think\Validate;


/**
 * 控制器基础类
 */
abstract class BaseController
{
    //是否显示所有数据
    public $isAll = true;
    //是否导出
    public $isExport = true;
    //是否初始化模型
    public $isModel = false;
    //判断是否全部不需要登录
    public $notNeedLoginAll = false;
    //判断不需要登录的方法
    public $notNeedLogin = ['test'];
    //默认登录用户id
    public $tokenKey = 'uid';

    public $isDebug = false;

    public $userId;
    public $model;

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->isDebug = $app->isDebug();
        $this->request = $this->app->request;
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        //如需要登录
        if (!$this->isNotNeedLogin()||$this->request->param("isself")=='1') {
            try {
                $payload = JWTAuth::auth(); //可验证token, 并获取token中的payload部分
                $this->request->userId = $payload[$this->tokenKey];
                if(isset( $payload['username'])){
                    $this->request->sessionusername = $payload['username'];
                }
                $this->userId = $this->request->userId;
            } catch (\Exception $e) {
                $msg = '登录过期';
                if ($e instanceof TokenExpiredException) {
                    $msg = '登录过期';
                }
                if ($e instanceof TokenBlacklistException) {
                    $msg = '登录被加入黑名单';
                }
                if ($e instanceof TokenInvalidException) {
                    $msg = '登录不合法';
                }
                echo json_encode(['code' => 401, 'msg' => $msg]);
                exit();
            }
        }
        //判断是否要获取用户MODEL
        if($this->isModel){
            $root = $this->request->rootUrl();
            $root = str_replace("/","",$root);
            $controller =  str_replace('.', '\\',$this->request->controller());
            $modelClass = '\\app\\'.$root.'\\model\\' . $controller . 'Model';
            if(class_exists($modelClass)){
                $this->model = new $modelClass();
            } else{
                if(strpos($controller,'api\\')!==false){
                    $controller = ucfirst(str_replace("api\\","",$controller));
                }else{
                    $controller = ucfirst($controller);
                }
                $modelClass = '\\app\\'.$root.'\\model\\' . $controller . 'Model';
                if(class_exists($modelClass)){
                    $this->model = new $modelClass();
                }else{
                    $root = ucfirst($root);
                    $modelClass = "\\app\\common\\model\\".$root.$controller."Model";
                    if(class_exists($modelClass)){
                        $this->model = new $modelClass();
                    }
                }
            }
            if(!$this->model){
                echo json_encode(['code' => 500, 'msg' => '未找到对应的模型']);
                exit();
            }
        }
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }


    public function success( string $msg = 'success'): Json{
        return $this->result([],200,$msg);
    }

    /**
     * @notes 接口操作成功，返回信息
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return Json
     */
    public function successData(array $data = [], string $msg = 'success'): Json
    {
        return self::result($data,200, $msg);
    }

    public function error( string $msg = 'fail'): Json{
        return $this->result([],500,$msg);
    }


    /**
     * @notes 接口操作失败，返回信息
     * @param string $msg
     * @param array $data
     * @param int $code
     */
    public function errorData(array $data = [], string $msg = 'fail'): Json
    {
        return self::result($data,500,  $msg);
    }

    /**
     * @notes 接口返回信息
     * @param int $code
     * @param string $msg
     * @param array $data
     * @param int $httpStatus
     * @return Json
     */
    public  function errorMsg( int $code, string $msg = 'OK'): Json
    {
        return self::result([],$code,  $msg);
    }

    /**
     * @notes 接口返回数据
     * @param $data
     * @return Json
     */
    public function data($data): Json
    {
        return json($data, 200);
    }

    /**
     * @notes 接口分页操作成功，返回信息
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return Json
     */
    public function pageData(array $data = [], int $code = 200, string $msg = 'success'): Json
    {
        $data['code'] = $code;
        $data['msg'] = $msg;
        return json($data, 200);
    }

    /**
     * @notes 接口返回信息
     * @param int $code
     * @param string $msg
     * @param array $data
     * @param int $httpStatus
     * @return Json
     */
    public  function result( array $data = [], int $code=200, string $msg = 'OK',int $httpStatus = 200): Json
    {
        if(count($data)==0){
            $result = compact('code', 'msg');
        }else{
            $result = compact('code', 'msg', 'data');
        }
        return json($result, $httpStatus);
    }



    /**
     * @notes 抛出异常json
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return Json
     */
    public function throw(int $code = 0,string $msg = 'fail', array $data = []): Json
    {
        $data = compact('code', 'msg', 'data');
        $response = Response::create($data, 'json', 200);
        throw new HttpResponseException($response);
    }


    /**
     * @notes 是否免登录验证
     * @return bool
     */
    public function isNotNeedLogin() : bool
    {
        //如果为真表示该类下面的所有方法都不需要登录
        if($this->notNeedLoginAll){
            return  true;
        }
        $notNeedLogin = $this->notNeedLogin;
        if (empty($notNeedLogin)) {
            return false;
        }
        $action = $this->request->action();

        if (!in_array(trim($action), $notNeedLogin)) {
            return false;
        }
        return true;
    }


    /**
     * 获取分页查询数据结果
     */
    public function list()
    {
        $pageData = $this->model->getList();
        return $this->pageData($pageData);
    }


    /**
     * 提交验证数据
     */
    public function checkData(){
        $root = $this->request->rootUrl();
        $root = str_replace("/","",$root);
        $controller = str_replace('.', '\\',$this->request->controller());
        $validateClass = '\\app\\'.$root.'\\validate\\' . $controller . 'Validate';
        if(class_exists($validateClass)){
            $validate = new $validateClass();
            return $validate->checkData();
        }
        return true;
    }
    /**
     * 新增记录
     */
    public function add()
    {
        try {
            $data = $this->request->param();
            $pk = $this->model->diygwPk();

            if(isset($data[Str::camel($pk)])){
                $id = $data[Str::camel($pk)];
            }

            $data['userId'] = $this->request->userId;
            if(!empty($this->request->sessionusername)){
                $data['username'] = $this->request->sessionusername;
            }

            if($this->checkData()){
                //如果对应的主要不为空，表示修改记录
                if(isset($id)&&!empty($id)){
                    $data = $this->model->edit($data);
                    if ($data) {
                        return $this->successData($data,'修改成功');
                    } else {
                        return $this->error($this->isDebug?$this->model->error:'修改失败');
                    }
                }else{
                    $data = $this->model->add($data);
                    if($data){
                        return $this->successData($data,'新增成功');
                    }else{
                        return $this->error($this->isDebug?$this->model->error:'新增失败');
                    }
                }
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * 更新记录
     */
    public function update()
    {
        try {
            if($this->checkData()) {
                $data = $this->request->param();
                $data = $this->model->edit($data);
                if ($data) {
                    return $this->successData($data,'修改成功');
                } else {
                    return $this->error($this->isDebug?$this->model->error:'修改失败');
                }
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    public function del(){
        try {
            $data = $this->request->param();
            $pageData = $this->model->del($data,false);
            if($pageData){
                return $this->success("删除成功");
            }else{
                return $this->error($this->isDebug?$this->model->error:"删除失败");
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }


    public function copy(){
        try {
            $data = $this->request->param();
            $pageData = $this->model->copy($data);
            if($pageData){
                return $this->success("复制成功");
            }else{
                return $this->error("复制失败");
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * 新增记录
     */
    public function get()
    {
        try {
            $data = $this->request->param();
            $data = $this->model->get($data);
            if($data){
                return $this->successData($data);
            }else{
                return $this->error('获取数据失败');
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * 获取所有数据结果 为了数据安全性  一般情况不给返回所有数据，如果需要返回，请设置isAll=true
     * @return Json
     */
    public function all(){
        if($this->isAll){
            $pageData = $this->model->getAllList();
            return $this->pageData($pageData);
        }else{
            return $this->error('不允许返回所有数据');
        }
    }


    public function clear(){
        $this->error("为了安全，请重写清空方法");
    }

    /**
     * 对某个字段增加或减少数量
     */
    public function incOrDec()
    {
        try {
            $data = $this->request->param();
            $data = $this->model->incOrDec($data);
            if ($data) {
                return $this->success('设置成功');
            } else {
                return $this->error('设置失败');
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * 对某个字段增加或减少数量
     */
    public function incOrDecs()
    {
        try {
            $data = $this->request->param();
            $data = $this->model->incOrDecs($data);
            if ($data) {
                return $this->successData($data,'设置成功');
            } else {
                return $this->error('设置失败');
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /*
     * 对某个字段进行排序增加
     */
    public function sortnum(){
        try {
            $data = $this->request->param();
            $data = $this->model->sortnum($data);
            if ($data) {
                return $this->success('设置成功');
            } else {
                return $this->error('设置失败');
            }
        }catch (Exception $exception){
            return $this->error($exception->getMessage());
        }
    }


    /**
     * 输出模板
     * @param string $template
     * @param array $vars
     */
    public function fetch(string $template = '', array $vars = []){
        if(empty($template)){
            return View::fetch();
        }else{
            return View::fetch($template,$vars);
        }
    }


    /**
     * 输出值
     * @param $name
     * @param mixed|null $value
     */
    public function assign($name, $value)
    {
        View::assign($name,$value);
    }

    /**
     * 加工导出表结构
     * @param $fields
     * @return mixed
     */
    public function getExportFields($fields){
        //比如字典转换数据 可以参照导出角色RoleController里的getExportFields方法的处理。
        return $fields;
    }


    public function getExportDatas($datas){
        //比如字典转换数据 可以参照导出角色RoleController里的getExportFields方法的处理。
        return $datas;
    }

    /**
     * 获取导出表对应字段转换的数据
     * @return array
     */
    public function getExportFieldsData(){
        //比如字典转换数据 可以参照导出角色RoleController里的getExportFieldsData方法的处理。
        return [];
    }
    /**
     * @return App
     */
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $worksheet = $spreadsheet->getActiveSheet();
        if(!$this->isExport){
            $worksheet->setCellValue("A1", "无权限导出数据，请开启导出权限");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="导出数据.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        }else{
            $pageData = $this->model->getAllList();
            $fields = $this->model->getFields();
            $exportFields = [];
            $excludeFields = ['create_by','update_by','delete_time'];
            foreach ($fields as $field){
                if(in_array($field['name'],$excludeFields)){
                    continue;
                }
                if($field['name']=='create_time'){
                    $field['comment'] = '创建时间';
                }else if($field['name']=='update_time'){
                    $field['comment'] = '创建时间';
                }
                $exportFields[] = $field;
            }
            //二次加工导出字段
            $exportFields = $this->getExportFields($exportFields);
            $exportFieldsData = $this->getExportFieldsData();

            $rows = $pageData['rows'];
            $rows  = $this->getExportDatas($rows);



            $row_num = 1;
            foreach ($exportFields as $index=>$field){
                $cols_num = Coordinate::stringFromColumnIndex(($index+1));
                $worksheet->getColumnDimension($cols_num)->setWidth(30);
                $title = $field['comment'];
                if(empty($title)){
                    $title  = $field['name'];
                }
                $worksheet->setCellValue($cols_num.$row_num, $title);
            }

            foreach ($rows as $rowData){
                $row_num++;
                foreach ($exportFields as $index=>$field){
                    $cols_num = Coordinate::stringFromColumnIndex(($index+1));
                    //判断是否存在的字典转换数据
                    if(isset($exportFieldsData[Str::camel($field['name'])])){
                        $datas = $exportFieldsData[Str::camel($field['name'])];

                        //查找有没有，如果没有还是直接照常输出值
                        $value = $rowData[Str::camel($field['name'])];
                        foreach ($datas as $data){
                            //查找是否有对应转换的数据，有则输出
                            if($data['value']==$rowData[Str::camel($field['name'])]){
                                $value = $data['label'];
                                break;
                            }
                        }
                        $worksheet->setCellValue($cols_num.$row_num,$value);
                    }else{
                        $worksheet->setCellValue($cols_num.$row_num,$rowData[Str::camel($field['name'])]);
                    }
                }
            }

            //设置样式
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,   //水平居中
                    'vertical' => Alignment::VERTICAL_CENTER,       //垂直居中
                ],
                'borders' => [
                    'allBorders' => [   //所有边框
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000']
                    ],
                ],
            ];
            $columnEnd = Coordinate::stringFromColumnIndex((count($exportFields)));
            $worksheet->getStyle('A1:'.$columnEnd.(count($rows)+1))->applyFromArray($styleArray);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="导出数据.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        }

    }
}
