# Dump of table password
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password`;

CREATE TABLE `password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cms_headline` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `hashed_password` varchar(255) NOT NULL,
  `login_url` varchar(255) NOT NULL DEFAULT '',
  `cms_active` tinyint(1) NOT NULL DEFAULT '1',
  `cms_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `cms_created` datetime NOT NULL,
  `cms_modified` datetime NOT NULL,
  `cms_modified_by_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
