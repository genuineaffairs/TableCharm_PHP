INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('shared-resources', 'Shared Resources Management', 'Module to manage share resources', '4.0.0', 1, 'extra') ;

CREATE TABLE IF NOT EXISTS `engine4_sharedresources_sites` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `enable` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`site_id`),
  UNIQUE KEY `host` (`host`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_sharedresources_users_sites` (
  `user_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL DEFAULT '1',
  `is_site_master` smallint(1) NOT NULL DEFAULT '0',
  `enable` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`, `site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
INSERT IGNORE INTO `engine4_sharedresources_users_sites` (`user_id`) SELECT `user_id` FROM `engine4_users`;

INSERT IGNORE INTO `engine4_sharedresources_sites` (`host`) VALUES ('engage.myglobalsportlink.com'), ('onesport.myglobalsportlink.com');