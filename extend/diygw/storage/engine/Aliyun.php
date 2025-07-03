<?php
declare (strict_types = 1);

namespace diygw\storage\engine;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 阿里云存储引擎 (OSS)
 * Class Aliyun
 * @package diygw\storage\engine
 */
class Aliyun extends Basics
{
    public function getIsCname(){
        $isCName = false;
        if(isset($this->config['cname'])&&!empty($this->config['cname'])&&$this->config['cname']!='http://'&&$this->config['cname']!='https://'){
            $isCName = true;
        }
        return $isCName;
    }
    /**
     * 执行上传
     * @return bool
     */
    public function upload(): bool
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],true);
            $ossClient->uploadFile(
                $this->config['bucket'],
                $this->getSaveFileInfo()['file_path'],
                $this->getRealPath()
            );
        } catch (OssException $e) {
            $this->error = $e->getMessage();
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
        try {
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],$this->getIsCname()
            );
            $ossClient->deleteObject($this->config['bucket'], $filePath);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    protected function getUrl(string $filePath)
    {
        return $this->config['domain'] .'/'.  str_replace('\\', '/', $filePath);
    }

}
