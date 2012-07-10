CREATE TABLE `youtube` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cms_headline` varchar(255) NOT NULL DEFAULT '',
  `link_text` varchar(255) NOT NULL DEFAULT '',
  `thumbnail_image` varchar(255) DEFAULT '',
  `align` varchar(255) DEFAULT NULL,
  `embed_html` text NOT NULL,
  `cms_active` tinyint(1) NOT NULL DEFAULT '1',
  `cms_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `cms_draft` tinyint(1) NOT NULL DEFAULT '0',
  `cms_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cms_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cms_modified_by_user` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;