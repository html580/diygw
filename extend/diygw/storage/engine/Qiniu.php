<?php
declare (strict_types=1);

namespace diygw\storage\engine;

use Qiniu\Auth;
use Qiniu\Http\Error;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use function Qcloud\Cos\endWith;

/**
 * 七牛云存储引擎
 * Class Qiniu
 * @package diygw\storage\engine
 */
class Qiniu extends Basics
{
    /**
     * 执行上传
     * @return bool|mixed
     * @throws \Exception
     */
    public function upload()
    {
        // 要上传图片的本地路径
        $realPath = $this->getRealPath();
        // 构建鉴权对象
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        // 要上传的空间
        $token = $auth->uploadToken($this->config['bucket']);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list(, $error) = $uploadMgr->putFile($token, $this->getSaveFileInfo()['file_path'], $realPath);
        /* @var $error Error */
        if ($error !== null) {
            $this->error = $error->message();
            return false;
        }
        return true;
    }

    /**
     * 删除文件
     * @param string $filePath
     * @return bool
     */
    public function delete(string $filePath): bool
    {
        // 构建鉴权对象
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        // 初始化 UploadManager 对象并进行文件的上传
        $bucketMgr = new BucketManager($auth);
        /* @var $error Error */
        list(, $error) = $bucketMgr->delete($this->config['bucket'], $filePath);
        if ($error !== null) {
            $this->error = $error->message();
            return false;
        }
        return true;
    }

    protected function getUrl(string $filePath)
    {
        return $this->config['domain'] .'/'.  str_replace('\\', '/', $filePath);
    }

}
