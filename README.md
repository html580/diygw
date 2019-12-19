Diygw for PHP
--

* Diygw For PHP 是DIY官网打造基于Thinkphp 5.1.x开发实时同步DIY官网设计应用，无需要下载直接在线同步应用更新应用；
* 基于浏览器的集成开发环境，可视化和智能化的设计，能轻松完成面向手机的移动应用开发；
* 无须编程 零代码基础 所见即所得设计工具；
* 轻松制作微信小程序、原型设计、WebApp设计、Bootstrap、单页动画
* 在线可视化制作小程序组件及在线可视化设计小程序数据源能力
* 无须编程轻易制作个性化移动WEBAPP界面,支持一键同步设计代码
* 设计完成同步至本地后，可以脱离DIY官网独立运行
* 基于ThinkAdmin改造支持多公众号管理

组件演示大全
----
![](http://lib.diygw.com/upload/1/image/20190109/15.jpg)  

系统安装
--
* 项目安装及二次开发请参考ThinkPHP官方文档及下面的服务环境说明。
>* 当前版本使用ThinkPHP5.1.x版本，对PHP版本要求不低于php5.6，具体请查阅ThinkPHP官方文档。
>* 如果需要再次安装删除./config/install.lock，./config/database.php,./application/middleware.php,./application/tags.php


Documentation
--
认真看看文档可能会对你的开发有所帮助哦！


开发技术交流（QQ群 217549678）

[![开发交流群](http://pub.idqqimg.com/wpa/images/group.png)](https://jq.qq.com/?_wv=1027&k=48D4Afw) 


Repositorie
--
Diygw为开源项目，允许把它用于任何地方，不受任何约束，欢迎 fork 项目。
* Gitee  托管地址：https://gitee.com/html580/diygw
* GitHub 托管地址：https://github.com/html580/diygw


Environment
---
>1. PHP 版本不低于 PHP5.6，推荐使用 PHP7 以达到最优效果；
>2. 需开启 PATHINFO，不再支持 ThinkPHP 的 URL 兼容模式运行（源于如何优雅的展示）。

* Apache

```xml
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

* Nginx

```
server {
	listen 80;
	server_name demo.diygw.com;
	root /home/wwwroot/Diygw;
	index index.php index.html index.htm;
	
	add_header X-Powered-Host $hostname;
	fastcgi_hide_header X-Powered-By;
	
	location / {  
        index  index.htm index.html index.php;  
        #访问路径的文件不存在则重写URL转交给ThinkPHP处理  
        if (!-e $request_filename) {  
           rewrite  ^/(.*)$  /index.php/$1  last;  
           break;  
        }  
    }  
	
	location ~ \.php($|/){
		fastcgi_index   index.php;
		fastcgi_pass    127.0.0.1:9000;
		include         fastcgi_params;
		set $real_script_name $fastcgi_script_name;
		if ($real_script_name ~ "^(.+?\.php)(/.+)$") {
			set $real_script_name $1;
		}
		fastcgi_split_path_info ^(.+?\.php)(/.*)$;
		fastcgi_param   PATH_INFO               $fastcgi_path_info;
		fastcgi_param   SCRIPT_NAME             $real_script_name;
		fastcgi_param   SCRIPT_FILENAME         $document_root$real_script_name;
		fastcgi_param   PHP_VALUE               open_basedir=$document_root:/tmp/:/proc/;
		access_log      /home/wwwlog/domain_access.log    access;
		error_log       /home/wwwlog/domain_error.log     error;
	}
	
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
		access_log  off;
		error_log   off;
		expires     30d;
	}
	
	location ~ .*\.(js|css)?$ {
		access_log   off;
		error_log    off;
		expires      12h;
	}
}
```

Copyright
--
* Diygw 基于`MIT`协议发布，任何人可以用在任何地方，不受约束
* Diygw 部分代码来自互联网，若有异议，可以联系作者进行删除

### 分享精神

非常感谢您的支持！如果您喜欢DiyGw，请将它介绍给自己的朋友，或者帮助他人安装一个DiyGw，又或者写一篇赞扬我们的文章。DiyGw是对ThinkPHP的传承和新的传奇。由DiyGw 开发团队完成开发。如果您愿意支持我们的工作，欢迎您对DiyGw进行捐赠。
#### 支付宝捐赠（收款人：luckyzf@126.com）
[![微信小程序商城解决方案](http://static.html580.com/assets/images/alipay.gif "微信小程序商城解决方案")](http://www.diygw.com "微信小程序商城解决方案")



DiyGw官方技术交流群 [482112340](https://jq.qq.com/?_wv=1027&k=48Dm8gg)

如果您对DiyGw有任何建议、想法、评论或发现了bug，请联系我们280160522@qq.com。


### [点击查看在线视频教程](https://v.qq.com/x/page/r0518gfnx33.html)  

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin1.png)
 


系统安装
----

浏览器端输入你域名，如果系统没有安装，会自动转到安装地址。我已我自己的域名为例。

输入域名https://xcx.diygw.com/。系统自动进入安装页面。

### 第一步：点击同意安装协议

 ![](http://lib.diygw.com/upload/1/image/20181222/9.png)

### 第二步：环境检测

大家检测下自己的环境，如果环境提供有误，请修复对应的错误

 ![](http://lib.diygw.com/upload/1/image/20181222/10.png)

### 第二步：创建数据库

大家根据自己的数据库来配置，建议独立数据库。输入创始人的账号信息，记得要保存好自己的用户名密码信息哟。

![](http://lib.diygw.com/upload/1/image/20181222/11.png)  

![](http://lib.diygw.com/upload/1/image/20181222/12.png)  

### 安装完后登录后台应用，可以对公众号进行管理

![](http://lib.diygw.com/upload/1/image/20181222/3.png)  

制作应用
----

第一步：点击新建微信小程序/WEBAPP
--------------------

点击后弹出窗口，输入你的应用名称。下面以首页我的例子我的电商首页展示为例。

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin7.png)  

首页主要包括：图片切换，分类导航，内容展示，底部导航

大家想到这里就会想到数据来源呢，对的。那我们就进入后台数据源管理

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin8.png)  

进入后我们第一步想到的是有图片切换，分类，详情三个表单。

数据源管理
-----

### 图片切换表单

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin9.png)  

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin10.png)

### 详情表单

其他表单可能都差不多，其中详情表单可能会稍有不同，他有可能会涉及到分类。

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin11.png)

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin12.png)  

大家每做完一个表单都可以在线预览然后增加几条数据，用于前台展示。

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin14.png)

新增表单有了大家肯定想到了我怎么数据管理呢？现在教大家怎么进行数据管理。

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin15.png)

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin16.png)

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin17.png)

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin18.png)

大家可能在设计的过程如果没注意表单的设置，显示的是表单。

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin19.png)

数据管理的时候，大家可能要编辑数据，对表格有个细节需要注意，在编辑页面选项值里选择对应的表单。

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin20.png)

至此后台数据源完成，我们回到前台的设计。

手机端设计
-----

### 图片切换设计

拖拉滑块组件进入设计区，设置数据源，进行数据源管理

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin21.png)

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin22.png)  

  

### 分类展示

拖拉九宫组件进入设计区，设置数据源及字段映射。字段映射用于分类详情展示，跳转到分类时对过滤出对应的数据。

 ![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin23.png)

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin24.png)  

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin25.png)  

  

### 内容展示

内容展示跟图片切换一样，只需要拖拉对应的位置即可。

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin26.png)  

一键同步应用,回到刚才安装的系统里

  

![](http://lib.diygw.com/upload/1/image/20181222/1.png)  

  

应用效果图如下

![](http://lib.diygw.com/upload/1/image/20181222/7.jpg)  

![](http://lib.diygw.com/upload/1/image/20181222/6.jpg)  

![](http://lib.diygw.com/upload/1/image/20181222/5.jpg)  

![](http://lib.diygw.com/upload/1/image/20181222/4.jpg)  

![](http://lib.diygw.com/upload/1/image/20181222/8.jpg)  

![](http://lib.diygw.com/upload/1/image/20170804/diygw-admin31.png)

![](http://lib.diygw.com/upload/1/image/20190109/1.png)  

![](http://lib.diygw.com/upload/1/image/20190109/2.png)  

![](http://lib.diygw.com/upload/1/image/20190109/3.png)  

![](http://lib.diygw.com/upload/1/image/20190109/4.png)  

![](http://lib.diygw.com/upload/1/image/20190109/5.png)  

![](http://lib.diygw.com/upload/1/image/20190109/6.png)  

![](http://lib.diygw.com/upload/1/image/20190109/7.png)  

![](http://lib.diygw.com/upload/1/image/20190109/8.png)  

![](http://lib.diygw.com/upload/1/image/20190109/9.png)  

![](http://lib.diygw.com/upload/1/image/20190109/10.png) 

![](http://lib.diygw.com/upload/1/image/20190109/11.png)  

![](http://lib.diygw.com/upload/1/image/20190109/12.png)  

![](http://lib.diygw.com/upload/1/image/20190109/13.png)  

![](http://lib.diygw.com/upload/1/image/20190109/14.png)  
