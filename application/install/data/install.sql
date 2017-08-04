-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 巿08 暿04 敿10:20
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `diygw111`
--

-- --------------------------------------------------------

--
-- 表的结构 `diygw_action`
--

DROP TABLE IF EXISTS `diygw_action`;
CREATE TABLE IF NOT EXISTS `diygw_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text COMMENT '行为规则',
  `log` text COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表' AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `diygw_action`
--

INSERT INTO `diygw_action` (`id`, `name`, `title`, `remark`, `rule`, `log`, `type`, `status`, `update_time`) VALUES
(1, 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了后台', 1, 0, 1387181220),
(2, 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', 2, 0, 1380173180),
(3, 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', 2, 1, 1383285646),
(4, 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\r\n表[model]，记录编号[record]。', 2, 0, 1386139726),
(5, 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', 2, 0, 1383285551),
(6, 'update_config', '更新配置', '新增或修改或删除配置', '', '', 1, 1, 1383294988),
(7, 'update_model', '更新模型', '新增或修改模型', '', '', 1, 1, 1383295057),
(8, 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', 1, 1, 1383295963),
(9, 'update_channel', '更新导航', '新增或修改或删除导航', '', '', 1, 1, 1383296301),
(10, 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', 1, 1, 1383296392),
(11, 'update_category', '更新分类', '新增或修改或删除分类', '', '', 1, 1, 1383296765);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_action_log`
--

DROP TABLE IF EXISTS `diygw_action_log`;
CREATE TABLE IF NOT EXISTS `diygw_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表' AUTO_INCREMENT=42 ;

--
-- 转存表中的数据 `diygw_action_log`
--

INSERT INTO `diygw_action_log` (`id`, `action_id`, `user_id`, `action_ip`, `model`, `record_id`, `remark`, `status`, `create_time`) VALUES
(1, 11, 1, 2130706433, 'category', 42, '操作url：/index.php/admin/category/edit.html', 1, 1495985311),
(2, 11, 1, 2130706433, 'category', 2, '操作url：/index.php/admin/category/edit.html', 1, 1495985324),
(3, 11, 1, 2130706433, 'category', 2, '操作url：/index.php/admin/category/edit.html', 1, 1495985341),
(4, 11, 1, 2130706433, 'category', 42, '操作url：/index.php/admin/category/edit.html', 1, 1495985352),
(5, 11, 1, 2130706433, 'category', 1, '操作url：/index.php/admin/category/edit.html', 1, 1495985417),
(6, 11, 1, 2130706433, 'category', 42, '操作url：/index.php/admin/category/edit.html', 1, 1496049639),
(7, 11, 1, 2130706433, 'category', 42, '操作url：/index.php/admin/category/edit.html', 1, 1496049921),
(8, 11, 1, 2130706433, 'category', 43, '操作url：/index.php/admin/category/edit.html', 1, 1496049934),
(9, 11, 1, 2130706433, 'category', 2, '操作url：/index.php/admin/category/edit.html', 1, 1496049946),
(10, 7, 1, 2130706433, 'model', 2, '操作url：/index.php/admin/model/update.html', 1, 1496060930),
(11, 7, 1, 2130706433, 'model', 25, '操作url：/index.php/admin/model/update.html', 1, 1496071522),
(12, 8, 1, 2130706433, 'attribute', 291, '操作url：/index.php/admin/attribute/remove/id/291.html', 1, 1496071908),
(13, 7, 1, 2130706433, 'model', 25, '操作url：/index.php/admin/model/update.html', 1, 1496234101),
(14, 8, 1, 2130706433, 'attribute', 297, '操作url：/index.php/admin/attribute/update.html', 1, 1496234225),
(15, 7, 1, 2130706433, 'model', 25, '操作url：/index.php/admin/model/update.html', 1, 1496234591),
(16, 7, 1, 2130706433, 'model', 25, '操作url：/index.php/admin/model/update.html', 1, 1496234953),
(17, 7, 1, 2130706433, 'model', 25, '操作url：/index.php/admin/model/update.html', 1, 1496235024),
(18, 7, 1, 2130706433, 'model', 25, '操作url：/index.php/admin/model/update.html', 1, 1496235134),
(19, 8, 1, 2130706433, 'attribute', 293, '操作url：/index.php/admin/attribute/update.html', 1, 1496239765),
(20, 11, 1, 2130706433, 'category', 42, '操作url：/index.php/admin/category/edit.html', 1, 1496241183),
(21, 8, 1, 2130706433, 'attribute', 292, '操作url：/index.php/admin/attribute/update.html', 1, 1496241273),
(22, 8, 1, 2130706433, 'attribute', 292, '操作url：/index.php/admin/attribute/update.html', 1, 1496242348),
(23, 8, 1, 2130706433, 'attribute', 298, '操作url：/index.php/admin/attribute/update.html', 1, 1496243044),
(24, 11, 1, 2130706433, 'category', 43, '操作url：/index.php/admin/category/edit.html', 1, 1496283614),
(25, 11, 1, 2130706433, 'category', 2, '操作url：/index.php/admin/category/edit.html', 1, 1496283626),
(26, 7, 1, 2130706433, 'model', 26, '操作url：/index.php/admin/model/update.html', 1, 1496285189),
(27, 8, 1, 2130706433, 'attribute', 301, '操作url：/index.php/admin/attribute/remove/id/301.html', 1, 1496285343),
(28, 7, 1, 2130706433, 'model', 26, '操作url：/index.php/admin/model/update.html', 1, 1496285941),
(29, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/add.html', 1, 1496286194),
(30, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/edit.html', 1, 1496286213),
(31, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/edit.html', 1, 1496286322),
(32, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/edit.html', 1, 1496286431),
(33, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/edit.html', 1, 1496286760),
(34, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/edit.html', 1, 1496286819),
(35, 7, 1, 2130706433, 'model', 26, '操作url：/index.php/admin/model/update.html', 1, 1496286918),
(36, 10, 1, 2130706433, 'Menu', 129, '操作url：/index.php/admin/menu/edit.html', 1, 1496286979),
(37, 10, 1, 2130706433, 'Menu', 130, '操作url：/index.php/admin/menu/add.html', 1, 1501749151),
(38, 10, 1, 2130706433, 'Menu', 131, '操作url：/index.php/admin/menu/add.html', 1, 1501749202),
(39, 10, 1, 2130706433, 'Menu', 1, '操作url：/index.php/admin/menu/edit.html', 1, 1501749219),
(40, 10, 1, 2130706433, 'Menu', 131, '操作url：/index.php/admin/menu/edit.html', 1, 1501749227),
(41, 10, 1, 2130706433, 'Menu', 162, '操作url：/admin/menu/add.html', 1, 1501837310);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_addons`
--

DROP TABLE IF EXISTS `diygw_addons`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='插件表' AUTO_INCREMENT=61 ;

--
-- 转存表中的数据 `diygw_addons`
--

INSERT INTO `diygw_addons` (`id`, `name`, `title`, `description`, `status`, `config`, `author`, `version`, `create_time`, `has_adminlist`) VALUES
(56, 'digg', 'Digg插件', '网上通用的文章顶一下，踩一下插件（不支持后台作弊修改数据）。', 1, '{"good_tip":"\\u8fd9\\u6587\\u7ae0\\u4e0d\\u9519","bad_tip":"\\u8fd9\\u6587\\u7ae0\\u5f88\\u5dee","stop_repeat_tip":"\\u60a8\\u5df2\\u7ecf\\u6295\\u8fc7\\u7968\\u4e86\\uff0c\\u611f\\u8c22\\u60a8\\u7684\\u53c2\\u4e0e\\uff01","post_sucess_tip":"\\u6295\\u7968\\u6210\\u529f\\uff01","post_error_tip":"\\u5b32\\u4f60\\u7684,\\u56e7^__^,\\u4e0d\\u662f\\u521a\\u521a\\u9876\\u8fc7\\u5417\\uff01\\uff01"}', 'thinkphp', '0.3', 1479981518, 0),
(39, 'editor', '前台编辑器', '用于增强整站长文本的输入和显示', 0, '{"editor_type":"1","editor_wysiwyg":1,"editor_height":"300px","editor_resize_type":"1"}', 'thinkphp', '0.1', 1478444756, 0),
(40, 'devteam', '开发团队信息', '开发团队成员信息', 1, '{"title":"diygw\\u5f00\\u53d1\\u56e2\\u961f","width":"2","display":"1"}', 'thinkphp', '0.1', 1478444759, 0),
(41, 'editorforadmin', '后台编辑器', '用于增强整站长文本的输入和显示', 1, '{"editor_type":"2","editor_wysiwyg":"2","editor_markdownpreview":"1","editor_height":"500px","editor_resize_type":"1"}', 'thinkphp', '0.2', 1478523762, 0),
(43, 'systeminfo', '系统环境信息', '用于显示一些服务器的信息', 1, '{"title":"\\u7cfb\\u7edf\\u4fe1\\u606f","width":"2","display":"1"}', 'thinkphp', '0.1', 1478523796, 0),
(45, 'sitestat', '站点统计信息', '统计站点的基础信息', 1, '{"title":"\\u7cfb\\u7edf\\u4fe1\\u606f","width":"2","display":"1"}', 'thinkphp', '0.1', 1478523840, 0),
(46, 'socialcomment', '通用社交化评论', '集成了各种社交化评论插件，轻松集成到系统中。', 0, '{"comment_type":"1","comment_uid_youyan":"2118746","comment_short_name_duoshuo":"diygw.com","comment_form_pos_duoshuo":"buttom","comment_data_list_duoshuo":"10","comment_data_order_duoshuo":"asc"}', 'thinkphp', '0.1', 1478523917, 0),
(58, 'returntop', '返回顶部', '回到顶部美化，随机或指定显示，100款样式，每天一种换，天天都用新样式', 1, '{"random":"0","current":"1"}', 'thinkphp', '0.1', 1495863664, 0),
(59, 'returntop', '返回顶部', '回到顶部美化，随机或指定显示，100款样式，每天一种换，天天都用新样式', 1, '{"random":"1","current":"1"}', 'thinkphp', '0.1', 1495863668, 0);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_address`
--

DROP TABLE IF EXISTS `diygw_address`;
CREATE TABLE IF NOT EXISTS `diygw_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `gender` varchar(10) DEFAULT '1',
  `name` varchar(100) DEFAULT '' COMMENT '钩子名称',
  `tel` varchar(50) DEFAULT NULL,
  `is_def` tinyint(1) DEFAULT '0' COMMENT '描述',
  `user_id` varchar(100) DEFAULT NULL,
  `address` varchar(500) DEFAULT '1.00' COMMENT '类型',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- 转存表中的数据 `diygw_address`
--

INSERT INTO `diygw_address` (`id`, `gender`, `name`, `tel`, `is_def`, `user_id`, `address`, `update_time`, `status`) VALUES
(30, 'male', '邓志锋', '15219941518', 1, 'diygw_com', '广东省中山市松苑路1号', 0, 1),
(31, 'female', '邓女士', '15911111111', 0, 'diygw_com', '广东省中山市松苑路1号', 0, 1),
(32, 'male', '邓志锋', '13888888888', 0, 'diygw_com', '广东省中山市龙井小区', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_attachment`
--

DROP TABLE IF EXISTS `diygw_attachment`;
CREATE TABLE IF NOT EXISTS `diygw_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '附件显示名',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小',
  `dir` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级目录ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_attribute`
--

DROP TABLE IF EXISTS `diygw_attribute`;
CREATE TABLE IF NOT EXISTS `diygw_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `is_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `valids` text,
  `validate_rule` varchar(255) NOT NULL DEFAULT '',
  `validate_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `error_info` varchar(100) NOT NULL DEFAULT '',
  `validate_type` varchar(25) NOT NULL DEFAULT '',
  `auto_rule` varchar(100) NOT NULL DEFAULT '',
  `auto_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_type` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模型属性表' AUTO_INCREMENT=1958 ;

--
-- 转存表中的数据 `diygw_attribute`
--

INSERT INTO `diygw_attribute` (`id`, `name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `valids`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`) VALUES
(1, 'uid', '用户ID', 'int(10) unsigned NOT NULL ', 'num', '0', '', 0, '', 1, 0, 1, 1384508362, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(2, 'name', '标识', 'char(40) NOT NULL ', 'string', '', '同一根节点下标识不重复', 1, '', 1, 0, 1, 1383894743, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(3, 'title', '标题', 'char(80) NOT NULL ', 'string', '', '文档标题', 1, '', 1, 0, 1, 1383894778, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(4, 'category_id', '所属分类', 'int(10) unsigned NOT NULL ', 'string', '', '', 0, '', 1, 0, 1, 1384508336, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(5, 'description', '描述', 'char(140) NOT NULL ', 'textarea', '', '', 1, '', 1, 0, 1, 1383894927, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(6, 'root', '根节点', 'int(10) unsigned NOT NULL ', 'num', '0', '该文档的顶级文档编号', 0, '', 1, 0, 1, 1384508323, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(7, 'pid', '所属ID', 'int(10) unsigned NOT NULL ', 'num', '0', '父文档编号', 0, '', 1, 0, 1, 1384508543, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(8, 'model_id', '内容模型ID', 'tinyint(3) unsigned NOT NULL ', 'num', '0', '该文档所对应的模型', 0, '', 1, 0, 1, 1384508350, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(9, 'type', '内容类型', 'tinyint(3) unsigned NOT NULL ', 'select', '2', '', 1, '1:目录\r\n2:主题\r\n3:段落', 1, 0, 1, 1384511157, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(10, 'position', '推荐位', 'smallint(5) unsigned NOT NULL ', 'checkbox', '0', '多个推荐则将其推荐值相加', 1, '[DOCUMENT_POSITION]', 1, 0, 1, 1383895640, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(11, 'link_id', '外链', 'int(10) unsigned NOT NULL ', 'num', '0', '0-非外链，大于0-外链ID,需要函数进行链接与编号的转换', 1, '', 1, 0, 1, 1383895757, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(12, 'cover_id', '封面', 'int(10) unsigned NOT NULL ', 'picture', '0', '0-无封面，大于0-封面图片ID，需要函数处理', 1, '', 1, 0, 1, 1384147827, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(13, 'display', '可见性', 'tinyint(3) unsigned NOT NULL ', 'radio', '1', '', 1, '0:不可见\r\n1:所有人可见', 1, 0, 1, 1386662271, 1383891233, NULL, '', 0, '', 'regex', '', 0, 'function'),
(14, 'deadline', '截至时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '0-永久有效', 1, '', 1, 0, 1, 1387163248, 1383891233, NULL, '', 0, '', 'regex', '', 0, 'function'),
(15, 'attach', '附件数量', 'tinyint(3) unsigned NOT NULL ', 'num', '0', '', 0, '', 1, 0, 1, 1387260355, 1383891233, NULL, '', 0, '', 'regex', '', 0, 'function'),
(16, 'view', '浏览量', 'int(10) unsigned NOT NULL ', 'num', '0', '', 1, '', 1, 0, 1, 1383895835, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(17, 'comment', '评论数', 'int(10) unsigned NOT NULL ', 'num', '0', '', 1, '', 1, 0, 1, 1383895846, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(18, 'extend', '扩展统计字段', 'int(10) unsigned NOT NULL ', 'num', '0', '根据需求自行使用', 0, '', 1, 0, 1, 1384508264, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(19, 'level', '优先级', 'int(10) unsigned NOT NULL ', 'num', '0', '越高排序越靠前', 1, '', 1, 0, 1, 1383895894, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(20, 'create_time', '创建时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '', 1, '', 1, 0, 1, 1383895903, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(21, 'update_time', '更新时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '', 0, '', 1, 0, 1, 1384508277, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(22, 'status', '数据状态', 'tinyint(4) NOT NULL ', 'radio', '0', '', 0, '-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿', 1, 0, 1, 1384508496, 1383891233, NULL, '', 0, '', '', '', 0, ''),
(23, 'parse', '内容解析类型', 'tinyint(3) unsigned NOT NULL ', 'select', '0', '', 0, '0:html\r\n1:ubb\r\n2:markdown', 2, 0, 1, 1384511049, 1383891243, NULL, '', 0, '', '', '', 0, ''),
(24, 'content', '文章内容', 'text NOT NULL ', 'editor', '', '', 1, '', 2, 0, 1, 1477845595, 1383891243, NULL, '', 3, '', 'regex', '', 0, 'function'),
(25, 'template', '详情页显示模板', 'varchar(100) NOT NULL ', 'string', '', '参照display方法参数的定义', 1, '', 2, 0, 1, 1383896190, 1383891243, NULL, '', 0, '', '', '', 0, ''),
(26, 'bookmark', '收藏数', 'int(10) unsigned NOT NULL ', 'num', '0', '', 1, '', 2, 0, 1, 1383896103, 1383891243, NULL, '', 0, '', '', '', 0, ''),
(27, 'parse', '内容解析类型', 'tinyint(3) unsigned NOT NULL ', 'select', '0', '', 0, '0:html\r\n1:ubb\r\n2:markdown', 3, 0, 1, 1387260461, 1383891252, NULL, '', 0, '', 'regex', '', 0, 'function'),
(28, 'content', '下载详细描述', 'text NOT NULL ', 'editor', '', '', 1, '', 3, 0, 1, 1383896438, 1383891252, NULL, '', 0, '', '', '', 0, ''),
(29, 'template', '详情页显示模板', 'varchar(100) NOT NULL ', 'string', '', '', 1, '', 3, 0, 1, 1383896429, 1383891252, NULL, '', 0, '', '', '', 0, ''),
(30, 'file_id', '文件ID', 'int(10) unsigned NOT NULL ', 'file', '0', '需要函数处理', 1, '', 3, 0, 1, 1383896415, 1383891252, NULL, '', 0, '', '', '', 0, ''),
(31, 'download', '下载次数', 'int(10) unsigned NOT NULL ', 'num', '0', '', 1, '', 3, 0, 1, 1383896380, 1383891252, NULL, '', 0, '', '', '', 0, ''),
(32, 'size', '文件大小', 'bigint(20) unsigned NOT NULL ', 'num', '0', '单位bit', 1, '', 3, 0, 1, 1383896371, 1383891252, NULL, '', 0, '', '', '', 0, ''),
(288, 'keywords', 'Tags关键词', 'varchar(40) NOT NULL', 'string', '', ' 多个之间用空格分隔', 1, '', 1, 0, 0, 1479963928, 0, NULL, '', 3, '', 'regex', '', 3, 'function'),
(289, 'score', '下载积分', 'mediumint(8) UNSIGNED NOT NULL', 'num', '0', '下载文件所需积分', 1, '', 3, 0, 0, 0, 0, NULL, '', 3, '', 'regex', '', 3, 'function'),
(290, 'id', '主键', 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT', 'num', '', '', 0, '', 25, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(295, 'name', '标题', 'varchar(255) NOT NULL', 'string', '', '', 1, '', 25, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(292, 'category_id', '分类ID', 'int(10) UNSIGNED NOT NULL', 'num', '', '', 0, '', 25, 0, 0, 1496242348, 0, NULL, '', 3, '', '', '', 3, ''),
(293, 'img', '图片', 'int(10) UNSIGNED NOT NULL', 'pictures', '', '', 1, '', 25, 0, 0, 1496239765, 0, NULL, '', 3, '', '', '', 3, ''),
(294, 'remark', '描述', 'varchar(255) NOT NULL', 'string', '', '', 1, '', 25, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(296, 'price', '价格', 'decimal(5,2) NOT NULL', 'pice', '', '', 1, '', 25, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(297, 'status', '状态', 'char(10) NOT NULL', 'radio', '1', '', 1, '0:无效\r\n1:有效', 25, 0, 0, 1496234225, 0, NULL, '', 3, '', '', '', 3, ''),
(298, 'update_time', '更新时间', 'int(10) NOT NULL', 'datetime', '0', '', 0, '', 25, 0, 0, 1496243043, 0, NULL, '', 3, '', '', '', 3, ''),
(299, 'content', '内容', 'text NOT NULL', 'editor', '', '', 1, '', 25, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(300, 'name', '图片名称', 'varchar(255) NOT NULL', 'string', '', '', 1, '', 26, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(302, 'img', '图片地址', 'varchar(1000) UNSIGNED NOT NULL', 'pictures', '', '', 1, '', 26, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(303, 'status', '状态', 'char(10) NOT NULL', 'radio', '', '', 1, '0:无效\r\n1:有效', 26, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(304, 'update_time', '更新时间', 'int(10) NOT NULL', 'datetime', '', '', 1, '', 26, 0, 0, 0, 0, NULL, '', 3, '', '', '', 3, ''),
(1945, 'name_330468', '标题', 'varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'string', '0', '', 0, '', 812, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1946, 'name_972311', '内容', 'varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'string', '0', '', 0, '', 812, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1947, 'name_842187', '封面', 'varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'textarea', '0', '', 0, '', 812, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1954, 'name_277385', '标题', 'varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'string', '0', '', 0, '', 815, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1955, 'name_288441', '内容', 'varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'string', '0', '', 0, '', 815, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1956, 'name_453443', '选择', 'varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'select', '0', '', 0, '', 815, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1957, 'name_807009', '封面', 'varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'textarea', '0', '', 0, '', 815, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1952, 'name_291719', '标题', 'varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'string', '0', '', 0, '', 814, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, ''),
(1953, 'name_937247', '封面', 'varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL', 'textarea', '0', '', 0, '', 814, 0, 1, 2017, 2017, NULL, '', 0, '', '', '', 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_auth_extend`
--

DROP TABLE IF EXISTS `diygw_auth_extend`;
CREATE TABLE IF NOT EXISTS `diygw_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';

--
-- 转存表中的数据 `diygw_auth_extend`
--

INSERT INTO `diygw_auth_extend` (`group_id`, `extend_id`, `type`) VALUES
(1, 1, 1),
(1, 1, 2),
(1, 2, 2),
(1, 3, 2),
(2, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_auth_group`
--

DROP TABLE IF EXISTS `diygw_auth_group`;
CREATE TABLE IF NOT EXISTS `diygw_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `diygw_auth_group`
--

INSERT INTO `diygw_auth_group` (`id`, `module`, `type`, `title`, `description`, `status`, `rules`) VALUES
(1, 'admin', 1, '默认用户组', '', 1, '1,2,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,100,102,103'),
(2, 'admin', 1, '测试用户', '测试用户', 1, '1,2,7,8,9,10,11,12,13,14,15,16,17,18,26,79,88,195');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_auth_group_access`
--

DROP TABLE IF EXISTS `diygw_auth_group_access`;
CREATE TABLE IF NOT EXISTS `diygw_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `diygw_auth_group_access`
--

INSERT INTO `diygw_auth_group_access` (`uid`, `group_id`) VALUES
(2, 1);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_auth_rule`
--

DROP TABLE IF EXISTS `diygw_auth_rule`;
CREATE TABLE IF NOT EXISTS `diygw_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=221 ;

--
-- 转存表中的数据 `diygw_auth_rule`
--

INSERT INTO `diygw_auth_rule` (`id`, `module`, `type`, `name`, `title`, `status`, `condition`) VALUES
(1, 'admin', 2, 'admin/Index/index', '首页', 1, ''),
(2, 'admin', 2, 'admin/Article/index', '内容', 1, ''),
(3, 'admin', 2, 'admin/User/index', '用户', 1, ''),
(4, 'admin', 2, 'admin/Addons/index', '扩展', 1, ''),
(5, 'admin', 2, 'admin/Config/group', '系统', 1, ''),
(7, 'admin', 1, 'admin/article/add', '新增', 1, ''),
(8, 'admin', 1, 'admin/article/edit', '编辑', 1, ''),
(9, 'admin', 1, 'admin/article/setStatus', '改变状态', 1, ''),
(10, 'admin', 1, 'admin/article/update', '保存', 1, ''),
(11, 'admin', 1, 'admin/article/autoSave', '保存草稿', 1, ''),
(12, 'admin', 1, 'admin/article/move', '移动', 1, ''),
(13, 'admin', 1, 'admin/article/copy', '复制', 1, ''),
(14, 'admin', 1, 'admin/article/paste', '粘贴', 1, ''),
(15, 'admin', 1, 'admin/article/permit', '还原', 1, ''),
(16, 'admin', 1, 'admin/article/clear', '清空', 1, ''),
(17, 'admin', 1, 'admin/Article/examine', '审核列表', 1, ''),
(18, 'admin', 1, 'admin/article/recycle', '回收站', 1, ''),
(19, 'admin', 1, 'admin/User/addaction', '新增用户行为', 1, ''),
(20, 'admin', 1, 'admin/User/editaction', '编辑用户行为', 1, ''),
(21, 'admin', 1, 'admin/User/saveAction', '保存用户行为', 1, ''),
(22, 'admin', 1, 'admin/User/setStatus', '变更行为状态', 1, ''),
(23, 'admin', 1, 'admin/User/changeStatus?method=forbidUser', '禁用会员', 1, ''),
(24, 'admin', 1, 'admin/User/changeStatus?method=resumeUser', '启用会员', 1, ''),
(25, 'admin', 1, 'admin/User/changeStatus?method=deleteUser', '删除会员', 1, ''),
(26, 'admin', 1, 'admin/User/index', '用户信息', 1, ''),
(27, 'admin', 1, 'admin/User/action', '用户行为', 1, ''),
(28, 'admin', 1, 'admin/AuthManager/changeStatus?method=deleteGroup', '删除', 1, ''),
(29, 'admin', 1, 'admin/AuthManager/changeStatus?method=forbidGroup', '禁用', 1, ''),
(30, 'admin', 1, 'admin/AuthManager/changeStatus?method=resumeGroup', '恢复', 1, ''),
(31, 'admin', 1, 'admin/AuthManager/createGroup', '新增', 1, ''),
(32, 'admin', 1, 'admin/AuthManager/editGroup', '编辑', 1, ''),
(33, 'admin', 1, 'admin/AuthManager/writeGroup', '保存用户组', 1, ''),
(34, 'admin', 1, 'admin/AuthManager/group', '授权', 1, ''),
(35, 'admin', 1, 'admin/AuthManager/access', '访问授权', 1, ''),
(36, 'admin', 1, 'admin/AuthManager/user', '成员授权', 1, ''),
(37, 'admin', 1, 'admin/AuthManager/removeFromGroup', '解除授权', 1, ''),
(38, 'admin', 1, 'admin/AuthManager/addToGroup', '保存成员授权', 1, ''),
(39, 'admin', 1, 'admin/AuthManager/category', '分类授权', 1, ''),
(40, 'admin', 1, 'admin/AuthManager/addToCategory', '保存分类授权', 1, ''),
(41, 'admin', 1, 'admin/AuthManager/index', '权限管理', 1, ''),
(42, 'admin', 1, 'admin/Addons/create', '创建', 1, ''),
(43, 'admin', 1, 'admin/Addons/checkForm', '检测创建', 1, ''),
(44, 'admin', 1, 'admin/Addons/preview', '预览', 1, ''),
(45, 'admin', 1, 'admin/Addons/build', '快速生成插件', 1, ''),
(46, 'admin', 1, 'admin/Addons/config', '设置', 1, ''),
(47, 'admin', 1, 'admin/Addons/disable', '禁用', 1, ''),
(48, 'admin', 1, 'admin/Addons/enable', '启用', 1, ''),
(49, 'admin', 1, 'admin/Addons/install', '安装', 1, ''),
(50, 'admin', 1, 'admin/Addons/uninstall', '卸载', 1, ''),
(51, 'admin', 1, 'admin/Addons/saveconfig', '更新配置', 1, ''),
(52, 'admin', 1, 'admin/Addons/adminList', '插件后台列表', 1, ''),
(53, 'admin', 1, 'admin/Addons/execute', 'URL方式访问插件', 1, ''),
(54, 'admin', 1, 'admin/Addons/index', '插件管理', 1, ''),
(55, 'admin', 1, 'admin/Addons/hooks', '钩子管理', 1, ''),
(56, 'admin', 1, 'admin/model/add', '新增', 1, ''),
(57, 'admin', 1, 'admin/model/edit', '编辑', 1, ''),
(58, 'admin', 1, 'admin/model/setStatus', '改变状态', 1, ''),
(59, 'admin', 1, 'admin/model/update', '保存数据', 1, ''),
(60, 'admin', 1, 'admin/Model/index', '模型管理', 1, ''),
(61, 'admin', 1, 'admin/Config/edit', '编辑', 1, ''),
(62, 'admin', 1, 'admin/Config/del', '删除', 1, ''),
(63, 'admin', 1, 'admin/Config/add', '新增', 1, ''),
(64, 'admin', 1, 'admin/Config/save', '保存', 1, ''),
(65, 'admin', 1, 'admin/Config/group', '网站设置', 1, ''),
(66, 'admin', 1, 'admin/Config/index', '配置管理', 1, ''),
(67, 'admin', 1, 'admin/Channel/add', '新增', 1, ''),
(68, 'admin', 1, 'admin/Channel/edit', '编辑', 1, ''),
(69, 'admin', 1, 'admin/Channel/del', '删除', 1, ''),
(70, 'admin', 1, 'admin/Channel/index', '导航管理', 1, ''),
(71, 'admin', 1, 'admin/Category/edit', '编辑', 1, ''),
(72, 'admin', 1, 'admin/Category/add', '新增', 1, ''),
(73, 'admin', 1, 'admin/Category/remove', '删除', 1, ''),
(74, 'admin', 1, 'admin/Category/index', '分类管理', 1, ''),
(75, 'admin', 1, 'Admin/file/upload', '上传控件', -1, ''),
(76, 'admin', 1, 'Admin/file/uploadPicture', '上传图片', -1, ''),
(77, 'admin', 1, 'Admin/file/download', '下载', -1, ''),
(79, 'admin', 1, 'admin/article/batchOperate', '导入', 1, ''),
(80, 'admin', 1, 'admin/Database/index?type=export', '备份数据库', 1, ''),
(81, 'admin', 1, 'admin/Database/index?type=import', '还原数据库', 1, ''),
(82, 'admin', 1, 'admin/Database/export', '备份', 1, ''),
(83, 'admin', 1, 'admin/Database/optimize', '优化表', 1, ''),
(84, 'admin', 1, 'admin/Database/repair', '修复表', 1, ''),
(86, 'admin', 1, 'admin/Database/import', '恢复', 1, ''),
(87, 'admin', 1, 'admin/Database/del', '删除', 1, ''),
(88, 'admin', 1, 'admin/User/add', '新增用户', 1, ''),
(89, 'admin', 1, 'admin/Attribute/index', '属性管理', 1, ''),
(90, 'admin', 1, 'admin/Attribute/add', '新增', 1, ''),
(91, 'admin', 1, 'admin/Attribute/edit', '编辑', 1, ''),
(92, 'admin', 1, 'admin/Attribute/setStatus', '改变状态', 1, ''),
(93, 'admin', 1, 'admin/Attribute/update', '保存数据', 1, ''),
(94, 'admin', 1, 'admin/AuthManager/modelauth', '模型授权', 1, ''),
(95, 'admin', 1, 'admin/AuthManager/addToModel', '保存模型授权', 1, ''),
(96, 'admin', 1, 'Admin/Category/move', '移动', -1, ''),
(97, 'admin', 1, 'Admin/Category/merge', '合并', -1, ''),
(98, 'admin', 1, 'Admin/Config/menu', '后台菜单管理', -1, ''),
(99, 'admin', 1, 'Admin/Article/mydocument', '内容', -1, ''),
(100, 'admin', 1, 'admin/Menu/index', '菜单管理', 1, ''),
(101, 'admin', 1, 'Admin/other', '其他', -1, ''),
(102, 'admin', 1, 'admin/Menu/add', '新增', 1, ''),
(103, 'admin', 1, 'admin/Menu/edit', '编辑', 1, ''),
(104, 'admin', 1, 'Admin/Think/lists?model=article', '文章管理', -1, ''),
(105, 'admin', 1, 'Admin/Think/lists?model=download', '下载管理', -1, ''),
(106, 'admin', 1, 'Admin/Think/lists?model=config', '配置管理', -1, ''),
(107, 'admin', 1, 'admin/Action/actionlog', '行为日志', 1, ''),
(108, 'admin', 1, 'admin/User/updatePassword', '修改密码', 1, ''),
(109, 'admin', 1, 'admin/User/updateNickname', '修改昵称', 1, ''),
(110, 'admin', 1, 'admin/action/edit', '查看行为日志', 1, ''),
(111, 'admin', 2, 'Admin/article/index', '文档列表', -1, ''),
(112, 'admin', 2, 'Admin/article/add', '新增', -1, ''),
(113, 'admin', 2, 'Admin/article/edit', '编辑', -1, ''),
(114, 'admin', 2, 'Admin/article/setStatus', '改变状态', -1, ''),
(115, 'admin', 2, 'Admin/article/update', '保存', -1, ''),
(116, 'admin', 2, 'Admin/article/autoSave', '保存草稿', -1, ''),
(117, 'admin', 2, 'Admin/article/move', '移动', -1, ''),
(118, 'admin', 2, 'Admin/article/copy', '复制', -1, ''),
(119, 'admin', 2, 'Admin/article/paste', '粘贴', -1, ''),
(120, 'admin', 2, 'Admin/article/batchOperate', '导入', -1, ''),
(121, 'admin', 2, 'Admin/article/recycle', '回收站', -1, ''),
(122, 'admin', 2, 'Admin/article/permit', '还原', -1, ''),
(123, 'admin', 2, 'Admin/article/clear', '清空', -1, ''),
(124, 'admin', 2, 'Admin/User/add', '新增用户', -1, ''),
(125, 'admin', 2, 'Admin/User/action', '用户行为', -1, ''),
(126, 'admin', 2, 'Admin/User/addAction', '新增用户行为', -1, ''),
(127, 'admin', 2, 'Admin/User/editAction', '编辑用户行为', -1, ''),
(128, 'admin', 2, 'Admin/User/saveAction', '保存用户行为', -1, ''),
(129, 'admin', 2, 'Admin/User/setStatus', '变更行为状态', -1, ''),
(130, 'admin', 2, 'Admin/User/changeStatus?method=forbidUser', '禁用会员', -1, ''),
(131, 'admin', 2, 'Admin/User/changeStatus?method=resumeUser', '启用会员', -1, ''),
(132, 'admin', 2, 'Admin/User/changeStatus?method=deleteUser', '删除会员', -1, ''),
(133, 'admin', 2, 'Admin/AuthManager/index', '权限管理', -1, ''),
(134, 'admin', 2, 'Admin/AuthManager/changeStatus?method=deleteGroup', '删除', -1, ''),
(135, 'admin', 2, 'Admin/AuthManager/changeStatus?method=forbidGroup', '禁用', -1, ''),
(136, 'admin', 2, 'Admin/AuthManager/changeStatus?method=resumeGroup', '恢复', -1, ''),
(137, 'admin', 2, 'Admin/AuthManager/createGroup', '新增', -1, ''),
(138, 'admin', 2, 'Admin/AuthManager/editGroup', '编辑', -1, ''),
(139, 'admin', 2, 'Admin/AuthManager/writeGroup', '保存用户组', -1, ''),
(140, 'admin', 2, 'Admin/AuthManager/group', '授权', -1, ''),
(141, 'admin', 2, 'Admin/AuthManager/access', '访问授权', -1, ''),
(142, 'admin', 2, 'Admin/AuthManager/user', '成员授权', -1, ''),
(143, 'admin', 2, 'Admin/AuthManager/removeFromGroup', '解除授权', -1, ''),
(144, 'admin', 2, 'Admin/AuthManager/addToGroup', '保存成员授权', -1, ''),
(145, 'admin', 2, 'Admin/AuthManager/category', '分类授权', -1, ''),
(146, 'admin', 2, 'Admin/AuthManager/addToCategory', '保存分类授权', -1, ''),
(147, 'admin', 2, 'Admin/AuthManager/modelauth', '模型授权', -1, ''),
(148, 'admin', 2, 'Admin/AuthManager/addToModel', '保存模型授权', -1, ''),
(149, 'admin', 2, 'Admin/Addons/create', '创建', -1, ''),
(150, 'admin', 2, 'Admin/Addons/checkForm', '检测创建', -1, ''),
(151, 'admin', 2, 'Admin/Addons/preview', '预览', -1, ''),
(152, 'admin', 2, 'Admin/Addons/build', '快速生成插件', -1, ''),
(153, 'admin', 2, 'Admin/Addons/config', '设置', -1, ''),
(154, 'admin', 2, 'Admin/Addons/disable', '禁用', -1, ''),
(155, 'admin', 2, 'Admin/Addons/enable', '启用', -1, ''),
(156, 'admin', 2, 'Admin/Addons/install', '安装', -1, ''),
(157, 'admin', 2, 'Admin/Addons/uninstall', '卸载', -1, ''),
(158, 'admin', 2, 'Admin/Addons/saveconfig', '更新配置', -1, ''),
(159, 'admin', 2, 'Admin/Addons/adminList', '插件后台列表', -1, ''),
(160, 'admin', 2, 'Admin/Addons/execute', 'URL方式访问插件', -1, ''),
(161, 'admin', 2, 'Admin/Addons/hooks', '钩子管理', -1, ''),
(162, 'admin', 2, 'Admin/Model/index', '模型管理', -1, ''),
(163, 'admin', 2, 'Admin/model/add', '新增', -1, ''),
(164, 'admin', 2, 'Admin/model/edit', '编辑', -1, ''),
(165, 'admin', 2, 'Admin/model/setStatus', '改变状态', -1, ''),
(166, 'admin', 2, 'Admin/model/update', '保存数据', -1, ''),
(167, 'admin', 2, 'Admin/Attribute/index', '属性管理', -1, ''),
(168, 'admin', 2, 'Admin/Attribute/add', '新增', -1, ''),
(169, 'admin', 2, 'Admin/Attribute/edit', '编辑', -1, ''),
(170, 'admin', 2, 'Admin/Attribute/setStatus', '改变状态', -1, ''),
(171, 'admin', 2, 'Admin/Attribute/update', '保存数据', -1, ''),
(172, 'admin', 2, 'Admin/Config/index', '配置管理', -1, ''),
(173, 'admin', 2, 'Admin/Config/edit', '编辑', -1, ''),
(174, 'admin', 2, 'Admin/Config/del', '删除', -1, ''),
(175, 'admin', 2, 'Admin/Config/add', '新增', -1, ''),
(176, 'admin', 2, 'Admin/Config/save', '保存', -1, ''),
(177, 'admin', 2, 'Admin/Menu/index', '菜单管理', -1, ''),
(178, 'admin', 2, 'Admin/Channel/index', '导航管理', -1, ''),
(179, 'admin', 2, 'Admin/Channel/add', '新增', -1, ''),
(180, 'admin', 2, 'Admin/Channel/edit', '编辑', -1, ''),
(181, 'admin', 2, 'Admin/Channel/del', '删除', -1, ''),
(182, 'admin', 2, 'Admin/Category/index', '分类管理', -1, ''),
(183, 'admin', 2, 'Admin/Category/edit', '编辑', -1, ''),
(184, 'admin', 2, 'Admin/Category/add', '新增', -1, ''),
(185, 'admin', 2, 'Admin/Category/remove', '删除', -1, ''),
(186, 'admin', 2, 'Admin/Category/move', '移动', -1, ''),
(187, 'admin', 2, 'Admin/Category/merge', '合并', -1, ''),
(188, 'admin', 2, 'Admin/Database/index?type=export', '备份数据库', -1, ''),
(189, 'admin', 2, 'Admin/Database/export', '备份', -1, ''),
(190, 'admin', 2, 'Admin/Database/optimize', '优化表', -1, ''),
(191, 'admin', 2, 'Admin/Database/repair', '修复表', -1, ''),
(192, 'admin', 2, 'Admin/Database/index?type=import', '还原数据库', -1, ''),
(193, 'admin', 2, 'Admin/Database/import', '恢复', -1, ''),
(194, 'admin', 2, 'Admin/Database/del', '删除', -1, ''),
(195, 'admin', 2, 'admin/other', '其他', 1, ''),
(196, 'admin', 2, 'Admin/Menu/add', '新增', -1, ''),
(197, 'admin', 2, 'Admin/Menu/edit', '编辑', -1, ''),
(198, 'admin', 2, 'Admin/Think/lists?model=article', '应用', -1, ''),
(199, 'admin', 2, 'Admin/Think/lists?model=download', '下载管理', -1, ''),
(200, 'admin', 2, 'Admin/Think/lists?model=config', '应用', -1, ''),
(201, 'admin', 2, 'Admin/Action/actionlog', '行为日志', -1, ''),
(202, 'admin', 2, 'Admin/User/updatePassword', '修改密码', -1, ''),
(203, 'admin', 2, 'Admin/User/updateNickname', '修改昵称', -1, ''),
(204, 'admin', 2, 'Admin/action/edit', '查看行为日志', -1, ''),
(205, 'admin', 1, 'admin/think/add', '新增数据', 1, ''),
(206, 'admin', 1, 'admin/think/edit', '编辑数据', 1, ''),
(207, 'admin', 1, 'admin/Menu/import', '导入', 1, ''),
(208, 'admin', 1, 'admin/Model/generate', '生成', 1, ''),
(209, 'admin', 1, 'admin/Addons/addHook', '新增钩子', 1, ''),
(210, 'admin', 1, 'admin/Addons/edithook', '编辑钩子', 1, ''),
(211, 'admin', 1, 'admin/Article/sort', '文档排序', 1, ''),
(212, 'admin', 1, 'admin/Config/sort', '排序', 1, ''),
(213, 'admin', 1, 'admin/Menu/sort', '排序', 1, ''),
(214, 'admin', 1, 'admin/Channel/sort', '排序', 1, ''),
(215, 'admin', 1, 'admin/Category/operate/type/move', '移动', 1, ''),
(216, 'admin', 1, 'admin/Category/operate/type/merge', '合并', 1, ''),
(217, 'admin', 1, 'admin/article/index', '文档列表', 1, ''),
(218, 'admin', 1, 'admin/think/lists', '数据列表', 1, ''),
(219, 'admin', 2, 'admin/Ymenu/index', 'two助手', -1, ''),
(220, 'admin', 1, 'admin/Think/lists?model=banner', '图片轮播', 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_category`
--

DROP TABLE IF EXISTS `diygw_category`;
CREATE TABLE IF NOT EXISTS `diygw_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(30) NOT NULL COMMENT '标志',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `list_row` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '列表每页行数',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO的网页标题',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `template_index` varchar(100) NOT NULL DEFAULT '' COMMENT '频道页模板',
  `template_lists` varchar(100) NOT NULL DEFAULT '' COMMENT '列表页模板',
  `template_detail` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页模板',
  `template_edit` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑页模板',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT '列表绑定模型',
  `model_sub` varchar(100) NOT NULL DEFAULT '' COMMENT '子文档绑定模型',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '允许发布的内容类型',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `allow_publish` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许发布内容',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可见性',
  `reply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回复',
  `check` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发布的文章是否需要审核',
  `reply_model` varchar(100) NOT NULL DEFAULT '',
  `extend` text COMMENT '扩展设置',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `icon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类图标',
  `groups` varchar(255) NOT NULL DEFAULT '' COMMENT '分组定义',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分类表' AUTO_INCREMENT=44 ;

--
-- 转存表中的数据 `diygw_category`
--

INSERT INTO `diygw_category` (`id`, `name`, `title`, `pid`, `sort`, `list_row`, `meta_title`, `keywords`, `description`, `template_index`, `template_lists`, `template_detail`, `template_edit`, `model`, `model_sub`, `type`, `link_id`, `allow_publish`, `display`, `reply`, `check`, `reply_model`, `extend`, `create_time`, `update_time`, `status`, `icon`, `groups`) VALUES
(1, 'diygw', 'DIY官网', 0, 0, 10, '', '', '', '', '', '', '', '1', '1', '1', 0, 0, 1, 0, 0, '1', '1', 1379474947, 1495985417, 1, 0, ''),
(2, 'xcx', '微信小程序', 1, 1, 10, '', '', '', '', '', '', '', '25', '25', '1', 0, 1, 1, 0, 0, '1', '1', 1379475028, 1496283626, 1, 0, ''),
(42, 'page', '单页动画', 1, 0, 10, '', '', '', '', '', '', '', '25', '25', '2,1', 0, 1, 1, 0, 0, '1', '1', 1495985287, 1496241182, 1, 0, ''),
(43, 'system', '原型设计', 1, 0, 10, '', '', '', '', '', '', '', '25', '25', '1', 0, 1, 1, 0, 0, '1', '1', 1495985386, 1496283614, 1, 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_channel`
--

DROP TABLE IF EXISTS `diygw_channel`;
CREATE TABLE IF NOT EXISTS `diygw_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级频道ID',
  `title` char(30) NOT NULL COMMENT '频道标题',
  `url` char(100) NOT NULL COMMENT '频道连接',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `target` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
  `dashboard_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000001282 ;

--
-- 转存表中的数据 `diygw_channel`
--

INSERT INTO `diygw_channel` (`id`, `pid`, `title`, `url`, `sort`, `create_time`, `update_time`, `status`, `target`, `dashboard_id`) VALUES
(1, 0, '首页', '/home/index', 1, 1379475111, 1379923177, 1, 0, NULL),
(2, 0, '博客', 'home/Article/index?category=diygw', 3, 1379475131, 1379483713, 1, 0, NULL),
(3, 0, '官网', 'http://www.diygw.com', 2, 1379475154, 1387163458, 1, 1, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_config`
--

DROP TABLE IF EXISTS `diygw_config`;
CREATE TABLE IF NOT EXISTS `diygw_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- 转存表中的数据 `diygw_config`
--

INSERT INTO `diygw_config` (`id`, `name`, `type`, `title`, `group`, `extra`, `remark`, `create_time`, `update_time`, `status`, `value`, `sort`) VALUES
(1, 'web_site_title', 1, '网站标题', 1, '', '网站标题前台显示标题', 1378898976, 1379235274, 1, 'DiyGw微信小程序管理框架', 1),
(2, 'web_site_description', 2, '网站描述', 1, '', '网站搜索引擎描述', 1378898976, 1379235841, 1, 'DiyGw微信小程序管理框架', 2),
(3, 'web_site_keyword', 2, '网站关键字', 1, '', '网站搜索引擎关键字', 1378898976, 1381390100, 1, 'ThinkPHP,diygw,DiyGw,微信小程序,PHP后台框架', 3),
(4, 'web_site_close', 4, '关闭站点', 1, '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', 1378898976, 1379235296, 1, '1', 4),
(9, 'config_type_list', 3, '配置类型列表', 4, '', '主要用于数据解析和页面表单的生成', 1378898976, 1379235348, 1, '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', 4),
(10, 'web_site_icp', 1, '网站备案号', 1, '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', 1378900335, 1379235859, 1, '', 5),
(11, 'document_position', 3, '文档推荐位', 2, '', '文档推荐位，推荐到多个位置KEY值相加即可', 1379053380, 1379235329, 1, '1:列表推荐\r\n2:频道推荐\r\n4:首页推荐', 7),
(12, 'document_display', 3, '文档可见性', 2, '', '文章可见性仅影响前台显示，后台不收影响', 1379056370, 1379235322, 1, '0:所有人可见\r\n1:仅注册会员可见\r\n2:仅管理员可见', 11),
(13, 'color_style', 4, '后台色系', 1, 'default_color:默认\r\nblue_color:紫罗兰', '后台颜色风格仅对默认主题有效', 1379122533, 1479986226, 1, 'default_color', 8),
(20, 'config_group_list', 3, '配置分组', 4, '', '配置分组', 1379228036, 1384418383, 1, '1:基本\r\n2:内容\r\n3:用户\r\n4:系统', 12),
(21, 'hooks_type', 3, '钩子的类型', 4, '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', 1379313397, 1379313407, 1, '1:视图\r\n2:控制器', 16),
(22, 'auth_config', 3, 'Auth配置', 4, '', '自定义Auth.class.php类配置', 1379409310, 1379409564, 1, 'auth_on:1\r\nauth_type:2', 22),
(23, 'open_draftbox', 4, '是否开启草稿功能', 2, '0:关闭草稿功能\r\n1:开启草稿功能\r\n', '新增文章时的草稿功能配置', 1379484332, 1379484591, 1, '1', 2),
(24, 'draft_aotosave_interval', 0, '自动保存草稿时间', 2, '', '自动保存草稿的时间间隔，单位：秒', 1379484574, 1386143323, 1, '60', 5),
(25, 'list_rows', 0, '后台每页记录数', 2, '', '后台数据每页显示记录数', 1379503896, 1380427745, 1, '10', 27),
(26, 'user_allow_register', 4, '是否允许用户注册', 3, '0:关闭注册\r\n1:允许注册', '是否开放用户注册', 1379504487, 1379504580, 1, '1', 8),
(27, 'codemirror_theme', 4, '预览插件的CodeMirror主题', 4, '3024-day:3024 day\r\n3024-night:3024 night\r\nambiance:ambiance\r\nbase16-dark:base16 dark\r\nbase16-light:base16 light\r\nblackboard:blackboard\r\ncobalt:cobalt\r\neclipse:eclipse\r\nelegant:elegant\r\nerlang-dark:erlang-dark\r\nlesser-dark:lesser-dark\r\nmidnight:midnight', '详情见CodeMirror官网', 1379814385, 1384740813, 1, 'ambiance', 9),
(28, 'data_backup_path', 1, '数据库备份根路径', 4, '', '路径必须以 / 结尾', 1381482411, 1381482411, 1, './static/data/', 14),
(29, 'data_backup_part_size', 0, '数据库备份卷大小', 4, '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', 1381482488, 1381729564, 1, '20971520', 19),
(30, 'data_backup_compress', 4, '数据库备份文件是否启用压缩', 4, '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', 1381713345, 1381729544, 1, '1', 24),
(31, 'data_backup_compress_level', 4, '数据库备份文件压缩级别', 4, '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', 1381713408, 1381713408, 1, '9', 29),
(32, 'develop_mode', 4, '开启开发者模式', 4, '0:关闭\r\n1:开启', '是否开启开发者模式', 1383105995, 1383291877, 1, '1', 30),
(33, 'allow_visit', 3, '不受限控制器方法', 0, '', '', 1386644047, 1386644741, 1, '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname\r\n10:file/uploadpicture', 6),
(34, 'deny_visit', 3, '超管专限控制器方法', 0, '', '仅超级管理员可访问的控制器方法', 1386644141, 1386644659, 1, '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', 10),
(35, 'reply_list_rows', 0, '回复列表每页条数', 2, '', '', 1386645376, 1387178083, 1, '10', 13),
(36, 'admin_allow_ip', 2, '后台允许访问IP', 4, '', '多个用逗号分隔，如果不配置表示不限制IP访问', 1387165454, 1387165553, 1, '', 31),
(37, 'app_trace', 4, '是否显示页面Trace', 4, '0:关闭\r\n1:开启', '是否显示页面Trace信息', 1387165685, 1387165685, 1, '0', 3),
(38, 'app_debug', 4, '应用调试模式', 4, '0:关闭\r\n1:开启', '网站正式部署建议关闭', 1478522232, 1478522395, 1, '1', 15),
(39, 'template.view_path', 1, '模板主题', 0, 'dd', '', 1479883093, 1479883093, 1, 'dd', 17),
(40, 'admin_view_path', 4, '后台模板主题', 1, 'default:默认 ', '添加主题请在配置管理添加', 1479986058, 1479991430, 1, 'default', 6),
(41, 'home_view_path', 4, '前台模板主题', 1, 'default:默认', '添加主题请在配置管理添加', 1479986147, 1479991437, 1, 'default', 7);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_content`
--

DROP TABLE IF EXISTS `diygw_content`;
CREATE TABLE IF NOT EXISTS `diygw_content` (
  `category_id` int(10) unsigned NOT NULL COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `img` varchar(1000) NOT NULL COMMENT '图片',
  `remark` varchar(200) DEFAULT NULL,
  `price` int(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` char(10) NOT NULL DEFAULT '1' COMMENT '状态',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `content` text NOT NULL COMMENT '内容',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属ID',
  `model_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=261 ;

--
-- 转存表中的数据 `diygw_content`
--

INSERT INTO `diygw_content` (`category_id`, `id`, `name`, `img`, `remark`, `price`, `status`, `update_time`, `content`, `pid`, `model_id`) VALUES
(42, 213, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', '强大的H5单页动画创作平台', 25, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 214, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 86, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 215, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', '强大的H5单页动画创作平台', 55, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 216, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', '强大的H5单页动画创作平台', 92, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 217, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 218, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 7, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 219, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', '强大的H5单页动画创作平台', 93, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 220, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', '强大的H5单页动画创作平台', 34, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 221, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 222, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', '强大的H5单页动画创作平台', 4, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 223, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 224, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', '强大的H5单页动画创作平台', 42, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(42, 225, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', '强大的H5单页动画创作平台', 4, '1', 0, '<p><br/></p><p><br/></p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p><br/></p><p>强大的H5单页动画创作平台</p><p><br/></p><p>任意组合的动画效果，实现超炫酷动画特效</p><p><br/></p><p><br/></p><p><a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>DIY官网助您打造专业交互设计页面</h2><p><br/></p><p>可视化页面动画创作，充分展现你的创意</p><p><br/></p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p><br/></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>基于Animate.css的CSS3动画效果</h2><p><br/></p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p><br/></p><p><br/></p><p><br/></p>', 0, 25),
(42, 226, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 70, '1', 0, '<p><br/></p><p><br/></p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p><br/></p><p>强大的H5单页动画创作平台</p><p><br/></p><p>任意组合的动画效果，实现超炫酷动画特效</p><p><br/></p><p><br/></p><p><a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>DIY官网助您打造专业交互设计页面</h2><p><br/></p><p>可视化页面动画创作，充分展现你的创意</p><p><br/></p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p><br/></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>基于Animate.css的CSS3动画效果</h2><p><br/></p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p><br/></p><p><br/></p><p><br/></p>', 0, 25),
(42, 227, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '强大的H5单页动画创作平台', 10, '1', 0, '<p><br/></p><p><br/></p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p><br/></p><p>强大的H5单页动画创作平台</p><p><br/></p><p>任意组合的动画效果，实现超炫酷动画特效</p><p><br/></p><p><br/></p><p><a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>DIY官网助您打造专业交互设计页面</h2><p><br/></p><p>可视化页面动画创作，充分展现你的创意</p><p><br/></p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p><br/></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>基于Animate.css的CSS3动画效果</h2><p><br/></p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p><br/></p><p><br/></p><p><br/></p>', 0, 25),
(42, 228, 'DiyGw单页动画设计', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', '强大的H5单页动画创作平台', 1, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p>\r\n			</p><p>强大的H5单页动画创作平台</p><p>\r\n			</p><p>任意组合的动画效果，实现超炫酷动画特效</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网助您打造专业交互设计页面</h2><p>\r\n			</p><p>可视化页面动画创作，充分展现你的创意</p><p>\r\n			</p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于Animate.css的CSS3动画效果</h2><p>\r\n			</p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p>\r\n		</p><p>\r\n	</p><p><br/></p>', 0, 25),
(43, 229, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com助您实现快速原型设计工具', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 230, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com助您实现快速原型设计工具', 17, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 231, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com助您实现快速原型设计工具', 20, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 232, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', 'DiyGw.com助您实现快速原型设计工具', 80, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 233, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com助您实现快速原型设计工具', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 234, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com助您实现快速原型设计工具', 67, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 235, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com助您实现快速原型设计工具', 76, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 236, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', 'DiyGw.com助您实现快速原型设计工具', 96, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 237, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com助您实现快速原型设计工具', 78, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 238, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com助您实现快速原型设计工具', 20, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 239, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com助您实现快速原型设计工具', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 240, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com助您实现快速原型设计工具', 31, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25);
INSERT INTO `diygw_content` (`category_id`, `id`, `name`, `img`, `remark`, `price`, `status`, `update_time`, `content`, `pid`, `model_id`) VALUES
(43, 241, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com助您实现快速原型设计工具', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 242, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com助您实现快速原型设计工具', 46, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 243, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com助您实现快速原型设计工具', 17, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(43, 244, 'DiyGw.com助您实现快速原型设计工具', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', 'DiyGw.com助您实现快速原型设计工具', 19, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您实现快速原型设计工具</h1><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', 0, 25),
(2, 245, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com微信小程序解决方案', 26, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 246, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com微信小程序解决方案', 12, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 247, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com微信小程序解决方案', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 248, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', 'DiyGw.com微信小程序解决方案', 72, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 249, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com微信小程序解决方案', 13, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 250, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com微信小程序解决方案', 36, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 251, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com微信小程序解决方案', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 252, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', 'DiyGw.com微信小程序解决方案', 4, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 253, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com微信小程序解决方案', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 254, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com微信小程序解决方案', 54, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 255, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com微信小程序解决方案', 3, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 256, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', 'DiyGw.com微信小程序解决方案', 5, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 257, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com微信小程序解决方案', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 258, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', 'DiyGw.com微信小程序解决方案', 18, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25),
(2, 259, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', 'DiyGw.com微信小程序解决方案', 10, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25);
INSERT INTO `diygw_content` (`category_id`, `id`, `name`, `img`, `remark`, `price`, `status`, `update_time`, `content`, `pid`, `model_id`) VALUES
(2, 260, 'DiyGw.com微信小程序解决方案', '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', 'DiyGw.com微信小程序解决方案', 8, '1', 0, '<p>\r\n		</p><p>\r\n			</p><h1>DIY官网助您上线微信小程序</h1><p>\r\n			</p><p>2017年最火热的移动互联网商机，深挖微信8亿用户</p><p>\r\n			</p><p>所见即所得设计工具 不会代码也能轻松制作微信小程序</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic2.png" title="DIY官网助您上线微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>工欲善其事必先利其器</h2><p>\r\n			</p><p class="">TO DO A GOOD JOB,ONE MUST FIRST SHARPEN ONE&#39;S TOOLS</p><p>\r\n			</p><p class="boxs-span clearfix">一切都那么简单</p><p>\r\n		</p><p>			\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic3.png" title="所见即所得设计工具 不会代码也能轻松制作微信小程序"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>基于微信小程序量身设计的WeUI组件库</h2><p>\r\n			</p><p class="text-left">WeUI是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身\r\n设计，令用户的使用感知更加统一。包含容器组件、FLEX布局、九宫格、选项卡、底部导航、滑块组件、图文列表、面板组件、文本链接、链接组件、图片组\r\n件、图标、进度条、按钮、单行文本、多行文本、单选列表、多选列表、开关选择、滑动条、上传组件、时间、下拉列表、单选列表等各式元素。</p><p>\r\n		</p><p>\r\n		</p><ul class="box3-img wow fadeInUp  animated list-paddingleft-2" style="padding-top: 20px; margin-bottom: 50px; visibility: visible; animation-name: fadeInUp;"><img src="http://lib.diygw.com/themes/design/images/pic4.png" title="基于微信小程序量身设计的WeUI组件库"/></ul><p>\r\n	</p><p>\r\n	\r\n		</p><p>\r\n			</p><h2>小程序适用行业</h2><p>\r\n			</p><p>微小程序特别适用于电影行业、交通行业、医疗行业、健身俱乐部、咖啡馆、餐饮酒店微信端的O2O平台。同时商超、电器城、服装店、零售商等需\r\n要快速构建微信端电商平台也适用。社群小程序主要面向站长、公众号等网络社群，帮助运营者搭建完善的社群，借助微信获取流量，提升用户黏性。一键导出设计\r\n的微信小程序代码，包括WXML、WXSS、JS、JSON、APP.JS、APP.JSON代码。</p><p>\r\n		</p><p><br/></p>', 0, 25);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_dashboard`
--

DROP TABLE IF EXISTS `diygw_dashboard`;
CREATE TABLE IF NOT EXISTS `diygw_dashboard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `title` varchar(100) DEFAULT NULL COMMENT '应用名称',
  `app_id` varchar(100) DEFAULT NULL,
  `app_secret` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='应用' AUTO_INCREMENT=1237 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_digg`
--

DROP TABLE IF EXISTS `diygw_digg`;
CREATE TABLE IF NOT EXISTS `diygw_digg` (
  `document_id` int(10) unsigned NOT NULL,
  `good` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赞数',
  `bad` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '批数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `uids` longtext NOT NULL COMMENT '投过票的用户id 字符合集 id1,id2,',
  PRIMARY KEY (`document_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `diygw_digg`
--

INSERT INTO `diygw_digg` (`document_id`, `good`, `bad`, `create_time`, `uids`) VALUES
(119, 1, 0, 1495863707, ',1,');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_document`
--

DROP TABLE IF EXISTS `diygw_document`;
CREATE TABLE IF NOT EXISTS `diygw_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `name` char(40) NOT NULL DEFAULT '' COMMENT '标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '标题',
  `category_id` int(10) unsigned NOT NULL COMMENT '所属分类',
  `group_id` smallint(3) unsigned NOT NULL COMMENT '所属分组',
  `description` char(140) NOT NULL DEFAULT '' COMMENT '描述',
  `root` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '根节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属ID',
  `model_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '内容类型',
  `position` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `cover_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '封面',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `deadline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截至时间',
  `attach` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件数量',
  `view` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扩展统计字段',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '优先级',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `keywords` varchar(40) NOT NULL COMMENT 'Tags关键词',
  PRIMARY KEY (`id`),
  KEY `idx_category_status` (`category_id`,`status`),
  KEY `idx_status_type_pid` (`status`,`uid`,`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文档模型基础表' AUTO_INCREMENT=137 ;

--
-- 转存表中的数据 `diygw_document`
--

INSERT INTO `diygw_document` (`id`, `uid`, `name`, `title`, `category_id`, `group_id`, `description`, `root`, `pid`, `model_id`, `type`, `position`, `link_id`, `cover_id`, `display`, `deadline`, `attach`, `view`, `comment`, `extend`, `level`, `create_time`, `update_time`, `status`, `keywords`) VALUES
(119, 1, '', 'DiyGw1.0正式版', 2, 0, '期待已久的DiyGw的ThinkPHP微信小程序解决方案最新版发布。针对小程序特性，提供了商城跟PHP后台进行交互的解决方案，帮助用户高效完成小程序开发，项目持续更新中...。', 0, 0, 2, 2, 0, 0, 0, 1, 0, 0, 127, 0, 0, 1, 1487091120, 1496281640, 1, ''),
(134, 1, '', '单页动画', 42, 0, '单页动画单页动画单页动画', 0, 0, 1, 2, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 1495986368, 1495986368, -1, '单页动画'),
(135, 1, '', '单页动画1', 42, 0, '可视化页面动画创作，充分展现你的创意。围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率', 0, 0, 2, 2, 0, 0, 2, 1, 0, 0, 0, 0, 0, 0, 1496050080, 1496061435, -1, ''),
(136, 1, '', 'DiyGw.Com快速原型设计工具', 43, 0, '更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有', 0, 0, 2, 2, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1496283550, 1496283550, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `diygw_document_article`
--

DROP TABLE IF EXISTS `diygw_document_article`;
CREATE TABLE IF NOT EXISTS `diygw_document_article` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `parse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容解析类型',
  `content` text NOT NULL COMMENT '文章内容',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页显示模板',
  `bookmark` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型文章表';

--
-- 转存表中的数据 `diygw_document_article`
--

INSERT INTO `diygw_document_article` (`id`, `parse`, `content`, `template`, `bookmark`) VALUES
(119, 0, '<p style="line-height: 1.5em;"><strong>DiyGw是一个<strong>基于TwoThink</strong>开源的内容管理框架,由Onethink基础上升级到最新的ThinkPHP5.0.6版本开发，提供更方便、更安全的WEB应用开发体验，采用了全新的架构设计和命名空间机制，融合了模块化、驱动化和插件化的设计理念于一体，开启了国内WEB应用傻瓜式开发的新潮流。 <br/></strong></p><h2><span class="piece">DiyGw提供PHP微信小程序解决方案</span></h2><p>针对小程序特性，提供了商城跟PHP后台进行交互的解决方案，帮助用户高效完成小程序开发，项目持续更新中...。<br/></p><p>感谢以下的作者提供的开源解决方案：</p><p>OneThink开源的内容管理框架：<a title="" target="_self" href="https://github.com/liu21st/onethink">https://github.com/liu21st/onethink</a></p><p>diygw开源的内容管理框架：<a title="" target="_self" href="https://github.com/593657688/diygw">https://github.com/593657688/diygw</a></p><p><span class="col-11 text-gray-dark mr-2" itemprop="about">ThinkPHP5.0的微信小程序登录流程封装</span>：<a href="https://github.com/wulongtao/think-wxminihelper">https://github.com/wulongtao/think-wxminihelper</a><br/></p><p>微信小程序商城前台：<a title="" target="_self" href="https://github.com/skyvow/m-mall">https://github.com/skyvow/m-mall</a></p><h2>主要特性：</h2><p>1. 基于ThinkPHP最新5.0.6版本。</p><p>2. 模块化：全新的架构和模块化的开发机制，便于灵活扩展和二次开发。&nbsp;</p><p>3. 文档模型/分类体系：通过和文档模型绑定，以及不同的文档类型，不同分类可以实现差异化的功能，轻松实现诸如资讯、下载、讨论和图片等功能。</p><p>4. 开源免费：DiyGw遵循Apache2开源协议,免费提供使用。&nbsp;</p><p>5. 用户行为：支持自定义用户行为，可以对单个用户或者群体用户的行为进行记录及分享，为您的运营决策提供有效参考数据。</p><p>6. 云端部署：通过驱动的方式可以轻松支持平台的部署，让您的网站无缝迁移，内置已经支持SAE和BAE3.0。</p><p>7. 云服务支持：即将启动支持云存储、云安全、云过滤和云统计等服务，更多贴心的服务让您的网站更安心。</p><p>8. 安全稳健：提供稳健的安全策略，包括备份恢复、容错、防止恶意攻击登录，网页防篡改等多项安全管理功能，保证系统安全，可靠、稳定的运行。&nbsp;</p><p>9. 应用仓库：官方应用仓库拥有大量来自第三方插件和应用模块、模板主题，有众多来自开源社区的贡献，让您的网站完美无缺。&nbsp;</p><p><br/></p><p><strong>DiyGw集成了一个完善的后台管理体系和前台模板标签系统，让你轻松管理数据和进行前台网站的标签式开发。&nbsp;</strong> </p><p><br/></p><h2>后台主要功能：</h2><p>1. 用户Passport系统</p><p>2. 配置管理系统&nbsp;</p><p>3. 权限控制系统</p><p>4. 后台建模系统&nbsp;</p><p>5. 多级分类系统&nbsp;</p><p>6. 用户行为系统&nbsp;</p><p>7. 钩子和插件系统</p><p>8. 系统日志系统&nbsp;</p><p>9. 数据备份和还原</p><p>10.小程序交互API<br/></p><p><br/></p><p><br/></p><p><br/></p><h2>系统安装<br/></h2><ol class=" list-paddingleft-2"><li><p>将DiyGw压缩包解压至一个空文件夹，并上传它。</p></li><li><p>首次在浏览器中访问index.php，将会进入安装向导。</p></li><li><p>按照安装向导完成安装。若在安装过程中出现问题，请访问官网讨论区寻求帮助。</p></li></ol><h2>分享精神 <br/></h2><p>非常感谢您的支持！如果您喜欢DiyGw，请将它介绍给自己的朋友，或者帮助他人安装一个DiyGw，又或者写一篇赞扬我们的文章。如果您愿意支持我们的工作，欢迎您对DiyGw进行捐赠。</p><h3>支付宝捐赠（收款人：luckyzf@126.com）<br/></h3><p><img src="http://static.html580.com/assets/images/alipay.gif" height="200" width="200"/></p><h3>微信捐赠（收款人：html580网站-邓志锋付钱）</h3><p><img src="http://static.html580.com/assets/images/weixin-pay.gif" height="200" width="200"/></p><p><br/></p>', '', 0),
(135, 0, '<p><br/></p><p><br/></p><h1>DIY官网做最好的单页动画可视化设计工具</h1><p><br/></p><p>强大的H5单页动画创作平台</p><p><br/></p><p>任意组合的动画效果，实现超炫酷动画特效</p><p><br/></p><p><br/></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic7.png" title="DIY官网做最好的单页动画可视化设计工具"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>DIY官网助您打造专业交互设计页面</h2><p><br/></p><p>可视化页面动画创作，充分展现你的创意</p><p><br/></p><p>围绕产品、用户、场景，对营销内容、渠道和效果整合管理，帮助企业全面提高管理效率</p><p><br/></p><p><br/></p><p><img src="http://lib.diygw.com/themes/design/images/pic8.png" title="可视化页面动画创作，充分展现你的创意,打造专业交互设计页面"/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><h2>基于Animate.css的CSS3动画效果</h2><p><br/></p><p>Animate.css是一款强大的预设CSS3动画库，它预设了抖动（shake）、闪烁（flash）、弹跳（bounce）、翻转\r\n（flip）、旋转（rotateIn/rotateOut）、淡入淡出（fadeIn/fadeOut）等多达 60 \r\n多种动画效果，几乎包含了所有常见的动画效果。</p><p><br/></p><p><br/></p><p><br/></p>', '', 0),
(136, 0, '<p>\r\n		</p><p>\r\n			</p><p>更快速、更简单、更高效的原型设计工具，适合网页或APP原型的制作</p><p>\r\n			</p><p>轻松导出基于Bootstrap,Weui高可用原型代码，下载代码后就能转为您系统所有</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<a class="a-btn1" href="http://www.diygw.com/login.html">立即定制</a>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic7.png"/>\r\n		</p><p>			\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造智能云数据表单</h2><p>\r\n			</p><p>无需特殊技能，都可以通过简单拖拽操作的创建出符合业务需求的表单</p><p>\r\n			</p><p>如问卷调查、客户登记、意见反馈、活动报名、在线订单等。</p><p>\r\n		</p><p>\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic9.png" title="DIY官网将打造智能云数据表单,通过简单拖拽操作的创建出符合业务需求的表单"/>\r\n		</p><p>		\r\n	</p><p>\r\n	</p><p>\r\n		</p><p>\r\n			</p><h2>DIY官网将打造一站式云管理系统平台</h2><p>\r\n			</p><p>随时随地移动办公,让你的企业协作更高效</p><p>\r\n			</p><p>如会议签到系统，投票系统，办公自动化OA系统，客户关系管理CRM系统，简单的进销存等。</p><p>\r\n		</p><p>	\r\n		</p><p>\r\n			<img src="http://lib.diygw.com/themes/design/images/pic10.png" title="打造一站式云管理系统平台,随时随地移动办公,让你的企业协作更高效"/>\r\n		</p><p>		\r\n	</p><p><br/></p>', '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_document_download`
--

DROP TABLE IF EXISTS `diygw_document_download`;
CREATE TABLE IF NOT EXISTS `diygw_document_download` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `parse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容解析类型',
  `content` text NOT NULL COMMENT '下载详细描述',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页显示模板',
  `file_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `score` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '下载积分',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型下载表';

--
-- 转存表中的数据 `diygw_document_download`
--

INSERT INTO `diygw_document_download` (`id`, `parse`, `content`, `template`, `file_id`, `download`, `size`, `score`) VALUES
(0, 0, '11', '', 1, 0, 239, 0),
(67, 0, '11', '', 1, 0, 239, 0);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_file`
--

DROP TABLE IF EXISTS `diygw_file`;
CREATE TABLE IF NOT EXISTS `diygw_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` char(50) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` char(30) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '远程地址',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_hooks`
--

DROP TABLE IF EXISTS `diygw_hooks`;
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
(2, 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', 1, 0, 'returntop', 1),
(3, 'documentEditForm', '添加编辑表单的 扩展内容钩子', 1, 0, '', 1),
(4, 'documentDetailAfter', '文档末尾显示', 1, 0, 'socialcomment,digg', 1),
(5, 'documentDetailBefore', '页面内容前显示用钩子', 1, 0, '', 1),
(6, 'documentSaveComplete', '保存文档数据后的扩展钩子', 2, 0, '', 1),
(7, 'documentEditFormContent', '添加编辑表单的内容显示钩子', 1, 0, 'editor', 1),
(8, 'adminArticleEdit', '后台内容编辑页编辑器', 1, 1378982734, 'editorforadmin', 1),
(13, 'AdminIndex', '首页小格子个性化显示', 1, 1479394250, 'sitestat,systeminfo,devteam', 1),
(14, 'topicComment', '评论提交方式扩展钩子。', 1, 1380163518, 'editor', 1),
(16, 'app_begin', '应用开始', 2, 1384481614, '', 1),
(17, 'thirdLogin', '第三方登录钩子', 1, 1495868922, '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_member`
--

DROP TABLE IF EXISTS `diygw_member`;
CREATE TABLE IF NOT EXISTS `diygw_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` char(10) NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `diygw_member`
--
 
-- --------------------------------------------------------

--
-- 表的结构 `diygw_menu`
--

DROP TABLE IF EXISTS `diygw_menu`;
CREATE TABLE IF NOT EXISTS `diygw_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  `module` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `dashboard_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

--
-- 转存表中的数据 `diygw_menu`
--

INSERT INTO `diygw_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`, `module`, `status`, `dashboard_id`) VALUES
(1, '首页', 0, 0, 'Index/index', 0, '', '', 0, 'admin', 1, NULL),
(2, '内容', 0, 2, 'Article/index', 0, '', '', 0, NULL, 1, NULL),
(3, '文档列表', 2, 0, 'article/index', 1, '', '内容', 0, NULL, 1, NULL),
(4, '新增', 3, 0, 'article/add', 0, '', '', 0, NULL, 1, NULL),
(5, '编辑', 3, 0, 'article/edit', 0, '', '', 0, NULL, 1, NULL),
(6, '改变状态', 3, 0, 'article/setStatus', 0, '', '', 0, NULL, 1, NULL),
(7, '保存', 3, 0, 'article/update', 0, '', '', 0, NULL, 1, NULL),
(8, '保存草稿', 3, 0, 'article/autoSave', 0, '', '', 0, NULL, 1, NULL),
(9, '移动', 3, 0, 'article/move', 0, '', '', 0, NULL, 1, NULL),
(10, '复制', 3, 0, 'article/copy', 0, '', '', 0, NULL, 1, NULL),
(11, '粘贴', 3, 0, 'article/paste', 0, '', '', 0, NULL, 1, NULL),
(12, '导入', 3, 0, 'article/batchOperate', 0, '', '', 0, NULL, 1, NULL),
(13, '回收站', 2, 0, 'article/recycle', 1, '', '内容', 0, NULL, 1, NULL),
(14, '还原', 13, 0, 'article/permit', 0, '', '', 0, NULL, 1, NULL),
(15, '清空', 13, 0, 'article/clear', 0, '', '', 0, NULL, 1, NULL),
(16, '用户', 0, 3, 'User/index', 0, '', '', 0, NULL, 1, NULL),
(17, '用户信息', 16, 0, 'User/index', 0, '', '用户管理', 0, NULL, 1, NULL),
(18, '新增用户', 17, 0, 'User/add', 0, '添加新用户', '', 0, NULL, 1, NULL),
(19, '用户行为', 16, 0, 'User/action', 0, '', '行为管理', 0, NULL, 1, NULL),
(20, '新增用户行为', 19, 0, 'User/addaction', 0, '', '', 0, NULL, 1, NULL),
(21, '编辑用户行为', 19, 0, 'User/editaction', 0, '', '', 0, NULL, 1, NULL),
(22, '保存用户行为', 19, 0, 'User/saveAction', 0, '"用户->用户行为"保存编辑和新增的用户行为', '', 0, NULL, 1, NULL),
(23, '变更行为状态', 19, 0, 'User/setStatus', 0, '"用户->用户行为"中的启用,禁用和删除权限', '', 0, NULL, 1, NULL),
(24, '禁用会员', 19, 0, 'User/changeStatus?method=forbidUser', 0, '"用户->用户信息"中的禁用', '', 0, NULL, 1, NULL),
(25, '启用会员', 19, 0, 'User/changeStatus?method=resumeUser', 0, '"用户->用户信息"中的启用', '', 0, NULL, 1, NULL),
(26, '删除会员', 19, 0, 'User/changeStatus?method=deleteUser', 0, '"用户->用户信息"中的删除', '', 0, NULL, 1, NULL),
(27, '权限管理', 16, 0, 'AuthManager/index', 0, '', '用户管理', 0, NULL, 1, NULL),
(28, '删除', 27, 0, 'AuthManager/changeStatus?method=deleteGroup', 0, '删除用户组', '', 0, NULL, 1, NULL),
(29, '禁用', 27, 0, 'AuthManager/changeStatus?method=forbidGroup', 0, '禁用用户组', '', 0, NULL, 1, NULL),
(30, '恢复', 27, 0, 'AuthManager/changeStatus?method=resumeGroup', 0, '恢复已禁用的用户组', '', 0, NULL, 1, NULL),
(31, '新增', 27, 0, 'AuthManager/createGroup', 0, '创建新的用户组', '', 0, NULL, 1, NULL),
(32, '编辑', 27, 0, 'AuthManager/editGroup', 0, '编辑用户组名称和描述', '', 0, NULL, 1, NULL),
(33, '保存用户组', 27, 0, 'AuthManager/writeGroup', 0, '新增和编辑用户组的"保存"按钮', '', 0, NULL, 1, NULL),
(34, '授权', 27, 0, 'AuthManager/group', 0, '"后台 \\ 用户 \\ 用户信息"列表页的"授权"操作按钮,用于设置用户所属用户组', '', 0, NULL, 1, NULL),
(35, '访问授权', 27, 0, 'AuthManager/access', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"访问授权"操作按钮', '', 0, NULL, 1, NULL),
(36, '成员授权', 27, 0, 'AuthManager/user', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"成员授权"操作按钮', '', 0, NULL, 1, NULL),
(37, '解除授权', 27, 0, 'AuthManager/removeFromGroup', 0, '"成员授权"列表页内的解除授权操作按钮', '', 0, NULL, 1, NULL),
(38, '保存成员授权', 27, 0, 'AuthManager/addToGroup', 0, '"用户信息"列表页"授权"时的"保存"按钮和"成员授权"里右上角的"添加"按钮)', '', 0, NULL, 1, NULL),
(39, '分类授权', 27, 0, 'AuthManager/category', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"分类授权"操作按钮', '', 0, NULL, 1, NULL),
(40, '保存分类授权', 27, 0, 'AuthManager/addToCategory', 0, '"分类授权"页面的"保存"按钮', '', 0, NULL, 1, NULL),
(41, '模型授权', 27, 0, 'AuthManager/modelauth', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"模型授权"操作按钮', '', 0, NULL, 1, NULL),
(42, '保存模型授权', 27, 0, 'AuthManager/addToModel', 0, '"分类授权"页面的"保存"按钮', '', 0, NULL, 1, NULL),
(43, '扩展', 0, 7, 'Addons/index', 0, '', '', 0, NULL, 1, NULL),
(44, '插件管理', 43, 1, 'Addons/index', 0, '', '扩展', 0, NULL, 1, NULL),
(45, '创建', 44, 0, 'Addons/create', 0, '服务器上创建插件结构向导', '', 0, NULL, 1, NULL),
(46, '检测创建', 44, 0, 'Addons/checkForm', 0, '检测插件是否可以创建', '', 0, NULL, 1, NULL),
(47, '预览', 44, 0, 'Addons/preview', 0, '预览插件定义类文件', '', 0, NULL, 1, NULL),
(48, '快速生成插件', 44, 0, 'Addons/build', 0, '开始生成插件结构', '', 0, NULL, 1, NULL),
(49, '设置', 44, 0, 'Addons/config', 0, '设置插件配置', '', 0, NULL, 1, NULL),
(50, '禁用', 44, 0, 'Addons/disable', 0, '禁用插件', '', 0, NULL, 1, NULL),
(51, '启用', 44, 0, 'Addons/enable', 0, '启用插件', '', 0, NULL, 1, NULL),
(52, '安装', 44, 0, 'Addons/install', 0, '安装插件', '', 0, NULL, 1, NULL),
(53, '卸载', 44, 0, 'Addons/uninstall', 0, '卸载插件', '', 0, NULL, 1, NULL),
(54, '更新配置', 44, 0, 'Addons/saveconfig', 0, '更新插件配置处理', '', 0, NULL, 1, NULL),
(55, '插件后台列表', 44, 0, 'Addons/adminList', 0, '', '', 0, NULL, 1, NULL),
(56, 'URL方式访问插件', 44, 0, 'Addons/execute', 0, '控制是否有权限通过url访问插件控制器方法', '', 0, NULL, 1, NULL),
(57, '钩子管理', 43, 2, 'Addons/hooks', 0, '', '扩展', 0, NULL, 1, NULL),
(58, '模型管理', 68, 3, 'Model/index', 0, '', '系统设置', 0, NULL, 1, NULL),
(59, '新增', 58, 0, 'model/add', 0, '', '', 0, NULL, 1, NULL),
(60, '编辑', 58, 0, 'model/edit', 0, '', '', 0, NULL, 1, NULL),
(61, '改变状态', 58, 0, 'model/setStatus', 0, '', '', 0, NULL, 1, NULL),
(62, '保存数据', 58, 0, 'model/update', 0, '', '', 0, NULL, 1, NULL),
(63, '属性管理', 68, 0, 'Attribute/index', 1, '网站属性配置。', '', 0, NULL, 1, NULL),
(64, '新增', 63, 0, 'Attribute/add', 0, '', '', 0, NULL, 1, NULL),
(65, '编辑', 63, 0, 'Attribute/edit', 0, '', '', 0, NULL, 1, NULL),
(66, '改变状态', 63, 0, 'Attribute/setStatus', 0, '', '', 0, NULL, 1, NULL),
(67, '保存数据', 63, 0, 'Attribute/update', 0, '', '', 0, NULL, 1, NULL),
(68, '系统', 0, 4, 'Config/group', 0, '', '', 0, NULL, 1, NULL),
(69, '网站设置', 68, 1, 'Config/group', 0, '', '系统设置', 0, NULL, 1, NULL),
(70, '配置管理', 68, 4, 'Config/index', 0, '', '系统设置', 0, NULL, 1, NULL),
(71, '编辑', 70, 0, 'Config/edit', 0, '新增编辑和保存配置', '', 0, NULL, 1, NULL),
(72, '删除', 70, 0, 'Config/del', 0, '删除配置', '', 0, NULL, 1, NULL),
(73, '新增', 70, 0, 'Config/add', 0, '新增配置', '', 0, NULL, 1, NULL),
(74, '保存', 70, 0, 'Config/save', 0, '保存配置', '', 0, NULL, 1, NULL),
(75, '菜单管理', 68, 5, 'Menu/index', 0, '', '系统设置', 0, NULL, 1, NULL),
(76, '导航管理', 68, 6, 'Channel/index', 0, '', '系统设置', 0, NULL, 1, NULL),
(77, '新增', 76, 0, 'Channel/add', 0, '', '', 0, NULL, 1, NULL),
(78, '编辑', 76, 0, 'Channel/edit', 0, '', '', 0, NULL, 1, NULL),
(79, '删除', 76, 0, 'Channel/del', 0, '', '', 0, NULL, 1, NULL),
(80, '分类管理', 68, 2, 'Category/index', 0, '', '系统设置', 0, NULL, 1, NULL),
(81, '编辑', 80, 0, 'Category/edit', 0, '编辑和保存栏目分类', '', 0, NULL, 1, NULL),
(82, '新增', 80, 0, 'Category/add', 0, '新增栏目分类', '', 0, NULL, 1, NULL),
(83, '删除', 80, 0, 'Category/remove', 0, '删除栏目分类', '', 0, NULL, 1, NULL),
(84, '移动', 80, 0, 'Category/operate/type/move', 0, '移动栏目分类', '', 0, NULL, 1, NULL),
(85, '合并', 80, 0, 'Category/operate/type/merge', 0, '合并栏目分类', '', 0, NULL, 1, NULL),
(86, '备份数据库', 68, 0, 'Database/index?type=export', 0, '', '数据备份', 0, NULL, 1, NULL),
(87, '备份', 86, 0, 'Database/export', 0, '备份数据库', '', 0, NULL, 1, NULL),
(88, '优化表', 86, 0, 'Database/optimize', 0, '优化数据表', '', 0, NULL, 1, NULL),
(89, '修复表', 86, 0, 'Database/repair', 0, '修复数据表', '', 0, NULL, 1, NULL),
(90, '还原数据库', 68, 0, 'Database/index?type=import', 0, '', '数据备份', 0, NULL, 1, NULL),
(91, '恢复', 90, 0, 'Database/import', 0, '数据库恢复', '', 0, NULL, 1, NULL),
(92, '删除', 90, 0, 'Database/del', 0, '删除备份文件', '', 0, NULL, 1, NULL),
(93, '其他', 0, 5, 'other', 1, '', '', 0, NULL, 1, NULL),
(96, '新增', 75, 0, 'Menu/add', 0, '', '系统设置', 0, NULL, 1, NULL),
(98, '编辑', 75, 0, 'Menu/edit', 0, '', '', 0, NULL, 1, NULL),
(106, '行为日志', 16, 0, 'Action/actionlog', 0, '', '行为管理', 0, NULL, 1, NULL),
(108, '修改密码', 16, 0, 'User/updatePassword', 1, '', '', 0, NULL, 1, NULL),
(109, '修改昵称', 16, 0, 'User/updateNickname', 1, '', '', 0, NULL, 1, NULL),
(110, '查看行为日志', 106, 0, 'action/edit', 1, '', '', 0, NULL, 1, NULL),
(112, '新增数据', 58, 0, 'think/add', 1, '', '', 0, NULL, 1, NULL),
(113, '编辑数据', 58, 0, 'think/edit', 1, '', '', 0, NULL, 1, NULL),
(114, '导入', 75, 0, 'Menu/import', 0, '', '', 0, NULL, 1, NULL),
(115, '生成', 58, 0, 'Model/generate', 0, '', '', 0, NULL, 1, NULL),
(116, '新增钩子', 57, 0, 'Addons/addHook', 0, '', '', 0, NULL, 1, NULL),
(117, '编辑钩子', 57, 0, 'Addons/edithook', 0, '', '', 0, NULL, 1, NULL),
(118, '文档排序', 3, 0, 'Article/sort', 1, '', '', 0, NULL, 1, NULL),
(119, '排序', 70, 0, 'Config/sort', 1, '', '', 0, NULL, 1, NULL),
(120, '排序', 75, 0, 'Menu/sort', 1, '', '', 0, NULL, 1, NULL),
(121, '排序', 76, 0, 'Channel/sort', 1, '', '', 0, NULL, 1, NULL),
(122, '数据列表', 58, 0, 'think/lists', 1, '', '', 0, NULL, 1, NULL),
(123, '审核列表', 3, 0, 'Article/examine', 1, '', '', 0, NULL, 1, NULL),
(129, '图片轮播', 2, 0, 'Think/lists?model=banner', 0, '', '内容', 0, 'admin', 1, NULL),
(130, '安装应用', 68, 0, 'Database/importfiles', 0, '', '数据备份', 0, 'admin', 1, NULL),
(131, '应用', 0, 1, 'Index/system', 0, '', '', 0, 'admin', 1, NULL),
(132, '应用', 131, 0, 'Index/system', 0, '', '', 0, 'admin', 1, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_model`
--

DROP TABLE IF EXISTS `diygw_model`;
CREATE TABLE IF NOT EXISTS `diygw_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '继承的模型',
  `relation` varchar(30) NOT NULL DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `need_pk` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text COMMENT '表单字段排序',
  `field_group` varchar(255) NOT NULL DEFAULT '1:基础' COMMENT '字段分组',
  `attribute_list` text COMMENT '属性列表（表的字段）',
  `attribute_alias` varchar(255) NOT NULL DEFAULT '' COMMENT '属性别名定义',
  `template_list` varchar(100) NOT NULL DEFAULT '' COMMENT '列表模板',
  `template_add` varchar(100) NOT NULL DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑模板',
  `list_grid` text COMMENT '列表定义',
  `list_row` smallint(2) unsigned NOT NULL DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) NOT NULL DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) NOT NULL DEFAULT '' COMMENT '高级搜索的字段',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) NOT NULL DEFAULT 'MyISAM' COMMENT '数据库引擎',
  `form_id` int(10) DEFAULT NULL,
  `dashboard_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文档模型表' AUTO_INCREMENT=816 ;

--
-- 转存表中的数据 `diygw_model`
--

INSERT INTO `diygw_model` (`id`, `name`, `title`, `extend`, `relation`, `need_pk`, `field_sort`, `field_group`, `attribute_list`, `attribute_alias`, `template_list`, `template_add`, `template_edit`, `list_grid`, `list_row`, `search_key`, `search_list`, `create_time`, `update_time`, `status`, `engine_type`, `form_id`, `dashboard_id`) VALUES
(1, 'document', '基础文档', 0, '', 1, '{"1":["2","3","288","5","9","10","11","12","13","14","16","17","19","20"]}', '1:基础', '', '', '', '', '', 'id:编号\r\ntitle:标题:[EDIT]\r\ntype:类型\r\nupdate_time:最后更新\r\nstatus:状态\r\nview:浏览\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 0, '', '', 1383891233, 1479651166, 1, 'MyISAM', NULL, NULL),
(2, 'article', '文章', 1, '', 1, '{"1":["3","24","12","2","288","5"],"2":["9","13","19","10","16","17","26","20","14","11","25"]}', '1:基础,2:扩展', '', '', '', '', '', '', 0, '', '', 1383891243, 1496060930, 1, 'MyISAM', NULL, NULL),
(3, 'download', '下载', 1, '', 1, '{"1":["3","2","288","5","289","28","30","32","31"],"2":["13","10","9","12","16","17","19","11","20","14","29"]}', '1:基础,2:扩展', '', '', '', '', '', '', 0, '', '', 1383891252, 1479964263, 1, 'MyISAM', NULL, NULL),
(25, 'content', '文章', 0, '', 1, '{"1":["295","294","296","293","299","297","298","292"]}', '1:基础', '', '', '', '', '', 'id:编号\r\nname:标题:[EDIT]\r\nprice:价格\r\nstatus:状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, '', '', 1496071521, 1496235133, 1, 'MyISAM', NULL, NULL),
(26, 'banner', '图片轮播', 0, '', 1, '{"1":["300","302","304","303"]}', '1:基础', '', '', '', '', '', 'id:编号\r\nname:标题:[EDIT]\r\nimg:图像地址\r\nstatus:状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, '', '', 1496285189, 1496286918, 1, 'MyISAM', NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_picture`
--

DROP TABLE IF EXISTS `diygw_picture`;
CREATE TABLE IF NOT EXISTS `diygw_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- 转存表中的数据 `diygw_picture`
--

INSERT INTO `diygw_picture` (`id`, `path`, `url`, `md5`, `sha1`, `status`, `create_time`) VALUES
(10, '/static/uploads/picture/20170601/baf6d81d4d2a691fae754b224f44ede4.png', '', '8c72c0ff778a0c0fa528c9f8561332e4', 'fde21816b9eb8d244544e9e0674051304b1a03ee', 1, 1496281988),
(11, '/static/uploads/picture/20170601/7bb70138eb8ce239cae6b632bc47ea25.png', '', 'e3cbe368636b5f6f449bc5f2220226c3', 'd2382cb27c5737272a601b95d284dc8ac3197670', 1, 1496282503),
(12, '/static/uploads/picture/20170601/b45191503f453c7ae84a5ac21c4bba5b.png', '', 'ed902b1f2b002a65aaf6e4cbd9183c6e', '9a87832c079a35602a371890d73d41d912875a8c', 1, 1496282523),
(13, '/static/uploads/picture/20170601/3ee653b9f12a1556b348d069b21a9324.png', '', '904ba7f1b160fadeb60ef6af125e1935', 'e89e63445996f9903a2e6498a9bff98359bd19ca', 1, 1496282542),
(14, '/static/uploads/picture/20170601/f0fe1532f5d3fdcfb0b8509926bc08c5.jpg', '', '4cd5b8ba16de20db449a1152c61d9aec', 'e8f86e2c439cf27492e59ef7544a76f7bbec21f8', 1, 1496287008),
(15, '/static/uploads/picture/20170601/58465aabef8f36f13859c955fcc980d6.jpg', '', '28cce3328b0a8a178bccc62c5180903a', '3348894f142dbac0befd0a49f5a4eac22a04d7e3', 1, 1496287130),
(16, '/static/uploads/picture/20170601/2944bee3664ad01a27bf3dad57f2124f.jpg', '', 'd2ad34ef31c28abc5d26bd58e707a18e', 'efc98b035901d4292e79a3aec3b1a32c124f89ba', 1, 1496287162);

-- --------------------------------------------------------

--
-- 表的结构 `diygw_ucenter_admin`
--

DROP TABLE IF EXISTS `diygw_ucenter_admin`;
CREATE TABLE IF NOT EXISTS `diygw_ucenter_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员用户ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_ucenter_app`
--

DROP TABLE IF EXISTS `diygw_ucenter_app`;
CREATE TABLE IF NOT EXISTS `diygw_ucenter_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应用ID',
  `title` varchar(30) NOT NULL COMMENT '应用名称',
  `url` varchar(100) NOT NULL COMMENT '应用URL',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '应用IP',
  `auth_key` varchar(100) NOT NULL DEFAULT '' COMMENT '加密KEY',
  `sys_login` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '同步登陆',
  `allow_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '允许访问的IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '应用状态',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_ucenter_member`
--

DROP TABLE IF EXISTS `diygw_ucenter_member`;
CREATE TABLE IF NOT EXISTS `diygw_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `diygw_ucenter_member`
--

-- --------------------------------------------------------

--
-- 表的结构 `diygw_ucenter_setting`
--

DROP TABLE IF EXISTS `diygw_ucenter_setting`;
CREATE TABLE IF NOT EXISTS `diygw_ucenter_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型（1-用户配置）',
  `value` text NOT NULL COMMENT '配置数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设置表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_url`
--

DROP TABLE IF EXISTS `diygw_url`;
CREATE TABLE IF NOT EXISTS `diygw_url` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '链接唯一标识',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `short` char(100) NOT NULL DEFAULT '' COMMENT '短网址',
  `status` tinyint(2) NOT NULL DEFAULT '2' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='链接表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `diygw_userdata`
--

DROP TABLE IF EXISTS `diygw_userdata`;
CREATE TABLE IF NOT EXISTS `diygw_userdata` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型标识',
  `target_id` int(10) unsigned NOT NULL COMMENT '目标id',
  UNIQUE KEY `uid` (`uid`,`type`,`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
