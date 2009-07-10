
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) default NULL,
  `password` varchar(50) default NULL,
  `access` text,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `users` WRITE;


INSERT INTO `users` (`id`,`username`,`password`,`access`) VALUES (1,'root','dc76e9f0c0006e8f919e0c515c66dbba3982f785','{\"*\":\"*\"}');


UNLOCK TABLES;
