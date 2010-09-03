

CREATE TABLE `resource` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `parameters` text,
  `authenticator` varchar(255) default NULL,
  `cache_length` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1
