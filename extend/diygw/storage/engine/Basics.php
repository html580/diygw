<?php
declare (strict_types=1);

namespace diygw\storage\engine;

use diygw\traits\ErrorTrait;
use think\facade\Request;
use think\Exception;
use think\file\UploadedFile;


/**
 * 存储引擎抽象类
 * Class Basics
 * @package diygw\storage\drivers
 */
abstract class Basics
{
    use  ErrorTrait;

    // 当前存储引擎
    protected $storage;

    // 存储配置
    protected $config;

    // file对象句柄
    /* @var $file UploadedFile */
    protected $file;

    // 验证规则
    protected $validateRuleScene;

    // 磁盘配置
    protected $disk = 'public';

    // 保存的根文件夹名称
    protected $rootDirName;

    protected $isself = false;
    /**
     * 构造函数
     * Server constructor.
     * @param string $storage 存储方式
     * @param array|null $config 存储配置
     */
    public function __construct(string $storage, array $config = null)
    {
        $this->storage = $storage;
        $this->config = $config;
    }

    /**
     * 设置上传的文件信息 (外部用户上传)
     * @param string $name
     * @return $this
     * @throws \think\Exception
     */
    public function setUploadFile(string $name): Basics
    {
        // 接收上传的文件
        try {
            $this->file = Request::file($name);
        } catch (Exception $e) {
            $this->throwFileError($e);
        }
        if (empty($this->file)) {
            throw new Exception('未找到上传文件的信息');
        }
        return $this;
    }

    public function setIssef(bool $isself){
        $this->isself = $isself;
        return $this;
    }
    /**
     * 文件异常处理
     * @param Exception $e
     * @throws \think\Exception
     */
    private function throwFileError(Exception $e)
    {
        $maxSize = ini_get('upload_max_filesize');
        $myMsg = $e->getCode() === 1 ? "上传的文件超出了服务器最大限制: {$maxSize}；可修改php.ini文件中upload_max_filesize项调整" : false;
       
        throw new Exception($myMsg ?: $e->getMessage());
    }

    /**
     * 设置上传的文件信息 (系统内部上传)
     * @param string $filePath 文件路径
     * @return $this
     * @throws \think\Exception
     */
    public function setUploadFileByReal(string $filePath): Basics
    {
        // 接收上传的文件
        $this->file = new UploadedFile($filePath, basename($filePath));
        if (empty($this->file)) {
            throw new Exception('未找到上传文件的信息');
        }
        return $this;
    }

    /**
     * 设置上传文件的验证规则
     * @param string $scene
     * @return Basics
     */
    public function setValidationScene(string $scene = ''): Basics
    {
        $this->validateRuleScene = $scene;
        return $this;
    }

    /**
     * 设置磁盘配置
     * @param string $disk
     * @return $this
     */
    public function setDisk(string $disk): Basics
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * 设置上传的文件根目录名称
     * [通常是商城的id, 例如: 10001]
     * @param string $name
     * @return $this
     */
    public function setRootDirName(string $name): Basics
    {
        $this->rootDirName = $name;
        return $this;
    }

    /**
     * 文件上传
     * @return mixed
     */
    abstract protected function upload();

    /**
     * 文件上传
     * @return mixed
     */
    abstract protected function getUrl(string $filePath);

    public function getExistUrl($filePath){
        return $this->config['domain'].str_replace(str_replace('\\', '/', root_path('public').'storage'),  '',str_replace('\\', '/', $filePath));
    }
    /**
     * 文件删除
     * @param string $filePath
     * @return mixed
     */
    abstract protected function delete(string $filePath);

    /**
     * 临时文件的绝对路径
     * @return mixed
     */
    protected function getRealPath(): string
    {
        return $this->file->getRealPath();
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 生成保存的文件信息
     * @return array
     */
    public function getSaveFileInfo(): array
    {
        // 自动生成的文件名称
        //$hashName = $this->file->hashName(null);
        $hashName = $this->hashName();
        // 存储目录
        if($this->isself){
            $filePath = $this->getFilePath($hashName);
        }else{
            $filePath = $this->getFilePath(date('Ymd') . DIRECTORY_SEPARATOR .$hashName);
        }
        // 全路径
        $url = $this->getUrl($filePath);
        // 文件名称
        // 去除扩展名的写法 stristr($this->file->getOriginalName(), '.', true)
        $fileName = $this->file->getOriginalName();
        // 文件扩展名
        $fileExt = strtolower($this->file->extension());

        return [
            'hash_name' => $hashName,
            'storage' => $this->storage,                 // 存储方式
            'domain' => $this->config['domain'] ?? '',   // 存储域名
            'file_path' => $filePath,                    // 文件路径
            'file_name' => $fileName,                    // 文件名称
            'file_size' => $this->file->getSize(),       // 文件大小(字节)
            'file_ext' => $fileExt,                      // 文件扩展名
            'size' => $this->file->getSize(),
            'type' => $this->validateRuleScene,
            'ext' => $fileExt,
            'md5' => $this->file->hash('sha1'),
            'parent_id' => app()->request->param('parentId'),
            'name' => $fileName,
            'driver'  => $this->storage,
            'path' => $filePath,
            'url' => $url
        ];
    }

    /**
     * 自动生成文件名
     * @return string
     */
    private function hashName(): string
    {
        if($this->isself){
            return $this->file->getOriginalName();
        }else{
            $fileExt = strtolower($this->file->extension());
            return $this->file->hash('sha1').".".$fileExt;
        }
//        return $this->file->hashName(function () {
//            return date('Ymd') . DIRECTORY_SEPARATOR . md5(uniqid((string)mt_rand(), true));
//        });
    }

    /**
     * 获取hashName的路径
     * @param string $hashName
     * @return string
     */
    private function getFilePath(string $hashName): string
    {
        $filePath = empty($this->rootDirName) ? "{$hashName}" : "{$this->rootDirName}/{$hashName}";
        return convert_left_slash($filePath);
    }

    /**
     * 获取hashName的文件名
     * @param string $filePath
     * @return string
     */
    protected function getFileHashName(string $filePath): string
    {
        return basename($filePath);
    }

    /**
     * 获取hashName的文件目录
     * @param string $filePath
     * @return string
     */
    protected function getFileHashRoute(string $filePath): string
    {
        return dirname($filePath);
    }
}
