<?php
namespace diygw;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use think\addons\AddonException;
use think\Exception;

class HttpUitl{

    /**
     * 获取远程服务器
     * @return  string
     */
    protected static function getServerUrl()
    {
        return config('app.diygw.api_url');
    }

    /**
     * 获取请求对象
     * @return Client
     */
    public static function getClient()
    {
        $options = [
            'base_uri'        => self::getServerUrl(),
            'timeout'         => 30,
            'connect_timeout' => 30,
            'verify'          => false,
            'http_errors'     => false,
            'headers'         => [
                'X-REQUESTED-WITH' => 'XMLHttpRequest',
                'Referer'          => request()->host(),
                'User-Agent'       => 'diygw',
            ]
        ];
        static $client;
        if (empty($client)) {
            $client = new Client($options);
        }
        return $client;
    }
    /**
     * 发送请求
     * @return array
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function sendRequest($url, $params = [], $method = 'POST',$type='json')
    {
        try {
            $client = self::getClient();
            $options = strtoupper($method) == 'POST' ? ['form_params' => $params] : ['query' => $params];
            $response = $client->request($method, $url, $options);
            $body = $response->getBody();
            $content = $body->getContents();
            if($type=='json'){
                $json = (array)json_decode($content, true);
                return $json;
            }else{
                return $content;
            }
        } catch (TransferException $e) {
            throw new Exception(config('app_debug') ? $e->getMessage() : __('Network error'));
        } catch (\Exception $e) {
            throw new Exception(config('app_debug') ? $e->getMessage() : __('Unknown data format'));
        }

    }

    /**
     * 发送请求
     * @return array
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function downRequest($url, $params = [], $method = 'POST',$saveFilePath)
    {
        try {
            $client = self::getClient();
            $options = strtoupper($method) == 'POST' ? ['form_params' => $params] : ['query' => $params];
            $response = $client->request($method, $url, $options);
            $body = $response->getBody();
            $content = $body->getContents();
            if (substr($content, 0, 1) === '{') {
                $json = (array)json_decode($content, true);
                return $json;
            }
            if ($write = fopen($saveFilePath, 'w')) {
                fwrite($write, $content);
                fclose($write);
                return true;
            }
        } catch (TransferException $e) {
            throw new Exception(config('app_debug') ? $e->getMessage() : __('Network error'));
        } catch (\Exception $e) {
            throw new Exception(config('app_debug') ? $e->getMessage() : __('Unknown data format'));
        }

    }

    /**
     * 发送请求
     * @return array
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function downZip($url,$saveFilePath)
    {
        $json = [];
        try {
            $client = self::getClient();
            $saveFile= fopen($saveFilePath, 'w+');
            $options = [RequestOptions::SINK=>$saveFile];
            $response = $client->request("GET", $url, $options);
            if ($response->getStatusCode() === 200) {
                return true;
            }else{
                return false;
            }
        } catch (TransferException $e) {
            throw new Exception(config('app_debug') ? $e->getMessage() : __('Network error'));
        } catch (\Exception $e) {
            throw new Exception(config('app_debug') ? $e->getMessage() : __('Unknown data format'));
        }

    }
}