# $Id: Jamp.sql,v 1.2 2004/02/29 20:51:25 entropie Exp $

DROP TABLE IF EXISTS `Jamp_files`;
CREATE TABLE `Jamp_files` (
  `id` int(20) NOT NULL auto_increment,
  `pathid` int(20) NOT NULL default '0',
  `file` varchar(255) NOT NULL default '',
  `prim_path_id` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Filnames, merged with Jamp_path';

DROP TABLE IF EXISTS `Jamp_fullpath`;
CREATE TABLE `Jamp_fullpath` (
  `id` int(20) NOT NULL auto_increment,
  `path` varchar(255) NOT NULL default '',
  `prim_path_id` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Fullpath`s';

DROP TABLE IF EXISTS `Jamp_path`;
CREATE TABLE `Jamp_path` (
  `id` int(20) NOT NULL auto_increment,
  `pid` int(20) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `prim_path_id` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Mirroring all folders beginning with /fake/root/';

DROP TABLE IF EXISTS `Jamp_playlists`;
CREATE TABLE `Jamp_playlists` (
  `id` int(20) NOT NULL auto_increment,
  `songid` int(20) NOT NULL default '0',
  `m3ufile` varchar(32) NOT NULL default '',
  `cookie_string` int(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='User playlists';

DROP TABLE IF EXISTS `Jamp_shoutbox`;
CREATE TABLE `Jamp_shoutbox` (
  `id` int(10) NOT NULL auto_increment,
  `sb_date` timestamp(14) NOT NULL,
  `sb_name` varchar(36) NOT NULL default '',
  `sb_text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Shoutbox';

DROP TABLE IF EXISTS `Jamp_time`;
CREATE TABLE `Jamp_time` (
  `id` int(20) NOT NULL auto_increment,
  `date` timestamp(14) NOT NULL,
  `cookie_string` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Saves the cookies string for each user';

DROP TABLE IF EXISTS `Jamp_tmpplaylist`;
CREATE TABLE `Jamp_tmpplaylist` (
  `id` int(20) NOT NULL auto_increment,
  `cookie_string` int(20) NOT NULL default '0',
  `songid` int(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Temporary playlists for each user';

DROP TABLE IF EXISTS `Jamp_user`;
CREATE TABLE `Jamp_user` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `admin` enum('admin','user') NOT NULL default 'user',
  `email` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='User login data';


INSERT INTO `Jamp_user` VALUES (1, 'admin', md5('admin'), 'admin', '');
