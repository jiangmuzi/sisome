--
-- 数据库: `sisome`
--

-- --------------------------------------------------------

--
-- 表的结构 `typecho_comments`
--

CREATE TABLE `typecho_comments` (
  `coid` int(10) unsigned NOT NULL auto_increment,
  `cid` int(10) unsigned default '0',
  `created` int(10) unsigned default '0',
  `authorId` int(10) unsigned default '0',
  `ownerId` int(10) unsigned default '0',
  `ip` varchar(64) default NULL,
  `agent` varchar(200) default NULL,
  `text` text,
  `type` varchar(16) default 'comment',
  `status` varchar(16) default 'approved',
  PRIMARY KEY  (`coid`),
  KEY `cid` (`cid`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_contents`
--

CREATE TABLE `typecho_contents` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `slug` varchar(200) default NULL,
  `created` int(10) unsigned default '0',
  `modified` int(10) unsigned default '0',
  `text` text,
  `order` int(10) unsigned default '0',
  `authorId` int(10) unsigned default '0',
  `template` varchar(32) default NULL,
  `type` varchar(16) default 'post',
  `status` varchar(16) default 'publish',
  `password` varchar(32) default NULL,
  `commentsNum` int(10) unsigned default '0',
  `allowComment` char(1) default '0',
  `allowPing` char(1) default '0',
  `allowFeed` char(1) default '0',
  `parent` int(10) unsigned default '0',
  `viewsNum` int(10) unsigned default '0',
  `lastUid` int(10) unsigned default '0',
  `lastComment` int(10) unsigned default '0',
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `slug` (`slug`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_fields`
--

CREATE TABLE `typecho_fields` (
  `cid` int(10) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(8) default 'str',
  `str_value` text,
  `int_value` int(10) default '0',
  `float_value` float default '0',
  PRIMARY KEY  (`cid`,`name`),
  KEY `int_value` (`int_value`),
  KEY `float_value` (`float_value`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_metas`
--

CREATE TABLE `typecho_metas` (
  `mid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `slug` varchar(200) default NULL,
  `type` varchar(32) NOT NULL,
  `description` varchar(200) default NULL,
  `style` text,
  `count` int(10) unsigned default '0',
  `order` int(10) unsigned default '0',
  `parent` int(10) unsigned default '0',
  PRIMARY KEY  (`mid`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_options`
--

CREATE TABLE `typecho_options` (
  `name` varchar(32) NOT NULL,
  `user` int(10) unsigned NOT NULL default '0',
  `value` text,
  PRIMARY KEY  (`name`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_relationships`
--

CREATE TABLE `typecho_relationships` (
  `cid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_users`
--

CREATE TABLE `typecho_users` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) default NULL,
  `password` varchar(64) default NULL,
  `mail` varchar(200) default NULL,
  `url` varchar(200) default NULL,
  `screenName` varchar(32) default NULL,
  `created` int(10) unsigned default '0',
  `activated` int(10) unsigned default '0',
  `logged` int(10) unsigned default '0',
  `group` varchar(16) default 'visitor',
  `authCode` varchar(64) default NULL,
  `location` varchar(120) default NULL,
  `sign` varchar(120) default NULL,
  `intro` varchar(200) default NULL,
  `credits` int(10) unsigned default '0',
  `extend` text,
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_verifies`
--

CREATE TABLE IF NOT EXISTS `typecho_verifies` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '验证表主键',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证用户ID',
  `type` char(16) NOT NULL DEFAULT '' COMMENT '验证类型',
  `token` varchar(100) NOT NULL COMMENT '验证码',
  `created` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `confirm` varchar(120) NOT NULL COMMENT '待验证邮箱',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已经验证',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_creditslog`
--

CREATE TABLE IF NOT EXISTS `typecho_creditslog` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '积分日志表主键',
  `uid` int(10) unsigned NOT NULL COMMENT '所属用户',
  `srcId` int(10) unsigned NOT NULL COMMENT '触发的资源ID',
  `created` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `type` char(16) NOT NULL DEFAULT 'login' COMMENT '积分类型',
  `amount` int(10) NOT NULL DEFAULT '0' COMMENT '本次积分',
  `balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '余额',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_oauth`
--

CREATE TABLE IF NOT EXISTS `typecho_oauth` (
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `qqId` char(64) NOT NULL DEFAULT '' COMMENT 'QQ登录',
  `weiboId` char(64) NOT NULL DEFAULT '' COMMENT '微博登录',
  `wechatId` char(64) NOT NULL DEFAULT '' COMMENT '微信登录',
  `doubanId` char(64) NOT NULL DEFAULT '' COMMENT '豆瓣登录',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_notice`
--

CREATE TABLE IF NOT EXISTS `typecho_messages` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '提醒表主键',
  `uid` int(10) unsigned NOT NULL COMMENT '提醒的用户',
  `type` char(16) NOT NULL DEFAULT 'comment' COMMENT '提醒类型',
  `srcId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发的资源',
  `created` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_favorites`
--

CREATE TABLE IF NOT EXISTS `typecho_favorites` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏主键',
  `uid` int(10) unsigned NOT NULL COMMENT '所属用户',
  `type` char(16) NOT NULL DEFAULT 'post' COMMENT '收藏类型',
  `srcId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `created` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏时间',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;