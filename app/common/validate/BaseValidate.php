<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\exception\HttpResponseException;
use think\Response;
use think\response\Json;
use think\Validate;

class BaseValidate extends Validate
{

    /**
     * @notes 切面验证接收到的参数
     * @param null $scene 场景验证
     * @param array $validate_data 验证参数，可追加和覆盖掉接收的参数
     * @param array $validate_data 验证参数，可追加和覆盖掉接收的参数
     * @return array
     * @author 邓志锋
     * @date 2022/03/27 10:13
     */
    public function checkData($scene = null, array $validate_data = [],$method='Param'): array
    {
        //接收参数
        if ($method == 'GET') {
            $params = request()->get();
            if (!request()->isGet()) {
                $this->throw('请求方式错误，请使用get请求方式');
            }
        } else if ($method == 'GET') {
            $params = request()->post();
            if (!request()->isPost()) {
                $this->throw('请求方式错误，请使用post请求方式');
            }
        } else {
            $params = request()->param();
        }
        //合并验证参数
        $params = array_merge($params, $validate_data);
        //场景
        if ($scene) {
            $result = $this->scene($scene)->check($params);
        } else {
            $result = $this->check($params);
        }
        if (!$result) {
            $exception = is_array($this->error) ? implode(';', $this->error) : $this->error;
            $this->throw($exception);
        }
        return $params;
    }


    /**
     * @notes 抛出异常json
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Json
     * @author 邓志锋
     * @date 2022/03/27 10:13
     */
    public function throw(string $msg = 'fail', array $data = [], int $code = 500, int $show = 1): Json
    {
        $data = compact('code', 'show', 'msg', 'data');
        $response = Response::create($data, 'json', 200);
        throw new HttpResponseException($response);
    }
}
