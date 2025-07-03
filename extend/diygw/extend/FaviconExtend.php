<?php

namespace diygw\extend;

use diygw\exceptions\Exception;

/**
 * 网站 ICO 文件生成工具
 * Class FaviconExtend
 * @package diygw\extend
 */
class FaviconExtend
{
    /**
     * 转换后的 BMP 图像
     * @var array
     */
    private $images = [];

    /**
     * Constructor - 创建一个新的 ICO 生成器
     * @param ?string $file 源图像文件的路径
     * @param array $size 图片文件尺寸 [W1,H1]
     * @throws \diygw\exceptions\Exception
     */
    function __construct(?string $file = null, array $size = [])
    {
        $functions = [
            'imagesx',
            'imagesy',
            'getimagesize',
            'imagesavealpha',
            'imagecreatefromstring',
            'imagecreatetruecolor',
            'imagecolortransparent',
            'imagecolorallocatealpha',
            'imagecopyresampled',
            'imagealphablending',
        ];

        foreach ($functions as $function) if (!function_exists($function)) {
            throw new Exception(sprintf('Required %s function not found.', $function));
        }

        if (is_string($file)) {
            $this->addImage($file, $size);
        }
    }

    /**
     * 添加图像到生成器中
     *
     * @param string $file 图像文件路径
     * @param array $size 图像文件尺寸
     * @throws \diygw\exceptions\Exception
     */
    public function addImage(string $file, array $size = []): FaviconExtend
    {
        if (false === ($im = $this->loadImageFile($file))) {
            throw new Exception('Read picture file Failed.');
        }
        if (empty($size)) {
            $size = [imagesx($im), imagesy($im)];
        }

        [$width, $height] = $size;
        $image = imagecreatetruecolor($width, $height);
        imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagealphablending($image, false);
        imagesavealpha($image, true);

        [$sourceWidth, $sourceHeight] = [imagesx($im), imagesy($im)];
        if (false === imagecopyresampled($image, $im, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight)) {
            throw new Exception('Parse and process picture Failed.');
        }
        $this->addImageData($image);
        return $this;
    }

    /**
     * 将 ICO 内容写入到文件
     *
     * @param string $file 写入文件路径
     * @return boolean
     */
    public function saveIco(string $file): bool
    {
        if (false === ($data = $this->getIcoData())) {
            return false;
        }
        if (false === ($fh = fopen($file, 'w'))) {
            return false;
        }
        if (false === (fwrite($fh, $data))) {
            fclose($fh);
            return false;
        }
        fclose($fh);
        return true;
    }

    /**
     * 生成并获取 ICO 图像数据
     */
    private function getIcoData()
    {
        if (!is_array($this->images) || empty($this->images)) {
            return false;
        }
        [$pixelData, $entrySize] = ['', 16];
        $data = pack('vvv', 0, 1, count($this->images));
        $offset = 6 + ($entrySize * count($this->images));
        foreach ($this->images as $image) {
            $data .= pack('CCCCvvVV', $image['width'], $image['height'], $image['colors'], 0, 1, $image['pixel'], $image['size'], $offset);
            $pixelData .= $image['data'];
            $offset += $image['size'];
        }
        $data .= $pixelData;
        unset($pixelData);
        return $data;
    }

    /**
     * 将 GD 图像转为 BMP 格式
     */
    private function addImageData($im)
    {
        [$width, $height] = [imagesx($im), imagesy($im)];
        [$pixelData, $opacityData, $opacityValue] = [[], [], 0];
        for ($y = $height - 1; $y >= 0; $y--) {
            for ($x = 0; $x < $width; $x++) {
                $color = imagecolorat($im, $x, $y);
                $alpha = ($color & 0x7F000000) >> 24;
                $alpha = (1 - ($alpha / 127)) * 255;
                $color &= 0xFFFFFF;
                $color |= 0xFF000000 & ($alpha << 24);
                $pixelData[] = $color;
                $opacity = ($alpha <= 127) ? 1 : 0;
                $opacityValue = ($opacityValue << 1) | $opacity;
                if ((($x + 1) % 32) == 0) {
                    $opacityData[] = $opacityValue;
                    $opacityValue = 0;
                }
            }
            if (($x % 32) > 0) {
                while (($x++ % 32) > 0) {
                    $opacityValue = $opacityValue << 1;
                }
                $opacityData[] = $opacityValue;
                $opacityValue = 0;
            }
        }

        $imageHeaderSize = 40;
        $colorMaskSize = $width * $height * 4;
        $opacityMaskSize = (ceil($width / 32) * 4) * $height;
        $data = pack('VVVvvVVVVVV', 40, $width, ($height * 2), 1, 32, 0, 0, 0, 0, 0, 0);
        foreach ($pixelData as $color) $data .= pack('V', $color);
        foreach ($opacityData as $opacity) $data .= pack('N', $opacity);

        $this->images[] = [
            'data'  => $data,
            'size'  => $imageHeaderSize + $colorMaskSize + $opacityMaskSize,
            'width' => $width, 'height' => $height,
            'pixel' => 32, 'colors' => 0,
        ];
    }

    /**
     * 读取图片资源
     * @param string $file 文件路径
     * @return false|\GdImage|resource
     */
    private function loadImageFile(string $file)
    {
        if (false === getimagesize($file)) {
            return false;
        }
        if (false === ($data = file_get_contents($file))) {
            return false;
        }
        if (false === ($image = imagecreatefromstring($data))) {
            return false;
        }
        unset($data);
        return $image;
    }
}