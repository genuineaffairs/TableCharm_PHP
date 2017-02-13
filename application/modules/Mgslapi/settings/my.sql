-- --------------------------------------------------------

--
-- Table structure for table `engine4_mgslapi_devices`
--
CREATE TABLE IF NOT EXISTS `engine4_mgslapi_devices` (
 `device_id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `device_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 for ios and 2 for andriod',
 `device_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `sender_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
 `app_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `allow` tinyint(4) DEFAULT '1',
 PRIMARY KEY (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('mgslapi', '0', '1', '1', '1');

ALTER TABLE `engine4_mgslapi_devices`
  ADD UNIQUE(`user_id`, `app_id`),
  ADD UNIQUE(`user_id`, `device_token`)
  ;