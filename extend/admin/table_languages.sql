
CREATE TABLE `languages` (
  `ident` varchar(50) NOT NULL,
  `scope` varchar(20) default NULL,
  UNIQUE KEY `ident` (`ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
