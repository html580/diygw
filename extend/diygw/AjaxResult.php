<?php


namespace diygw;

use think\exception\HttpResponseException;
use think\Response;
use think\response\Json;

class AjaxResult
{

    /**
     * @notes 接口操作成功，返回信息
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return Json
     */
    public static function success(int $code = 1, string $msg = 'success', array $data = []): Json
    {
        return self::result($code, $msg, $data);
    }

    /**
     * @notes 接口操作失败，返回信息
     * @param string $msg
     * @param array $data
     * @param int $code
     * @date 2021/12/24 18:28
     */
    public static function fail(int $code = 0, string $msg = 'fail', array $data = []): Json
    {
        return self::result($code,  $msg, $data);
    }



    /**
     * @notes 接口返回数据
     * @param $data
     * @return Json
     */
    public static function data($data): Json
    {
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
    private static function result(int $code, string $msg = 'OK', array $data = [], int $httpStatus = 200): Json
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
    public static function throw(int $code = 0,string $msg = 'fail', array $data = []): Json
    {
        $data = compact('code', 'msg', 'data');
        $response = Response::create($data, 'json', 200);
        throw new HttpResponseException($response);
    }

}