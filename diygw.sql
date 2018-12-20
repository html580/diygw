-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2018 �?12 �?20 �?23:12
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `diygw_wechat`
--

-- --------------------------------------------------------

--
-- 表的结构 `diygw_addons`
--

CREATE TABLE IF NOT EXISTS `diygw_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='插件表' AUTO_INCREMENT=22 ;

--
-- 转存表中的数据 `diygw_addons`
--

INSERT INTO `diygw_addons` (`id`, `name`, `title`, `description`, `status`, `config`, `author`, `version`, `create_time`, `has_adminlist`) VALUES
(19, 'systeminfo', '系统环境信息', '用于显示一些服务器的信息', 1, '{"title":"\\u7cfb\\u7edf\\u4fe1\\u606f","width":"2","display":"1"}', 'diygw', '0.1', 1543895835, 0),
(20, 'sitestat', '站点统计信息', '统计站点的基础信息', 0, '{"title":"\\u7cfb\\u7edf\\u4fe1\\u606f","width":"2","display":"1"}', 'diygw', '0.1', 1543896403, 0),
(21, 'devteam', '开发团队信息', '开发团队成员信息', 1, '{"title":"diygw\\u5f00\\u53d1\\u56e2\\u961f","width":"2","display":"1"}', 'diygw', '0.1', 1543896421, 0);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_address`
--

CREATE TABLE IF NOT EXISTS `diygw_app_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dashboard_id` int(10) NOT NULL COMMENT '应用ID',
  `mpid` int(10) DEFAULT NULL,
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `gender` varchar(10) DEFAULT '1' COMMENT '性别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '姓名',
  `tel` varchar(50) NOT NULL COMMENT '电话',
  `is_def` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认1：为默认',
  `address` varchar(500) NOT NULL DEFAULT '1.00' COMMENT '收货地址（不加省市区）',
  `address_xq` varchar(600) DEFAULT NULL COMMENT '省市区+详细地址',
  `sheng` int(10) DEFAULT NULL,
  `quyu` int(10) DEFAULT NULL,
  `city` int(10) DEFAULT NULL,
  `code` varchar(20) DEFAULT '1' COMMENT '状态1为有效，0为无效',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建人',
  `update_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_attribute`
--

CREATE TABLE IF NOT EXISTS `diygw_app_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) DEFAULT NULL,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `type` varchar(20) DEFAULT '' COMMENT '数据类型',
  `valids` text,
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `value` varchar(100) DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) DEFAULT '' COMMENT '备注',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `dashboard_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模型属性表' AUTO_INCREMENT=24117 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_cart`
--

CREATE TABLE IF NOT EXISTS `diygw_app_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dbid` int(10) NOT NULL,
  `dashboard_id` int(10) NOT NULL COMMENT '应用ID',
  `form_id` varchar(100) NOT NULL,
  `page_id` int(10) DEFAULT NULL,
  `page_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '页面标识',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `pkey` varchar(100) DEFAULT NULL,
  `link_id` varchar(200) NOT NULL COMMENT '关联商品ID',
  `link_title` varchar(1000) CHARACTER SET utf8 NOT NULL COMMENT '关联商品标题',
  `link_json` varchar(4000) CHARACTER SET utf8 DEFAULT NULL COMMENT '关联商品备注',
  `link_price` decimal(15,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '关联商品价格',
  `link_img` varchar(2000) CHARACTER SET utf8 DEFAULT NULL COMMENT '关联商品缩略图',
  `link_total` int(5) NOT NULL DEFAULT '1' COMMENT '购买商品数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1为有效，0为无效',
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_config`
--

CREATE TABLE IF NOT EXISTS `diygw_app_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT '配置名称',
  `value` text COMMENT '配置值',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=2804 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_dashboard`
--

CREATE TABLE IF NOT EXISTS `diygw_app_dashboard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `mpid` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL COMMENT '应用名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='应用' AUTO_INCREMENT=14089 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_dashboard_extend`
--

CREATE TABLE IF NOT EXISTS `diygw_app_dashboard_extend` (
  `id` varchar(30) NOT NULL,
  `label` varchar(100) DEFAULT NULL COMMENT '配置文本',
  `name` varchar(100) DEFAULT NULL COMMENT '配置名称',
  `value` text COMMENT '配置值',
  `scene_id` int(10) DEFAULT NULL COMMENT '场景ID',
  `dashboard_id` varchar(10) DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_dashboard_scene`
--

CREATE TABLE IF NOT EXISTS `diygw_app_dashboard_scene` (
  `id` varchar(30) NOT NULL,
  `title` varchar(500) DEFAULT NULL COMMENT '应用名称',
  `description` varchar(1000) DEFAULT NULL COMMENT '描述',
  `dashboard_id` int(10) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '最后修改时间',
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用场景秀';

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_menu`
--

CREATE TABLE IF NOT EXISTS `diygw_app_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_web` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否后台',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `dashboard_id` int(10) DEFAULT NULL,
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_model`
--

CREATE TABLE IF NOT EXISTS `diygw_app_model` (
  `id` varchar(30) NOT NULL COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `dashboard_id` int(10) DEFAULT NULL,
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文档模型表';

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_order`
--

CREATE TABLE IF NOT EXISTS `diygw_app_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mpid` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dashboard_id` int(10) NOT NULL COMMENT '应用ID',
  `cart_price` decimal(15,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '购物车总价',
  `pay_price` decimal(15,2) NOT NULL DEFAULT '1.00' COMMENT '实际支付总价',
  `pay_title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pay_detail` varchar(6000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付单号（微信、支付宝等）',
  `trade_no` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_time` datetime DEFAULT NULL,
  `order_pay_id` int(10) DEFAULT NULL,
  `pay_attach` varchar(127) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_ip` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_list` text CHARACTER SET utf8 NOT NULL COMMENT '购物车商品列表',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `create_time` datetime NOT NULL,
  `update_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '订单状态：0:已取消;1(默认):未付款;2:已付款;3:已发货;4:已收货;',
  `client_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '客户名称',
  `client_tel` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系电话',
  `client_gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系地址',
  `client_remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注(买家备注)',
  `way_type` int(11) DEFAULT '1' COMMENT '配送方式（1快递，2无物流）',
  `express_com` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '快递公司',
  `express_num` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '快递单号',
  `express_price` int(11) DEFAULT '0' COMMENT '快递费（单位：分）',
  `express_remark` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '快递备注',
  `express_time` datetime DEFAULT NULL COMMENT '快递时间',
  `finish_time` datetime DEFAULT NULL COMMENT '订单完成时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderid` (`order_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='订单信息' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_order_info`
--

CREATE TABLE IF NOT EXISTS `diygw_app_order_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) NOT NULL,
  `dbid` int(10) NOT NULL,
  `dashboard_id` int(10) NOT NULL COMMENT '应用ID',
  `form_id` varchar(100) NOT NULL,
  `page_id` int(10) DEFAULT NULL,
  `page_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '页面标识',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `pkey` varchar(100) DEFAULT NULL,
  `link_id` varchar(200) NOT NULL COMMENT '关联商品ID',
  `link_title` varchar(1000) CHARACTER SET utf8 NOT NULL COMMENT '关联商品标题',
  `link_json` varchar(4000) CHARACTER SET utf8 DEFAULT NULL COMMENT '关联商品备注',
  `link_price` decimal(15,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '关联商品价格',
  `link_img` varchar(2000) CHARACTER SET utf8 DEFAULT NULL COMMENT '关联商品缩略图',
  `link_total` int(5) NOT NULL DEFAULT '1' COMMENT '购买商品数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1为有效，0为无效',
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='购物车' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_order_pay`
--

CREATE TABLE IF NOT EXISTS `diygw_app_order_pay` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户Openid',
  `pay_title` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_attach` varchar(127) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_detail` varchar(600) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_price` decimal(15,2) NOT NULL DEFAULT '1.00' COMMENT '实际支付总价',
  `pay_ip` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment` decimal(15,2) DEFAULT NULL,
  `trade_no` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '支付单号',
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付单号（微信、支付宝等）',
  `pay_type` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '支付方式：0:微信:1支付宝;',
  `pay_time` datetime DEFAULT NULL COMMENT '购买时间(下单时间)',
  `pay_end_time` datetime DEFAULT NULL,
  `mpid` int(11) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '订单状态：0:已取消;1(默认):未付款;2:已付款;',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='订单信息' AUTO_INCREMENT=57 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_order_refund`
--

CREATE TABLE IF NOT EXISTS `diygw_app_order_refund` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` varchar(32) NOT NULL COMMENT '订单项id',
  `trade_no` varchar(32) NOT NULL COMMENT '退款交易号',
  `refund_trade_no` varchar(32) DEFAULT NULL,
  `refund_money` decimal(10,2) NOT NULL COMMENT '退款金额',
  `refund_way` int(11) NOT NULL COMMENT '退款方式0:微信:1支付宝;10：线下',
  `refund_time` datetime NOT NULL COMMENT '退款时间',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单退款账户记录' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_app_page`
--

CREATE TABLE IF NOT EXISTS `diygw_app_page` (
  `id` varchar(30) NOT NULL,
  `dashboard_id` int(10) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `scene_id` int(10) DEFAULT NULL,
  `template_type` varchar(1) DEFAULT '0',
  `template` varchar(100) DEFAULT NULL,
  `attributes` longtext,
  `content` longtext,
  `is_home` int(1) DEFAULT '0',
  `orderlist` int(5) DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `mpid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_db`
--

CREATE TABLE IF NOT EXISTS `diygw_db` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(200) DEFAULT '',
  `remark` varchar(2000) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT '',
  `database` varchar(100) NOT NULL,
  `hostname` varchar(1000) NOT NULL DEFAULT '',
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(200) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '安装时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='插件表' AUTO_INCREMENT=72 ;

--
-- 转存表中的数据 `diygw_db`
--

INSERT INTO `diygw_db` (`id`, `title`, `remark`, `type`, `database`, `hostname`, `username`, `password`, `create_time`) VALUES
(10, '远程数据库', ' ', 'MySQL', 'diygw_wechat', 'localhost', 'root', 'eT5ZT2K+IxSWy52tgCfs7g==', '2018-12-16 20:53:03'),
(71, 'www_2hfp_com', ' ', 'MySQL', '', '', 'www_2hfp_com', '3xvaJpeGV/el5XkCI3zr0w==', '2018-04-20 22:19:39');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_hooks`
--

CREATE TABLE IF NOT EXISTS `diygw_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 ''，''分割',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `diygw_hooks`
--

INSERT INTO `diygw_hooks` (`id`, `name`, `description`, `type`, `update_time`, `addons`, `status`) VALUES
(1, 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', 1, 0, '', 1),
(2, 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', 1, 0, '', 1),
(3, 'AdminIndex', '首页小格子个性化显示', 1, 1479394250, 'systeminfo,devteam', 1);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_member`
--

CREATE TABLE IF NOT EXISTS `diygw_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `mpid` int(10) DEFAULT '1' COMMENT '公众号ID',
  `dashboardid` int(10) DEFAULT NULL,
  `username` varchar(500) NOT NULL COMMENT '用户名',
  `password` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '密码',
  `rand` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '随机码',
  `email` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '邮箱',
  `nickname` varchar(500) DEFAULT NULL COMMENT '昵称',
  `sex` int(1) DEFAULT '0' COMMENT '性别',
  `headimgurl` varchar(1000) CHARACTER SET utf8 DEFAULT NULL COMMENT '头像',
  `mobile` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '电话',
  `address` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '地址',
  `birth` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '生日',
  `status` int(1) DEFAULT '1' COMMENT '状态',
  `register_ip` varchar(22) CHARACTER SET utf8 DEFAULT '0' COMMENT '注册IP',
  `refresh_time` int(10) DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` varchar(22) CHARACTER SET utf8 DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  PRIMARY KEY (`uid`),
  KEY `sex` (`sex`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_member_auth`
--

CREATE TABLE IF NOT EXISTS `diygw_member_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `openid` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '1' COMMENT '公众号用户ID',
  `mpid` int(10) NOT NULL COMMENT '公众号ID',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `type` tinyint(1) NOT NULL COMMENT '1:微信',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_payment`
--

CREATE TABLE IF NOT EXISTS `diygw_payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
  `dashboard_id` int(11) DEFAULT NULL,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户 ID',
  `openid` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'OPENID',
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '标题|商品名称',
  `trade_no` varchar(32) NOT NULL DEFAULT '0' COMMENT '订单号',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '交易类型（1为微信2为支付宝）',
  `from_addon` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0：未完成交易1：完成关键交易）',
  `create_time` int(10) NOT NULL COMMENT '交易时间',
  `mpid` int(11) NOT NULL COMMENT '公众号标识',
  `remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  `attach` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '附加数据',
  `refund` tinyint(1) DEFAULT NULL COMMENT '1：申请退款中2：退款完成',
  PRIMARY KEY (`payment_id`),
  KEY `openid` (`openid`),
  KEY `member_id` (`member_id`),
  KEY `mpid` (`mpid`),
  KEY `order_number` (`trade_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=57 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_auth`
--

CREATE TABLE IF NOT EXISTS `diygw_system_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(1:禁用,2:启用)',
  `sort` smallint(6) unsigned DEFAULT '0' COMMENT '排序权重',
  `desc` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_by` bigint(11) unsigned DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_auth_title` (`title`) USING BTREE,
  KEY `index_system_auth_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统权限表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_auth_node`
--

CREATE TABLE IF NOT EXISTS `diygw_system_auth_node` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auth` bigint(20) unsigned DEFAULT NULL COMMENT '角色ID',
  `node` varchar(200) DEFAULT NULL COMMENT '节点路径',
  PRIMARY KEY (`id`),
  KEY `index_system_auth_auth` (`auth`) USING BTREE,
  KEY `index_system_auth_node` (`node`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统角色与节点绑定' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_config`
--

CREATE TABLE IF NOT EXISTS `diygw_system_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '配置编码',
  `value` varchar(500) DEFAULT NULL COMMENT '配置值',
  PRIMARY KEY (`id`),
  KEY `index_system_config_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统参数配置' AUTO_INCREMENT=44 ;

--
-- 转存表中的数据 `diygw_system_config`
--

INSERT INTO `diygw_system_config` (`id`, `name`, `value`) VALUES
(1, 'app_name', 'GIYGW.COM'),
(2, 'site_name', 'DIY官网'),
(3, 'app_version', 'V1.0'),
(4, 'site_copy', '©版权所有 2014-2018 DIY官网'),
(5, 'browser_icon', 'http://wx.diygw.com/static/upload/95d51ddab6601a89/51905192984e48cb.ico'),
(6, 'tongji_baidu_key', ''),
(7, 'miitbeian', '粤ICP备12026349号'),
(8, 'storage_type', 'local'),
(9, 'storage_local_exts', 'png,jpg,rar,doc,icon,mp4,ico'),
(10, 'storage_qiniu_bucket', ''),
(11, 'storage_qiniu_domain', ''),
(12, 'storage_qiniu_access_key', ''),
(13, 'storage_qiniu_secret_key', ''),
(14, 'storage_oss_bucket', 'cuci'),
(15, 'storage_oss_endpoint', 'oss-cn-beijing.aliyuncs.com'),
(16, 'storage_oss_domain', 'cuci.oss-cn-beijing.aliyuncs.com'),
(17, 'storage_oss_keyid', '用你自己的吧'),
(18, 'storage_oss_secret', '用你自己的吧'),
(34, 'wechat_appid', 'wx60a43dd8161666d4'),
(35, 'wechat_appkey', '9890a0d7c91801a609d151099e95b61a'),
(36, 'storage_oss_is_https', 'http'),
(37, 'wechat_type', 'thr'),
(38, 'wechat_token', 'test'),
(39, 'wechat_appsecret', 'a041bec98ed015d52b99acea5c6a16ef'),
(40, 'wechat_encodingaeskey', 'BJIUzE0gqlWy0GxfPp4J1oPTBmOrNDIGPNav1YFH5Z5'),
(41, 'wechat_thr_appid', 'wx60a43dd8161666d4'),
(42, 'wechat_thr_appkey', '05db2aa335382c66ab56d69b1a9ad0ee'),
(43, 'wechat_default', '1');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_log`
--

CREATE TABLE IF NOT EXISTS `diygw_system_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `node` char(200) NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '操作人用户名',
  `action` varchar(200) NOT NULL DEFAULT '' COMMENT '操作行为',
  `content` text NOT NULL COMMENT '操作内容描述',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统操作日志表' AUTO_INCREMENT=80 ;

--
-- 转存表中的数据 `diygw_system_log`
--

INSERT INTO `diygw_system_log` (`id`, `ip`, `node`, `username`, `action`, `content`, `create_at`) VALUES
(1, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-26 02:23:00'),
(2, '127.0.0.1', 'admin/config/index', 'admin', '系统管理', '系统参数配置成功', '2018-11-26 08:49:14'),
(3, '127.0.0.1', 'admin/config/index', 'admin', '系统管理', '系统参数配置成功', '2018-11-26 08:49:28'),
(4, '127.0.0.1', 'admin/config/index', 'admin', '系统管理', '系统参数配置成功', '2018-11-26 08:50:54'),
(5, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-26 13:30:18'),
(6, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-27 01:59:11'),
(7, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-27 07:30:54'),
(8, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-11-27 07:35:33'),
(9, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-27 08:23:24'),
(10, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-27 11:47:39'),
(11, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-28 01:12:07'),
(12, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-28 07:07:31'),
(13, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-30 01:16:24'),
(14, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-30 04:50:38'),
(15, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-11-30 07:57:59'),
(16, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-01 03:45:47'),
(17, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-02 01:43:10'),
(18, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-02 01:43:18'),
(19, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-02 01:47:10'),
(20, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-02 06:34:09'),
(21, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-02 08:16:00'),
(22, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-03 02:08:30'),
(23, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-03 06:54:39'),
(24, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-03 12:07:05'),
(25, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-03 12:16:59'),
(26, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-03 12:18:25'),
(27, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-03 12:35:46'),
(28, '127.0.0.1', 'admin/config/index', 'admin', '系统管理', '系统参数配置成功', '2018-12-03 13:18:47'),
(29, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-03 14:13:15'),
(30, '127.0.0.1', 'admin/config/index', 'admin', '系统管理', '系统参数配置成功', '2018-12-03 14:22:55'),
(31, '127.0.0.1', 'admin/config/index', 'admin', '系统管理', '系统参数配置成功', '2018-12-03 14:23:31'),
(32, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 01:03:36'),
(33, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 01:30:50'),
(34, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 01:30:56'),
(35, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 01:35:26'),
(36, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 01:36:02'),
(37, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 02:28:33'),
(38, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 02:31:38'),
(39, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 02:38:43'),
(40, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 02:38:49'),
(41, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 03:01:19'),
(42, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 03:01:26'),
(43, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 03:01:55'),
(44, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 03:02:17'),
(45, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 03:03:12'),
(46, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 03:03:20'),
(47, '127.0.0.1', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2018-12-04 03:04:16'),
(48, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 03:04:22'),
(49, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-04 14:36:56'),
(50, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-05 02:26:17'),
(51, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-05 12:43:20'),
(52, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-07 01:06:56'),
(53, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-07 08:18:38'),
(54, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-11 08:10:11'),
(55, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-12 05:15:33'),
(56, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-12 08:10:42'),
(57, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-12 09:27:09'),
(58, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-12 12:20:18'),
(59, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-14 12:19:32'),
(60, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-15 01:21:14'),
(61, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-15 08:42:58'),
(62, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-15 09:11:10'),
(63, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-15 14:31:29'),
(64, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-16 04:13:45'),
(65, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-16 12:41:22'),
(66, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-17 01:48:53'),
(67, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-17 03:33:55'),
(68, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-17 12:46:39'),
(69, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-18 12:41:38'),
(70, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-18 14:40:12'),
(71, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-19 01:31:08'),
(72, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-19 03:00:18'),
(73, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-19 03:43:11'),
(74, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-19 03:47:43'),
(75, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-19 13:56:25'),
(76, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-19 14:48:15'),
(77, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-20 06:34:53'),
(78, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-20 07:31:22'),
(79, '127.0.0.1', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2018-12-20 12:22:34');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_menu`
--

CREATE TABLE IF NOT EXISTS `diygw_system_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `node` varchar(200) NOT NULL DEFAULT '' COMMENT '节点代码',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(400) NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `create_by` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_menu_node` (`node`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统菜单表' AUTO_INCREMENT=55 ;

--
-- 转存表中的数据 `diygw_system_menu`
--

INSERT INTO `diygw_system_menu` (`id`, `pid`, `title`, `node`, `icon`, `url`, `params`, `target`, `sort`, `status`, `create_by`, `create_at`) VALUES
(1, 0, '系统设置', '', '', '#', '', '_self', 9000, 1, 10000, '2018-01-19 07:27:00'),
(2, 10, '后台菜单', '', 'fa fa-leaf', 'admin/menu/index', '', '_self', 10, 1, 10000, '2018-01-19 07:27:17'),
(3, 10, '系统参数', '', 'fa fa-modx', 'admin/config/index', '', '_self', 20, 1, 10000, '2018-01-19 07:27:57'),
(4, 11, '访问授权', '', 'fa fa-group', 'admin/auth/index', '', '_self', 20, 1, 10000, '2018-01-22 03:13:02'),
(5, 11, '用户管理', '', 'fa fa-user', 'admin/user/index', '', '_self', 10, 1, 0, '2018-01-23 04:15:12'),
(6, 11, '访问节点', '', 'fa fa-fort-awesome', 'admin/node/index', '', '_self', 30, 1, 0, '2018-01-23 04:36:54'),
(7, 0, '后台首页', '', '', '#', '', '_self', 0, 1, 0, '2018-01-23 05:42:30'),
(8, 16, '系统日志', '', 'fa fa-code', 'admin/log/index', '', '_self', 10, 1, 0, '2018-01-24 05:52:58'),
(9, 10, '文件存储', '', 'fa fa-stop-circle', 'admin/config/file', '', '_self', 30, 1, 0, '2018-01-25 02:54:28'),
(10, 1, '系统管理', '', '', '#', '', '_self', 200, 1, 0, '2018-01-25 10:14:28'),
(11, 1, '访问权限', '', '', '#', '', '_self', 300, 1, 0, '2018-01-25 10:15:08'),
(16, 1, '日志管理', '', '', '#', '', '_self', 400, 1, 0, '2018-02-10 08:31:15'),
(17, 0, '微信管理', '', '', '#', '', '_self', 8000, 1, 0, '2018-03-06 06:42:49'),
(18, 17, '公众号配置', '', '', '#', '', '_self', 0, 1, 0, '2018-03-06 06:43:05'),
(19, 18, '功能配置', '', 'fa fa-cog', 'wechat/config/index', '', '_self', 4, 1, 0, '2018-03-06 06:43:26'),
(20, 18, '关注默认回复', '', 'fa fa-comment-o', 'wechat/keys/subscribe', '', '_self', 0, 1, 0, '2018-03-06 06:44:45'),
(21, 18, '无反馈默认回复', '', 'fa fa-commenting', 'wechat/keys/defaults', '', '_self', 0, 1, 0, '2018-03-06 06:45:55'),
(22, 18, '微信关键字管理', '', 'fa fa-hashtag', 'wechat/keys/index', '', '_self', 0, 1, 0, '2018-03-06 06:46:23'),
(23, 17, '微信服务定制', '', '', '#', '', '_self', 0, 1, 0, '2018-03-06 06:47:11'),
(24, 23, '微信菜单管理', '', 'fa fa-gg-circle', 'wechat/menu/index', '', '_self', 0, 1, 0, '2018-03-06 06:47:39'),
(25, 23, '微信图文管理', '', 'fa fa-map-o', 'wechat/news/index', '', '_self', 0, 1, 0, '2018-03-06 06:48:14'),
(26, 17, '微信粉丝管理', '', '', '#', '', '_self', 0, 1, 0, '2018-03-06 06:48:33'),
(27, 26, '微信粉丝列表', '', 'fa fa-users', 'wechat/fans/index', '', '_self', 20, 1, 0, '2018-03-06 06:49:04'),
(28, 26, '微信黑名单管理', '', 'fa fa-user-times', 'wechat/fans_block/index', '', '_self', 30, 1, 0, '2018-03-06 06:49:22'),
(29, 26, '微信标签管理', '', 'fa fa-tags', 'wechat/tags/index', '', '_self', 10, 1, 0, '2018-03-06 06:49:39'),
(43, 1, '微信平台', '', '', '#', '', '_self', 0, 1, 0, '2018-11-26 02:24:29'),
(44, 43, '公众号管理', '', 'layui-icon layui-icon-login-wechat', 'admin/wechat/index', '', '_self', 0, 1, 0, '2018-11-26 02:25:37'),
(45, 49, '插件管理', '', 'fa fa-plug', 'admin/addons/index', '', '_self', 0, 1, 0, '2018-11-27 08:50:20'),
(47, 7, '快键菜单', '', '', '#', '', '_self', 0, 1, 0, '2018-12-04 01:05:50'),
(48, 47, '后台首页', '', '', 'admin/index/index', '', '_self', 0, 1, 0, '2018-12-04 01:06:29'),
(49, 1, '系统扩展', '', '', '#', '', '_self', 0, 1, 0, '2018-12-04 07:20:26'),
(50, 49, '钩子管理', '', 'layui-icon layui-icon-ok', 'admin/hooks/index', '', '_self', 0, 1, 0, '2018-12-04 07:23:00'),
(51, 0, '应用管理', '', '', '#', '', '_self', 1001, 1, 0, '2018-12-04 14:38:32'),
(52, 51, '应用管理', '', '', '#', '', '_self', 0, 1, 0, '2018-12-04 14:38:57'),
(53, 52, '应用管理', '', 'layui-icon layui-icon-app', 'diygw/dashboard/index', '', '_self', 0, 1, 0, '2018-12-04 14:40:15'),
(54, 52, '云服务授权', '', 'fa fa-key', 'diygw/dashboard/auth', '', '_self', 0, 1, 0, '2018-12-04 14:42:15');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_node`
--

CREATE TABLE IF NOT EXISTS `diygw_system_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(100) DEFAULT NULL COMMENT '节点代码',
  `title` varchar(500) DEFAULT NULL COMMENT '节点标题',
  `is_menu` tinyint(1) unsigned DEFAULT '0' COMMENT '是否可设置为菜单',
  `is_auth` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启动RBAC权限控制',
  `is_login` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启动登录控制',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_node_node` (`node`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统节点表' AUTO_INCREMENT=146 ;

--
-- 转存表中的数据 `diygw_system_node`
--

INSERT INTO `diygw_system_node` (`id`, `node`, `title`, `is_menu`, `is_auth`, `is_login`, `create_at`) VALUES
(13, 'admin', '系统设置', 0, 1, 1, '2018-05-04 03:02:34'),
(14, 'admin/auth', '权限管理', 0, 1, 1, '2018-05-04 03:06:55'),
(15, 'admin/auth/index', '权限列表', 1, 1, 1, '2018-05-04 03:06:56'),
(16, 'admin/auth/apply', '权限配置', 0, 1, 1, '2018-05-04 03:06:56'),
(17, 'admin/auth/add', '添加权限', 0, 1, 1, '2018-05-04 03:06:56'),
(18, 'admin/auth/edit', '编辑权限', 0, 1, 1, '2018-05-04 03:06:56'),
(19, 'admin/auth/forbid', '禁用权限', 0, 1, 1, '2018-05-04 03:06:56'),
(20, 'admin/auth/resume', '启用权限', 0, 1, 1, '2018-05-04 03:06:56'),
(21, 'admin/auth/del', '删除权限', 0, 1, 1, '2018-05-04 03:06:56'),
(22, 'admin/config', '系统配置', 0, 1, 1, '2018-05-04 03:08:18'),
(23, 'admin/config/index', '系统参数', 1, 1, 1, '2018-05-04 03:08:25'),
(24, 'admin/config/file', '文件存储', 1, 1, 1, '2018-05-04 03:08:27'),
(25, 'admin/log', '日志管理', 0, 1, 1, '2018-05-04 03:08:43'),
(26, 'admin/log/index', '日志管理', 1, 1, 1, '2018-05-04 03:08:43'),
(28, 'admin/log/del', '日志删除', 0, 1, 1, '2018-05-04 03:08:43'),
(29, 'admin/menu', '系统菜单', 0, 1, 1, '2018-05-04 03:09:54'),
(30, 'admin/menu/index', '菜单列表', 1, 1, 1, '2018-05-04 03:09:54'),
(31, 'admin/menu/add', '添加菜单', 0, 1, 1, '2018-05-04 03:09:55'),
(32, 'admin/menu/edit', '编辑菜单', 0, 1, 1, '2018-05-04 03:09:55'),
(33, 'admin/menu/del', '删除菜单', 0, 1, 1, '2018-05-04 03:09:55'),
(34, 'admin/menu/forbid', '禁用菜单', 0, 1, 1, '2018-05-04 03:09:55'),
(35, 'admin/menu/resume', '启用菜单', 0, 1, 1, '2018-05-04 03:09:55'),
(36, 'admin/node', '节点管理', 0, 1, 1, '2018-05-04 03:10:20'),
(37, 'admin/node/index', '节点列表', 1, 1, 1, '2018-05-04 03:10:20'),
(38, 'admin/node/clear', '清理节点', 0, 1, 1, '2018-05-04 03:10:21'),
(39, 'admin/node/save', '更新节点', 0, 1, 1, '2018-05-04 03:10:21'),
(40, 'admin/user', '系统用户', 0, 1, 1, '2018-05-04 03:10:43'),
(41, 'admin/user/index', '用户列表', 1, 1, 1, '2018-05-04 03:10:43'),
(42, 'admin/user/auth', '用户授权', 0, 1, 1, '2018-05-04 03:10:43'),
(43, 'admin/user/add', '添加用户', 0, 1, 1, '2018-05-04 03:10:43'),
(44, 'admin/user/edit', '编辑用户', 0, 1, 1, '2018-05-04 03:10:43'),
(45, 'admin/user/pass', '修改密码', 0, 1, 1, '2018-05-04 03:10:43'),
(46, 'admin/user/del', '删除用户', 0, 1, 1, '2018-05-04 03:10:43'),
(47, 'admin/user/forbid', '禁用启用', 0, 1, 1, '2018-05-04 03:10:43'),
(48, 'admin/user/resume', '启用用户', 0, 1, 1, '2018-05-04 03:10:44'),
(49, 'store', '商城管理', 0, 1, 1, '2018-05-04 03:11:28'),
(50, 'store/express', '快递公司管理', 0, 1, 1, '2018-05-04 03:11:39'),
(51, 'store/express/index', '快递公司列表', 1, 1, 1, '2018-05-04 03:11:39'),
(52, 'store/express/add', '添加快递公司', 0, 1, 1, '2018-05-04 03:11:39'),
(53, 'store/express/edit', '编辑快递公司', 0, 1, 1, '2018-05-04 03:11:39'),
(54, 'store/express/del', '删除快递公司', 0, 1, 1, '2018-05-04 03:11:39'),
(55, 'store/express/forbid', '禁用快递公司', 0, 1, 1, '2018-05-04 03:11:39'),
(56, 'store/express/resume', '启用快递公司', 0, 1, 1, '2018-05-04 03:11:40'),
(57, 'store/order', '订单管理', 0, 1, 1, '2018-05-04 03:12:14'),
(58, 'store/order/index', '订单列表', 1, 1, 1, '2018-05-04 03:12:17'),
(59, 'store/order/address', '修改地址', 0, 1, 1, '2018-05-04 03:12:19'),
(76, 'wechat', '微信管理', 0, 1, 1, '2018-05-04 03:14:59'),
(78, 'wechat/config', '微信对接管理', 0, 1, 1, '2018-05-04 03:16:20'),
(79, 'wechat/config/index', '微信对接配置', 1, 1, 1, '2018-05-04 03:16:23'),
(80, 'wechat/fans', '微信粉丝管理', 0, 1, 1, '2018-05-04 03:16:31'),
(81, 'wechat/fans/index', '微信粉丝列表', 1, 1, 1, '2018-05-04 03:16:32'),
(82, 'wechat/fans/backadd', '微信粉丝拉黑', 0, 1, 1, '2018-05-04 03:16:32'),
(83, 'wechat/fans/tagset', '设置粉丝标签', 0, 1, 1, '2018-05-04 03:16:32'),
(84, 'wechat/fans/tagadd', '添加粉丝标签', 0, 1, 1, '2018-05-04 03:16:32'),
(85, 'wechat/fans/tagdel', '删除粉丝标签', 0, 1, 1, '2018-05-04 03:16:32'),
(86, 'wechat/fans/sync', '同步粉丝列表', 0, 1, 1, '2018-05-04 03:16:32'),
(87, 'wechat/fans_block', '粉丝黑名单管理', 0, 1, 1, '2018-05-04 03:17:25'),
(88, 'wechat/fans_block/index', '粉丝黑名单列表', 1, 1, 1, '2018-05-04 03:17:50'),
(89, 'wechat/fans_block/backdel', '移除粉丝黑名单', 0, 1, 1, '2018-05-04 03:17:51'),
(90, 'wechat/keys', '微信关键字', 0, 1, 1, '2018-05-04 03:18:09'),
(91, 'wechat/keys/index', '关键字列表', 1, 1, 1, '2018-05-04 03:18:09'),
(92, 'wechat/keys/add', '添加关键字', 0, 1, 1, '2018-05-04 03:18:09'),
(93, 'wechat/keys/edit', '编辑关键字', 0, 1, 1, '2018-05-04 03:18:09'),
(94, 'wechat/keys/del', '删除关键字', 0, 1, 1, '2018-05-04 03:18:09'),
(95, 'wechat/keys/forbid', '禁用关键字', 0, 1, 1, '2018-05-04 03:18:09'),
(96, 'wechat/keys/resume', '启用关键字', 0, 1, 1, '2018-05-04 03:18:09'),
(97, 'wechat/keys/subscribe', '关注回复', 1, 1, 1, '2018-05-04 03:18:09'),
(98, 'wechat/keys/defaults', '默认回复', 1, 1, 1, '2018-05-04 03:18:09'),
(99, 'wechat/menu', '微信菜单管理', 0, 1, 1, '2018-05-04 03:18:57'),
(100, 'wechat/menu/index', '微信菜单展示', 1, 1, 1, '2018-05-04 03:19:10'),
(101, 'wechat/menu/edit', '编辑微信菜单', 0, 1, 1, '2018-05-04 03:19:22'),
(102, 'wechat/menu/cancel', '取消微信菜单', 0, 1, 1, '2018-05-04 03:19:26'),
(103, 'wechat/news/index', '微信图文列表', 1, 1, 1, '2018-05-04 03:19:28'),
(104, 'wechat/news/select', '微信图文选择', 0, 1, 1, '2018-05-04 03:19:28'),
(105, 'wechat/news/image', '微信图片选择', 0, 1, 1, '2018-05-04 03:19:28'),
(106, 'wechat/news/add', '添加微信图文', 0, 1, 1, '2018-05-04 03:19:28'),
(107, 'wechat/news/edit', '编辑微信图文', 0, 1, 1, '2018-05-04 03:19:28'),
(108, 'wechat/news/del', '删除微信图文', 0, 1, 1, '2018-05-04 03:19:28'),
(109, 'wechat/news/push', '推送微信图文', 0, 1, 1, '2018-05-04 03:19:28'),
(110, 'wechat/news', '微信图文管理', 0, 1, 1, '2018-05-04 03:19:35'),
(111, 'wechat/tags', '微信粉丝标签管理', 0, 1, 1, '2018-05-04 03:20:28'),
(112, 'wechat/tags/index', '粉丝标签列表', 1, 1, 1, '2018-05-04 03:20:28'),
(113, 'wechat/tags/add', '添加粉丝标签', 0, 1, 1, '2018-05-04 03:20:28'),
(114, 'wechat/tags/edit', '编辑粉丝标签', 0, 1, 1, '2018-05-04 03:20:29'),
(115, 'wechat/tags/del', '删除粉丝标签', 0, 1, 1, '2018-05-04 03:20:29'),
(116, 'wechat/tags/sync', '同步粉丝标签', 0, 1, 1, '2018-05-04 03:20:29'),
(117, 'store/goods', '商品管理', 0, 1, 1, '2018-05-04 03:29:55'),
(118, 'store/goods/index', '商品列表', 1, 1, 1, '2018-05-04 03:29:56'),
(119, 'store/goods/add', '添加商品', 0, 1, 1, '2018-05-04 03:29:56'),
(120, 'store/goods/edit', '编辑商品', 0, 1, 1, '2018-05-04 03:29:56'),
(121, 'store/goods/del', '删除商品', 0, 1, 1, '2018-05-04 03:29:56'),
(122, 'store/goods/forbid', '下架商品', 0, 1, 1, '2018-05-04 03:29:56'),
(123, 'store/goods/resume', '上架商品', 0, 1, 1, '2018-05-04 03:29:57'),
(124, 'store/goods_brand', '商品品牌管理', 0, 1, 1, '2018-05-04 03:30:44'),
(125, 'store/goods_brand/index', '商品品牌列表', 1, 1, 1, '2018-05-04 03:30:52'),
(126, 'store/goods_brand/add', '添加商品品牌', 0, 1, 1, '2018-05-04 03:30:55'),
(127, 'store/goods_brand/edit', '编辑商品品牌', 0, 1, 1, '2018-05-04 03:30:56'),
(128, 'store/goods_brand/del', '删除商品品牌', 0, 1, 1, '2018-05-04 03:30:56'),
(129, 'store/goods_brand/forbid', '禁用商品品牌', 0, 1, 1, '2018-05-04 03:30:56'),
(130, 'store/goods_brand/resume', '启用商品品牌', 0, 1, 1, '2018-05-04 03:30:56'),
(131, 'store/goods_cate', '商品分类管理', 0, 1, 1, '2018-05-04 03:31:19'),
(132, 'store/goods_cate/index', '商品分类列表', 1, 1, 1, '2018-05-04 03:31:23'),
(133, 'store/goods_cate/add', '添加商品分类', 0, 1, 1, '2018-05-04 03:31:23'),
(134, 'store/goods_cate/edit', '编辑商品分类', 0, 1, 1, '2018-05-04 03:31:23'),
(135, 'store/goods_cate/del', '删除商品分类', 0, 1, 1, '2018-05-04 03:31:24'),
(136, 'store/goods_cate/forbid', '禁用商品分类', 0, 1, 1, '2018-05-04 03:31:24'),
(137, 'store/goods_cate/resume', '启用商品分类', 0, 1, 1, '2018-05-04 03:31:24'),
(138, 'store/goods_spec', '商品规格管理', 0, 1, 1, '2018-05-04 03:31:47'),
(139, 'store/goods_spec/index', '商品规格列表', 1, 1, 1, '2018-05-04 03:31:47'),
(140, 'store/goods_spec/add', '添加商品规格', 0, 1, 1, '2018-05-04 03:31:47'),
(141, 'store/goods_spec/edit', '编辑商品规格', 0, 1, 1, '2018-05-04 03:31:48'),
(142, 'store/goods_spec/del', '删除商品规格', 0, 1, 1, '2018-05-04 03:31:48'),
(143, 'store/goods_spec/forbid', '禁用商品规格', 0, 1, 1, '2018-05-04 03:31:48'),
(144, 'store/goods_spec/resume', '启用商品规格', 0, 1, 1, '2018-05-04 03:31:48'),
(145, 'diygw', '应用管理', 0, 1, 1, '2018-12-19 15:05:26');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_sequence`
--

CREATE TABLE IF NOT EXISTS `diygw_system_sequence` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL COMMENT '序号类型',
  `sequence` char(50) NOT NULL COMMENT '序号值',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_sequence_unique` (`type`,`sequence`) USING BTREE,
  KEY `index_system_sequence_type` (`type`),
  KEY `index_system_sequence_number` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统序号表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_system_user`
--

CREATE TABLE IF NOT EXISTS `diygw_system_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户登录名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户登录密码',
  `qq` varchar(16) DEFAULT NULL COMMENT '联系QQ',
  `mail` varchar(32) DEFAULT NULL COMMENT '联系邮箱',
  `phone` varchar(16) DEFAULT NULL COMMENT '联系手机号',
  `desc` varchar(255) DEFAULT '' COMMENT '备注说明',
  `login_num` bigint(20) unsigned DEFAULT '0' COMMENT '登录次数',
  `login_at` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `authorize` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除状态(1:删除,0:未删)',
  `create_by` bigint(20) unsigned DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_user_username` (`username`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统用户表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `diygw_system_user`
--

INSERT INTO `diygw_system_user` (`id`, `username`, `password`, `qq`, `mail`, `phone`, `desc`, `login_num`, `login_at`, `status`, `authorize`, `is_deleted`, `create_by`, `create_at`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '280160522', 'diygwcom@foxmail.com', '', '', 23036, '2018-12-20 20:22:33', 1, '2,4', 0, NULL, '2015-11-13 07:14:22');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `name` varchar(50) NOT NULL COMMENT '公众号名称',
  `appid` varchar(50) DEFAULT NULL COMMENT 'AppId',
  `appsecret` varchar(50) DEFAULT NULL COMMENT 'AppSecret',
  `origin_id` varchar(50) NOT NULL COMMENT '公众号原始ID',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '公众号类型（1：普通订阅号；2：认证订阅号；3：普通服务号；4：认证服务号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常，2：审核中）',
  `autologin` tinyint(1) DEFAULT '1' COMMENT '1:自动登录并注册用户 0表示手动注册用户 ',
  `valid_token` varchar(40) DEFAULT NULL COMMENT '接口验证Token',
  `valid_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已接入；0未接入',
  `token` varchar(50) DEFAULT NULL COMMENT '公众号标识',
  `encodingaeskey` varchar(50) DEFAULT NULL COMMENT '消息加解密秘钥',
  `mp_number` varchar(50) DEFAULT NULL COMMENT '微信号',
  `desc` text COMMENT '描述',
  `logo` varchar(255) DEFAULT NULL COMMENT 'logo',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `login_name` varchar(50) DEFAULT NULL COMMENT '公众号登录名',
  `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '当前使用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='公众号表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `diygw_wechat`
--

INSERT INTO `diygw_wechat` (`id`, `user_id`, `name`, `appid`, `appsecret`, `origin_id`, `type`, `status`, `autologin`, `valid_token`, `valid_status`, `token`, `encodingaeskey`, `mp_number`, `desc`, `logo`, `qrcode`, `create_time`, `login_name`, `is_use`) VALUES
(1, 1, '测试公众号', '1', '1', '1', 1, 1, 1, 'hv5Ri9lkNrUXdNRZEHuFU1ejDpQUlrFG', 0, 'coZZbWvWyMbdiv4rqLGwQVkkvo8eUJ5L', 'lEGVThSs1vxwlVmftbinFhvLevTzaGgh4o5gZKUI2uB', '1', '1', '', '', 1543221891, NULL, 0);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_config`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(11) NOT NULL COMMENT '公众号标识',
  `name` varchar(180) NOT NULL COMMENT '配置项名称',
  `value` text NOT NULL COMMENT '配置值',
  `cate` varchar(30) DEFAULT NULL COMMENT '分类',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_fans`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_fans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `appid` char(50) DEFAULT '' COMMENT '公众号Appid',
  `unionid` char(100) DEFAULT '' COMMENT 'unionid',
  `openid` char(100) DEFAULT '' COMMENT '用户的标识,对当前公众号唯一',
  `spread_openid` char(100) DEFAULT '' COMMENT '推荐人OPENID',
  `spread_at` datetime DEFAULT NULL COMMENT '推荐时间',
  `tagid_list` varchar(100) DEFAULT '' COMMENT '标签id',
  `is_black` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为黑名单用户',
  `subscribe` tinyint(1) unsigned DEFAULT '0' COMMENT '用户是否关注该公众号(0:未关注, 1:已关注)',
  `nickname` varchar(200) DEFAULT '' COMMENT '用户的昵称',
  `sex` tinyint(1) unsigned DEFAULT NULL COMMENT '用户的性别,值为1时是男性,值为2时是女性,值为0时是未知',
  `country` varchar(50) DEFAULT '' COMMENT '用户所在国家',
  `province` varchar(50) DEFAULT '' COMMENT '用户所在省份',
  `city` varchar(50) DEFAULT '' COMMENT '用户所在城市',
  `language` varchar(50) DEFAULT '' COMMENT '用户的语言,简体中文为zh_CN',
  `headimgurl` varchar(500) DEFAULT '' COMMENT '用户头像',
  `subscribe_time` bigint(20) unsigned DEFAULT '0' COMMENT '用户关注时间',
  `subscribe_at` datetime DEFAULT NULL COMMENT '关注时间',
  `remark` varchar(50) DEFAULT '' COMMENT '备注',
  `expires_in` bigint(20) unsigned DEFAULT '0' COMMENT '有效时间',
  `refresh_token` varchar(200) DEFAULT '' COMMENT '刷新token',
  `access_token` varchar(200) DEFAULT '' COMMENT '访问token',
  `subscribe_scene` varchar(200) DEFAULT '' COMMENT '扫码关注场景',
  `qr_scene` varchar(100) DEFAULT '' COMMENT '二维码场景值',
  `qr_scene_str` varchar(200) DEFAULT '' COMMENT '二维码场景内容',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_fans_spread_openid` (`spread_openid`) USING BTREE,
  KEY `index_wechat_fans_openid` (`openid`) USING BTREE,
  KEY `index_wechat_fans_unionid` (`unionid`),
  KEY `index_wechat_fans_is_back` (`is_black`),
  KEY `index_wechat_fans_subscribe` (`subscribe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信粉丝' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_fans_tags`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_fans_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `mpid` int(11) DEFAULT '1' COMMENT '公众号ID',
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `name` varchar(35) DEFAULT NULL COMMENT '标签名称',
  `count` int(11) unsigned DEFAULT NULL COMMENT '总数',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  KEY `index_wechat_fans_tags_id` (`id`) USING BTREE,
  KEY `index_wechat_fans_tags_appid` (`appid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信会员标签' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_keys`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_keys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `appid` char(100) DEFAULT '' COMMENT '公众号APPID',
  `type` varchar(20) DEFAULT '' COMMENT '类型，text 文件消息，image 图片消息，news 图文消息',
  `keys` varchar(100) DEFAULT NULL COMMENT '关键字',
  `content` text COMMENT '文本内容',
  `image_url` varchar(255) DEFAULT '' COMMENT '图片链接',
  `voice_url` varchar(255) DEFAULT '' COMMENT '语音链接',
  `music_title` varchar(100) DEFAULT '' COMMENT '音乐标题',
  `music_url` varchar(255) DEFAULT '' COMMENT '音乐链接',
  `music_image` varchar(255) DEFAULT '' COMMENT '音乐缩略图链接',
  `music_desc` varchar(255) DEFAULT '' COMMENT '音乐描述',
  `video_title` varchar(100) DEFAULT '' COMMENT '视频标题',
  `video_url` varchar(255) DEFAULT '' COMMENT '视频URL',
  `video_desc` varchar(255) DEFAULT '' COMMENT '视频描述',
  `news_id` bigint(20) unsigned DEFAULT NULL COMMENT '图文ID',
  `sort` bigint(20) unsigned DEFAULT '0' COMMENT '排序字段',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0 禁用，1 启用',
  `create_by` bigint(20) unsigned DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_keys_appid` (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信关键字' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_menu`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `index` bigint(20) DEFAULT NULL,
  `pindex` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `type` varchar(24) NOT NULL DEFAULT '' COMMENT '菜单类型 null主菜单 link链接 keys关键字',
  `name` varchar(256) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `content` text NOT NULL COMMENT '文字内容',
  `sort` bigint(20) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(0禁用1启用)',
  `create_by` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_menu_pindex` (`pindex`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信菜单配置' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_news`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `media_id` varchar(100) DEFAULT '' COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT '' COMMENT '永久素材显示URL',
  `article_id` varchar(60) DEFAULT '' COMMENT '关联图文ID，用，号做分割',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`),
  KEY `index_wechat_news_artcle_id` (`article_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信图文表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_news_article`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_news_article` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `title` varchar(50) DEFAULT '' COMMENT '素材标题',
  `local_url` varchar(300) DEFAULT '' COMMENT '永久素材显示URL',
  `show_cover_pic` tinyint(4) unsigned DEFAULT '0' COMMENT '是否显示封面 0不显示，1 显示',
  `author` varchar(20) DEFAULT '' COMMENT '作者',
  `digest` varchar(300) DEFAULT '' COMMENT '摘要内容',
  `content` longtext COMMENT '图文内容',
  `content_source_url` varchar(200) DEFAULT '' COMMENT '图文消息原文地址',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信素材表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_news_image`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_news_image` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `md5` varchar(32) DEFAULT '' COMMENT '文件md5',
  `local_url` varchar(300) DEFAULT '' COMMENT '本地文件链接',
  `media_url` varchar(300) DEFAULT '' COMMENT '远程图片链接',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_news_image_md5` (`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信服务器图片' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_wechat_news_media`
--

CREATE TABLE IF NOT EXISTS `diygw_wechat_news_media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL DEFAULT '1' COMMENT '公众号ID',
  `appid` varchar(100) DEFAULT '' COMMENT '公众号ID',
  `md5` varchar(32) DEFAULT '' COMMENT '文件md5',
  `type` varchar(20) DEFAULT '' COMMENT '媒体类型',
  `media_id` varchar(100) DEFAULT '' COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT '' COMMENT '本地文件链接',
  `media_url` varchar(300) DEFAULT '' COMMENT '远程图片链接',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信素材表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
