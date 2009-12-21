
CREATE TABLE `languages` (
  `ident` varchar(50) NOT NULL default '',
  `scope` varchar(20) default NULL,
  `en` longtext COMMENT 'en',
  `lv` longtext COMMENT 'lv',
  UNIQUE KEY `ident` (`ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `type` tinyint(1) default NULL,
  `username` varchar(20) default NULL,
  `password` varchar(50) default NULL,
  `access` text,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`,`type`,`username`,`password`,`access`) VALUES (1,NULL,'root','dc76e9f0c0006e8f919e0c515c66dbba3982f785','{\"*\":\"*\"}');
