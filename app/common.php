<?php
// 应用公共文件
use diygw\FileUtil;
use diygw\storage\Driver as StorageDriver;
use itbdw\Ip\IpLocation;
use think\facade\Log;
use diygw\sms\SmsCaptcha;

/**
 *
 * @time 2022年03月12日
 * @param $agent
 * @return string
 */
function getOs($agent): string
{
    if (false !== stripos($agent, 'win') && preg_match('/nt 6.1/i', $agent)) {
        return 'Windows 7';
    }
    if (false !== stripos($agent, 'win') && preg_match('/nt 6.2/i', $agent)) {
        return 'Windows 8';
    }
    if(false !== stripos($agent, 'win') && preg_match('/nt 10.0/i', $agent)) {
        return 'Windows 10';#添加win10判断
    }
    if (false !== stripos($agent, 'win') && preg_match('/nt 5.1/i', $agent)) {
        return 'Windows XP';
    }
    if (false !== stripos($agent, 'linux')) {
        return 'Linux';
    }
    if (false !== stripos($agent, 'mac')) {
        return 'mac';
    }
    return $agent;
}

/**
 *
 * @time 2022年03月12日
 * @param $agent
 * @return string
 */
function getBrowser($agent): string
{
    if (false !== stripos($agent, "MSIE")) {
        return 'MSIE';
    }
    if (false !== stripos($agent, "Firefox")) {
        return 'Firefox';
    }
    if (false !== stripos($agent, "Chrome")) {
        return 'Chrome';
    }
    if (false !== stripos($agent, "Safari")) {
        return 'Safari';
    }
    if (false !== stripos($agent, "Opera")) {
        return 'Opera';
    }
    return $agent;
}

function getIpLocation($ip){
    $location  = IpLocation::getLocation($ip);
    return $location['area']?$location['area']:'未知';
}



/*
    参数：
    $sql_path:sql文件路径；
    $old_prefix:原表前缀；
    $new_prefix:新表前缀；
    $separator:分隔符 参数可为";\n"或";\r\n"或";\r"
*/
function get_mysql_data($sql_path, $old_prefix = "", $new_prefix = "", $separator = ";\n")
{

    $commenter = array('#', '--');
    //判断文件是否存在
    if (!file_exists($sql_path))
        return false;

    $content = file_get_contents($sql_path);   //读取sql文件
    $content = str_replace(array($old_prefix, "\r"), array($new_prefix, "\n"), $content);//替换前缀

    //通过sql语法的语句分割符进行分割
    $segment = explode($separator, trim($content));

    //去掉注释和多余的空行
    $data = array();
    foreach ($segment as $statement) {
        $sentence = explode("\n", $statement);
        $newStatement = array();
        foreach ($sentence as $subSentence) {
            if ('' != trim($subSentence)) {
                //判断是会否是注释
                $isComment = false;
                foreach ($commenter as $comer) {
                    if (preg_match("/^(" . $comer . ")/is", trim($subSentence))) {
                        $isComment = true;
                        break;
                    }
                }
                //如果不是注释，则认为是sql语句
                if (!$isComment)
                    $newStatement[] = $subSentence;
            }
        }
        $data[] = $newStatement;
    }

    //组合sql语句
    foreach ($data as $statement) {
        $newStmt = '';
        foreach ($statement as $sentence) {
            $newStmt = $newStmt . trim($sentence) . "\n";
        }
        if (!empty($newStmt)) {
            $result[] = $newStmt;
        }
    }
    return $result;
}

/**
 * 文本左斜杠转换为右斜杠
 * @param string $string
 * @return mixed
 */
function convert_left_slash(string $string)
{
    return str_replace('\\', '/', $string);
}

/**
 * 生成订单号
 */
function getOrderNo()
{
    return date('YmdHis') . rand(10000000, 99999999);
}

function getFirstLetter($str) {
    $pos = strpos($str, '_');
    if ($pos === false) {
        return substr($str, 0, 1);
    } else {
        return substr($str, 0, $pos);
    }
}




function getAfterFirstUnderscore($str) {
    $pos = strpos($str, '_');
    if ($pos === false) {
        return "";  // 如果没有下划线，则返回空字符串或其他适当的值
    } else {
        return substr($str, $pos + 1);
    }
}

function getLocalPath($filePath)
{
    $path = str_replace(str_replace('\\', '/', root_path('public')),  '',str_replace('\\', '/',  $filePath)) ;
    return app()->request->domain() .'/'.  $path;
}

//把base64图片转化网络路径
function setBase64ToImg($base64_image_content){
    //匹配出图片的格式
    $preg = preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result);
    if ($preg){
        $type = $result[2];
        $new_file = str_replace('\\', '/',  \config('filesystem.disks.local.root'). DIRECTORY_SEPARATOR .'avatar'.DIRECTORY_SEPARATOR);
        if(!file_exists($new_file))
        {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            FileUtil::mk_dirs($new_file);
        }
        $new_file = $new_file.uniqid().".{$type}";
        $driver = \config('filesystem.default');
        //如果是本地存储
        if($driver=='default'&&file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
            return  $url = getLocalPath($new_file);;
        }else{
            $storage = new StorageDriver();
            $type = "image";
            // 设置上传文件的信息
            $storage->setUploadFileByReal($new_file)
                ->setRootDirName($type)
                ->setValidationScene($type);
            $data = $storage->getSaveFileInfo();
            if($storage->upload()){
                //删除原文件
                unlink($new_file);
                return  $data['url'];
            }else{
                return  $url = getLocalPath($new_file);;
            }
        }
    }else{
        return $base64_image_content;
    }
}


/**
 * 写入日志 (使用tp自带驱动记录到runtime目录中)
 * @param $value
 * @param string $type
 */
function log_record($value, string $type = 'info')
{
    $content = is_string($value) ? $value : print_r($value, true);
    Log::record($content, $type);
}



/*
 * 获取短信验证码
 */
function getSmsCode($phone){
    $code = (new SmsCaptcha())->mobile($phone)->create();
    return $code;
}

/*
 * 获取短信验证码
 */
function getCode($phone){
    $code = (new SmsCaptcha())->mobile($phone)->getCode();
    return $code;
}
/*
 * 验证短信验证码
 */
function checkSmsCode($phone,$code){
    return (new SmsCaptcha())->mobile($phone)->code($code)->check();
}

function getCurrentDateTime(){
    return date('Y-m-d H:i:s', time());
}