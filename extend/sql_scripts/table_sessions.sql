
CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL default '',
  `data` mediumblob,
  `expires` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `expires` (`expires`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
