<?php
declare (strict_types = 1);

namespace diygw\storage;
use diygw\storage\engine\Aliyun;
use diygw\storage\engine\Local;
use diygw\storage\engine\Qcloud;
use diygw\storage\engine\Qiniu;
use diygw\storage\enum\StorageEnum;
use think\Exception;

/**
 * 存储模块驱动
 * Class driver
 * @package diygw\storage
 */
class Driver
{
    private $config;    // upload 配置
    private $engine;    // 当前存储引擎类

    /**
     * 存储引擎类列表
     */
    const ENGINE_CLASS_LIST = [
        StorageEnum::LOCAL => Local::class,
        StorageEnum::QINIU => QINIU::class,
        StorageEnum::ALIYUN => Aliyun::class,
        StorageEnum::QCLOUD => Qcloud::class
    ];
    /**
     * 构造方法
     * Driver constructor.
     * @param $config
     * @param string|null $storage 指定存储方式，如不指定则为系统默认
     * @throws Exception
     */
    public function __construct($config=null, string $storage = null)
    {
        if(empty($config)){
            $driver = \config('filesystem.default');
            if(!empty($storage)){
                $driver = $storage;
            }
            $engine = \config('filesystem.disks.'.$driver);
            $config = [
                'default' => 'local',
                'engine' => [
                    'local' => [],
                    'qiniu' => [
                        'bucket' => '',
                        'access_key' => '',
                        'secret_key' => '',
                        'domain' => 'http://'
                    ],
                    'aliyun' => [
                        'bucket' => '',
                        'access_key_id' => '',
                        'access_key_secret' => '',
                        'domain' => 'http://'
                    ],
                    'qcloud' => [
                        'bucket' => '',
                        'region' => '',
                        'secret_id' => '',
                        'secret_key' => '',
                        'domain' => 'http://'
                    ],
                ]
            ];
            $config['default'] = $driver;
            $config['engine'][$driver] = $engine;
        }
        $this->config = $config;
        // 实例化当前存储引擎
        $this->engine = $this->getEngineClass($storage);
    }

    /**
     * 设置上传的文件信息
     * @param string $name
     * @return mixed
     */
    public function setUploadFile(string $name = 'iFile')
    {
        return $this->engine->setUploadFile($name);
    }

    /**
     * 设置上传的文件信息
     * @param string $filePath
     * @return mixed
     */
    public function setUploadFileByReal(string $filePath)
    {
        return $this->engine->setUploadFileByReal($filePath);
    }

    /**
     * 设置上传的文件信息
     * @param string $name
     * @return mixed
     */
    public function setRootName(string $name = '')
    {
        return $this->engine->setRootName($name);
    }

    /**
     * 设置上传文件的验证规则
     * @param array $rules
     * @return mixed
     */
    public function setValidationScene(array $rules = [])
    {
        return $this->engine->setValidationScene($rules);
    }

    /**
     * 执行文件上传
     */
    public function upload()
    {
        return $this->engine->upload();
    }

    /**
     * 执行文件删除
     * @param string $filePath
     * @return mixed
     */
    public function delete(string $filePath)
    {
        return $this->engine->delete($filePath);
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 返回保存的文件信息
     * @return mixed
     */
    public function getSaveFileInfo()
    {
        return $this->engine->getSaveFileInfo();
    }

    public  function getExistUrl($filePath){
        return $this->engine->getExistUrl($filePath);
    }
    /**
     * 获取当前的存储引擎
     */
    /**
     * 获取当前的存储引擎
     */
    private function getEngineClass($storage = null)
    {
        $storage = is_null($storage) ? $this->config['default'] : $storage;
        if (!isset(self::ENGINE_CLASS_LIST[$storage])) {
            throw new Exception("未找到存储引擎类: {$storage}");
        }
        $class = self::ENGINE_CLASS_LIST[$storage];
        return new $class($storage, $this->config['engine'][$storage]);
    }

}
