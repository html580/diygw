<?php

declare (strict_types=1);

namespace diygw\extend;

use diygw\exceptions\Exception;

/**
 * JsonRpc 客户端
 * Class JsonRpcClient
 * @package diygw\extend
 */
class JsonRpcClient
{
    /**
     * 请求ID
     * @var integer
     */
    private $id;

    /**
     * 服务端地址
     * @var string
     */
    private $proxy;

    /**
     * JsonRpcClient constructor.
     * @param string $proxy
     */
    public function __construct(string $proxy)
    {
        $this->id = time();
        $this->proxy = $proxy;
    }

    /**
     * 执行 JsonRpc 请求
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $params = [])
    {
        $options = [
            'ssl'  => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode([
                    'jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => $this->id,
                ], JSON_UNESCAPED_UNICODE),
            ],
        ];
        // Performs the HTTP POST
        if ($fp = fopen($this->proxy, 'r', false, stream_context_create($options))) {
            $response = '';
            while ($line = fgets($fp)) $response .= trim($line) . "\n";
            [, $response] = [fclose($fp), json_decode($response, true)];
        } else {
            throw new Exception("无法连接到 {$this->proxy}");
        }
        // Final checks and return
        if ($response['id'] != $this->id) {
            throw new Exception("错误标记 (请求标记: {$this->id}, 响应标记: {$response['id']}）");
        }
        if (is_null($response['error'])) {
            return $response['result'];
        } else {
            throw new Exception($response['error']['message'], $response['error']['code'], $response['result']);
        }
    }
}