<?php
declare(strict_types=1);

namespace diygw;

use app\sys\model\StorageModel;
use diygw\exceptions\FailedException;
use think\exception\ValidateException;
use think\facade\Filesystem;
use think\file\UploadedFile;
use think\Url;

class DiygwUpload
{
    /**
     * 阿里云
     */
    public const OSS = 'oss';

    /**
     * 腾讯云
     */
    public const QCLOUD = 'qcloud';

    /**
     * 七牛
     */
    public const QIQNIU = 'qiniu';

    /**
     * 驱动
     *
     * @var string
     */
    protected $driver;

    /**
     * 本地
     */
    public const LOCAL = 'local';

    /**
     * path
     *
     * @var string
     */
    protected $path = '';

    protected $type = 'image';

    /**
     * upload files
     *
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        try {

            $path = Filesystem::disk($this->getDriver())->putFile($this->getPath(), $file);

            if ($path) {

                $url = self::getCloudDomain($this->getDriver()) .'/'. $this->getLocalPath($path);

                return [
                    'size' => $file->getSize(),
                    'type' => $this->type,
                    'ext' => $file->getOriginalExtension(),
                    'md5' => $file->hash('sha1'),
                    'parent_id' => app()->request->param('parentId'),
                    'name' => $file->getOriginalName(),
                    'driver'  => $this->getDriver(),
                    'path' => $path,
                    'url' => $url,
                    'driver' => $this->getDriver()
                ];
            }

            throw new FailedException('Upload Failed, Try Again!');

        } catch (\Exception $exception) {
            throw new FailedException($exception->getMessage());
        }
    }

    /**
     * 上传到 Local
     * @param $file
     * @return string
     */
    public function toLocal($file): string
    {
        $path = Filesystem::disk(self::LOCAL)->putFile($this->getPath(), $file);

        return public_path() . $this->getLocalPath($path);
    }


    /**
     * 本地路径
     * @param $path
     * @return string
     */
    protected function getLocalPath($path): string
    {
        if ($this->getDriver() === self::LOCAL) {

            $path = str_replace(str_replace('\\', '/', root_path('public')),  '',str_replace('\\', '/',  \config('filesystem.disks.local.root'))) . DIRECTORY_SEPARATOR .$path;

            return str_replace('\\', '/', $path);
        }

        return $path;
    }

    /**
     * 多文件上传
     * @param $attachments
     * @return array|string
     */
    public function multiUpload($attachments)
    {
        $paths = [];
        if (!is_array($attachments)) {
            $paths[] = $this->upload($attachments);
            return $paths;
        }

        foreach ($attachments as $attachment) {
            $paths[] = $this->upload($attachment);
        }

        return $paths;
    }

    /**
     * get upload driver
     * @return string
     */
    protected function getDriver(): string
    {
        if ($this->driver) {
            return $this->driver;
        }

        return \config('filesystem.default');
    }

    /**
     * set driver
     * @param $driver
     * @throws \Exception
     * @return $this
     */
    public function setDriver($driver): self
    {
        if (!in_array($driver, [self::OSS, self::QCLOUD, self::QIQNIU, self::LOCAL])) {
            throw new \Exception(sprintf('Upload Driver [%s] Not Supported', $driver));
        }

        $this->driver = $driver;

        return $this;
    }

    /**
     * @time 2020/1/25
     * @return string
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * @time 2020/1/25
     * @param string $path
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }


    /**
     * 验证图片
     * @time 2020/2/1
     * @param array $images
     * @return $this
     */
    public function checkImages(array $images)
    {
        try {
            validate(['image' => config('filesystem.disks.upload.image')])->check($images);
        } catch (ValidateException $e) {
            throw new FailedException($e->getMessage());
        }
        $this->type = 'image';
        return $this;
    }

    /**
     * 验证文件
     * @time 2020/2/1
     * @param array $files
     * @return $this
     */
    public function checkFiles($type,array $files)
    {
        try {
            //如果验证类型为空，表示图片默认图片类型
            if(empty($type)){
                $type = 'image';
            }
            $checkTypes = config('filesystem.disks.upload.'.$type);
            if(empty($checkTypes)){
                $checkTypes = config('filesystem.disks.upload.'.$type);
            }
            validate(['file' => $checkTypes])->check($files);
        } catch (ValidateException $e) {
            throw new FailedException($e->getMessage());
        }
        $this->type = $type;
        return $this;
    }

    /**
     * 获取云存储的域名
     * @param $driver
     * @return string
     */
    public static function getCloudDomain($driver): ?string
    {
        $driver = \config('filesystem.disks.' . $driver);

        switch ($driver['type']) {
            case DiygwUpload::QIQNIU:
                return  $driver['domain'];
            case DiygwUpload::LOCAL:
                return app()->request->domain();
            case DiygwUpload::OSS:
                return self::getOssDomain();
            case DiygwUpload::QCLOUD:
                return $driver['cdn'];
            default:
                throw new FailedException(sprintf('Driver [%s] Not Supported.', $driver));
        }
    }

    /**
     * 获取 OSS Domain
     * @return mixed|string
     */
    protected static function getOssDomain(): string
    {
        $oss = \config('filesystem.disks.oss');
        if ($oss['is_cname'] === false) {
            return 'https://' . $oss['bucket'] . '.' . $oss['end_point'];
        }
        return $oss['end_point'];
    }
}