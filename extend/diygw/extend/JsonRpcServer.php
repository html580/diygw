<?php

declare (strict_types=1);

namespace diygw\extend;

use think\App;
use think\Container;
use think\exception\HttpResponseException;

/**
 * JsonRpc 服务端
 * Class JsonRpcServer
 * @package diygw\extend
 */
class JsonRpcServer
{
    /**
     * 当前App对象
     * @var App
     */
    protected $app;

    /**
     * JsonRpcServer constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 静态实例对象
     * @param array $args
     * @return static
     */
    public static function instance(...$args): JsonRpcServer
    {
        return Container::getInstance()->make(static::class, $args);
    }

    /**
     * 设置监听对象
     * @param mixed $object
     */
    public function handle($object)
    {
        // Checks if a JSON-RCP request has been received
        if ($this->app->request->method() !== "POST" || $this->app->request->contentType() !== 'application/json') {
            $this->printMethod($object);
        } else {
            // Reads the input data
            $request = json_decode(file_get_contents('php://input'), true) ?: [];
            if (empty($request)) {
                $error = ['code' => '-32700', 'message' => '语法解析错误', 'meaning' => '服务端接收到无效的JSON'];
                $response = ['jsonrpc' => '2.0', 'id' => '0', 'result' => null, 'error' => $error];
            } elseif (!isset($request['id']) || !isset($request['method']) || !isset($request['params'])) {
                $error = ['code' => '-32600', 'message' => '无效的请求', 'meaning' => '发送的JSON不是一个有效的请求对象'];
                $response = ['jsonrpc' => '2.0', 'id' => $request['id'] ?? '0', 'result' => null, 'error' => $error];
            } else try {
                if ($object instanceof \Exception) {
                    throw $object;
                } elseif (strtolower($request['method']) === '_get_class_name_') {
                    $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => get_class($object), 'error' => null];
                } elseif (method_exists($object, $request['method'])) {
                    $result = call_user_func_array([$object, $request['method']], $request['params']);
                    $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => $result, 'error' => null];
                } else {
                    $error = ['code' => '-32601', 'message' => '找不到方法', 'meaning' => '该方法不存在或无效'];
                    $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => null, 'error' => $error];
                }
            } catch (\diygw\exceptions\Exception $exception) {
                $error = ['code' => $exception->getCode(), 'message' => $exception->getMessage(), 'meaning' => '数据处理异常'];
                $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => $exception->getData(), 'error' => $error];
            } catch (\Exception $exception) {
                $error = ['code' => $exception->getCode(), 'message' => $exception->getMessage(), 'meaning' => '系统处理异常'];
                $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => null, 'error' => $error];
            }
            // Output the response
            throw new HttpResponseException(json($response));
        }
    }

    /**
     * 打印输出对象方法
     * @param mixed $object
     */
    protected function printMethod($object)
    {
        try {
            $object = new \ReflectionClass($object);
            echo "<h2>{$object->getName()}</h2><hr>";
            foreach ($object->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (stripos($method->getName(), '_') === 0) continue;
                $params = [];
                foreach ($method->getParameters() as $parameter) {
                    $type = $parameter->getType();
                    $params[] = ($type ? "{$type} $" : '$') . $parameter->getName();
                }
                $params = count($params) > 0 ? join(', ', $params) : '';
                echo '<div style="color:#666">' . nl2br($method->getDocComment() ?: '') . '</div>';
                echo "<div style='color:#00E'>{$object->getShortName()}::{$method->getName()}({$params})</div><br>";
            }
        } catch (\Exception $exception) {
            echo "<h3>[{$exception->getCode()}] {$exception->getMessage()}</h3>";
        }
    }
}