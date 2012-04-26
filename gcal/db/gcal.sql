DROP TABLE IF EXISTS `gcal`;
CREATE TABLE `gcal` (
  `id` int(11) unsigned NOT NULL,
  `cms_headline` varchar(255) NOT NULL default '',
  `gcal_content` text NOT NULL default '',
  `timestamp` int(22) NOT NULL default '0',
  `cms_deleted` tinyint(1) NOT NULL default '0',
  `cms_draft` tinyint(1) NOT NULL default '0',
  `cms_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `cms_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `cms_modified_by_user` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
