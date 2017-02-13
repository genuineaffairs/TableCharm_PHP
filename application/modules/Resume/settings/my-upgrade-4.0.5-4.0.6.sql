CREATE TABLE IF NOT EXISTS `engine4_resume_videoratings` (
  `video_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`video_id`,`user_id`),
  KEY `video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_resume_videos` (
  `video_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `owner_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '1',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL,
  `code` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) unsigned DEFAULT NULL,
  `rating` float NOT NULL,
  `like_count` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `highlighted` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `duration` int(9) unsigned NOT NULL,
  PRIMARY KEY (`video_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`),
  KEY `page_id` (`resume_id`),
  KEY `featured` (`featured`),
  KEY `highlighted` (`highlighted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `enabled`, `multi`, `priority`) VALUES
('Resume Video Encode', 'resume_video_encode', 'resume', 'Resume_Plugin_Job_Encode', 1, 2, 75);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'video' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'video' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');  

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('resume_video_processed', 'resume', 'Your {item:$object:resume video} is ready to be viewed.', 0, ''),
('resume_video_processed_failed', 'resume', 'Your {item:$object:resume video} has failed to process.', 0, ''),
('resume_video_new', 'resume', '{item:$subject} has created a resume video {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('resume_dashboard_video', 'resume', 'Upload Resume Videos', 'Resume_Plugin_Menus', '{"route":"resume_video_create", "class":"buttonlink icon_video_new"}', 'resume_dashboard', '', 5);