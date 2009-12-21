
CREATE TABLE `sessions` (
  `id` varchar(42) NOT NULL default '',
  `data` blob,
  `expires` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `expires` (`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
