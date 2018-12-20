Diygw for PHP
--

* Diygw For PHP 是DIY官网打造基于Thinkphp 5.1.x开发实时同步DIY官网设计应用，无需要下载直接在线同步应用更新应用；
* 基于浏览器的集成开发环境，可视化和智能化的设计，能轻松完成身和面向手机的移动应用开发；
* 无须编程 零代码基础 所见即所得设计工具；
* 轻松制作微信小程序、原型设计、WebApp设计、Bootstrap、单页动画
* 在线可视化制作小程序组件及在线可视化设计小程序数据源能力
* 无须编程轻易制作个性化移动WEBAPP界面,支持一键同步设计代码
* 设计完成同步至本地后，可以脱离DIY官网独立运行
* 基于ThinkAdmin改造支持多公众号管理


系统安装
--
* 项目安装及二次开发请参考hinkPHP官方文档及下面的服务环境说明。
>* 当前版本使用ThinkPHP5.1.x版本，对PHP版本要求不低于php5.6，具体请查阅ThinkPHP官方文档。
>* 如果需要再次安装删除./config/install.lock，./config/database.php,./application/middleware.php,./application/tags.php


Documentation
--
认真看看文档可能会对你的开发有所帮助哦！


开发技术交流（QQ群 513350915）

[![PHP微信开发群 (SDK)](http://pub.idqqimg.com/wpa/images/group.png)](http://shang.qq.com/wpa/qunwpa?idkey=ae25cf789dafbef62e50a980ffc31242f150bc61a61164458216dd98c411832a) 


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
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```

* Nginx

```
server {
	listen 80;
	server_name wealth.demo.cuci.cc;
	root /home/wwwroot/Diygw;
	index index.php index.html index.htm;
	
	add_header X-Powered-Host $hostname;
	fastcgi_hide_header X-Powered-By;
	
	if (!-e $request_filename) {
		rewrite  ^/(.+?\.php)/?(.*)$  /$1/$2  last;
		rewrite  ^/(.*)$  /index.php/$1  last;
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

#### 微信捐赠（收款人：html580网站-邓志锋付钱）
[![微信小程序商城解决方案](http://static.html580.com/assets/images/weixin-pay.gif "微信小程序商城解决方案")](http://www.diygw.com "微信小程序商城解决方案")


DiyGw官方技术交流群 [482112340](https://jq.qq.com/?_wv=1027&k=48Dm8gg)

如果您对DiyGw有任何建议、想法、评论或发现了bug，请联系我们280160522@qq.com。