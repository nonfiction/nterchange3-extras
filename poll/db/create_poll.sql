# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.0.22)
# Database: bowen_2011
# Generation Time: 2012-05-29 22:03:34 +0000
# ************************************************************

# Dump of table poll
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poll`;

CREATE TABLE `poll` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cms_headline` varchar(255) NOT NULL default '',
  `question_count` int(11) unsigned NOT NULL default '5',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table poll_submissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poll_submissions`;

CREATE TABLE `poll_submissions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `poll_id` int(11) unsigned NOT NULL default '5',
  `poll_value_id` int(11) unsigned NOT NULL default '5',
  `submission_ip` varchar(255) NOT NULL default '5',
  `submission_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table poll_values
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poll_values`;

CREATE TABLE `poll_values` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `poll_id` int(11) unsigned NOT NULL default '5',
  `value` varchar(255) NOT NULL default '5',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_asset_info` (asset, asset_name, cms_created, cms_modified, cms_modified_by_user) VALUE ('poll', 'Poll', NOW(), NOW(), 1)
