
CREATE TABLE test_table (
    `id` INT(11) UNSIGNED auto_increment,
    `name` VARCHAR(255),
    PRIMARY KEY(id)
);

INSERT INTO test_table VALUES (1,"Linus");
INSERT INTO test_table VALUES (2,"Jason");
INSERT INTO test_table VALUES (3,"Dan");
INSERT INTO test_table VALUES (4,"John");
INSERT INTO test_table VALUES (5,"Jon");
INSERT INTO test_table VALUES (6,"Jez");

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