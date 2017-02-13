INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('zulu', 'Zulu', 'Electronic Medical Record', '4.0.0', 1, 'extra') ;
INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('zulu', '0', '1', '1', '1');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_zulu', 'zulu', 'E-Medical Record', '', '{"route":"admin_default","module":"zulu","controller":"fields"}', 'core_admin_main_plugins', '', 2)
;

-- ('core_admin_main_zulu_management', 'zulu', 'Zulu Management', '', '{"uri":"javascript:void(0);this.blur();"}', 'core_admin_main', 'core_admin_main_zulu', 99),

-- ('core_admin_main_zulu_news_management', 'zulu', 'News', '', '{"route":"admin_default","module":"zulu","controller":"news","action":"index"}', 'core_admin_main_zulu', '', 1);

-- Insert menu items for Profile Edit Page

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES ('zulu_edit', 'standard', 'Zulu Member Edit Profile Navigation Menu', 999);

DELETE FROM `engine4_core_menuitems` WHERE `name` IN ('zulu_edit_profile', 'zulu_edit_clinical', 'zulu_edit_sharing');
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('zulu_edit_profile', 'zulu', 'Personal Info', 'Zulu_Plugin_Menus', '{"route":"zulu_extended","module":"zulu","controller":"edit","action":"profile"}', 'zulu_edit', '', 1, 0, 1),
('zulu_edit_clinical', 'zulu', 'Medical Record', 'Zulu_Plugin_Menus', '{"route":"zulu_extended","module":"zulu","controller":"edit","action":"clinical"}', 'zulu_edit', '', 1, 0, 2),
('zulu_edit_sharing', 'zulu', 'Sharing preferences for Medical Record', 'Zulu_Plugin_Menus', '{"route":"zulu_extended","module":"zulu","controller":"edit","action":"sharing"}', 'zulu_edit', '', 1, 0, 3)
;

-- Insert menu items for Zulu Admin Page

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES ('zulu_admin_main', 'standard', 'Zulu Admin Main Navigation Menu', 999);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('zulu_admin_main_fields', 'zulu', 'Clinical Questions', '', '{"route":"admin_default","module":"zulu","controller":"fields"}', 'zulu_admin_main', '', 1),
('zulu_admin_main_userfields', 'zulu', 'Profile Questions', '', '{"route":"admin_default","module":"zulu","controller":"user-fields"}', 'zulu_admin_main', '', 2)
;

--
-- Table structure for table `engine4_zulu_signup`
--

DROP TABLE IF EXISTS `engine4_zulu_signup`;
CREATE TABLE IF NOT EXISTS `engine4_zulu_signup` (
  `signup_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  `enable` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`signup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `engine4_zulu_signup`
--

INSERT IGNORE INTO `engine4_zulu_signup` (`signup_id`, `class`, `order`, `enable`) VALUES
(1, 'User_Plugin_Signup_Account', 1, 0),
(2, 'User_Plugin_Signup_Fields', 5, 0),
(3, 'User_Plugin_Signup_Photo', 6, 0),
(4, 'User_Plugin_Signup_Invite', 5, 1),
(5, 'Payment_Plugin_Signup_Subscription', 7, 0),
(6, 'Zulu_Plugin_Signup_ProfileFields', 2, 1),
(7, 'Zulu_Plugin_Signup_ClinicalFields', 3, 1),
(8, 'Zulu_Plugin_Signup_ProfileSharing', 4, 1),
(9, 'Zulu_Plugin_Signup_Account', 1, 1);

--
-- Table structure for table `engine4_zulu_profileshare`
--

CREATE TABLE IF NOT EXISTS `engine4_zulu_profileshare` (
  `subject_id` int(11) NOT NULL,
  `viewer_id` int(11) NOT NULL,
  `access_level` smallint(6) NOT NULL,
  PRIMARY KEY (`subject_id`,`viewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `engine4_zulu_zulus` (
  `zulu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'queued',
  `status_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expiration_settings` tinyint(1) NOT NULL DEFAULT '0',
  `expiration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `sponsored` tinyint(1) NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`zulu_id`),
  KEY `parent_type_id` (`parent_type`,`parent_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `package_id` (`package_id`),
  KEY `status` (`status`),
  KEY `published` (`published`),
  KEY `featured` (`featured`),
  KEY `sponsored` (`sponsored`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

--
-- Table structure for table `engine4_zulu_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_zulu_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_zulu_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_zulu_fields_meta` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=419 ;

ALTER TABLE `engine4_zulu_fields_meta` MODIFY `label` VARCHAR(510);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_zulu_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_zulu_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=438 ;

DELETE FROM `engine4_zulu_fields_options` WHERE `field_id` = '1' AND `label` = 'E-Medical Record';
INSERT IGNORE INTO `engine4_zulu_fields_options` (`field_id`, `label`) VALUES ('1', 'E-Medical Record');
UPDATE `engine4_zulu_fields_options` SET `option_id` = '0' WHERE `field_id` = '1' AND `label` = 'E-Medical Record';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_zulu_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_zulu_fields_search` (
  `item_id` int(11) unsigned NOT NULL,
  `profile_type` enum('1','230','384','312','5','83','331','180','369','391') COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('2','3') COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `field_279` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_380` enum('484','485','483','482','486','487','488','471','489','490','481','480','479','470','472','469','473','474','475','476','477','478','491','492','505','506','507','508','509','510','511','512','513','504','503','502','493','494','495','496','497','498','499','500','501','514','468','435','436','437','438','439','440','441','442','443','434','433','432','426','427','428','429','430','431','444','445','458','459','460','461','462','463','464','465','466','457','456','455','446','447','448','449','450','451','452','453','454','467','515') COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_400` enum('416','417') COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_401` enum('418','419') COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_402` enum('420','421','422') COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_sport` enum('470','471','472','480','479','477','478','481','482','483','484','485','486','487','488','489','490','491','492','505','506','469','473','474','475','476','507','508','509','510','512','513','504','511','503','502','493','494','495','496','497','498','499','500','501','514','468','435','436','437','438','439','440','441','442','443','434','433','432','426','427','428','429','430','431','444','445','458','459','460','461','462','463','464','465','466','457','456','455','446','453','447','448','449','450','451','452','454','467','515') COLLATE utf8_unicode_ci DEFAULT NULL,
  `PrimarySport` enum('AFL','Alpine','Archery','Athletics','AutoS','Badminton','Baseball','Basketball','Beach_Volleyball','Biathlon','BMX','Bobsleigh','Body_Building','Boxing','Canoe_Slalom','Canoe_Sprint','Cricket','Cross_Country_Running','Cross_Country_Skiing','Curling','Cycling_Road','Cycling_Track','Dance_Contemporary','Dance_Freestyle','Dance_Latin','Dance_Ballet','Darts','Diving','Equestrian_Dressage','Equestrian_Eventing','Equestrian_Jumping','Fencing','Figure_Skating','Fitness_Health','Fitness_Instructors','Football_Soccer','Freestyle_Skiing','Go_Karting','Golf','Gymnastics_Artistic','Gymnastics_Rhythmic','Handball','Hockey','Ice_Hockey','Judo','Karate','Kart_Racing','Kick_Boxing','Kite_Surfing','Lawn_Bowls','Luge','Modern_Pentathlon','Motor_Boat_Racing','Motor_Cycle_Racing','Mountain_Bike','Nordic_Combined','Orienteering','Pool','Rowing','Rugby_League','Rugby_Union','Running','Sailing','Shooting','Short_Track_Speed_Skating','Skeleton','Ski_Jumping','Snooker','Snowboard','Softball','Speed_Skating','Speedway_Racing','Surf_Lifesaving','Surfing','Swimming','Synchronised_Swimming','Table_Tennis','Taekwondo','Ten_Pin_Bowling','Tennis','Touch_Rugby','Trampoline','Triathlon','Volley_Ball','Walking','Water_Polo','Weightlifting','Wrestling_Freestyle','Wrestling_Greco-Roman') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `first_name` (`first_name`),
  KEY `field_279` (`field_279`),
  KEY `profile_type` (`profile_type`),
  KEY `last_name` (`last_name`),
  KEY `country` (`country`),
  KEY `field_380` (`field_380`),
  KEY `primary_sport` (`primary_sport`),
  KEY `PrimarySport` (`PrimarySport`),
  KEY `birthdate` (`birthdate`),
  KEY `gender` (`gender`),
  KEY `field_400` (`field_400`),
  KEY `field_401` (`field_401`),
  KEY `field_402` (`field_402`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_zulu_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_zulu_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `engine4_zulu_fields_xhtml` (
  `field_id` int(11) unsigned NOT NULL,
  `user_edit_row` enum('0','1') NOT NULL DEFAULT '0',
  `field_data` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Update main navigation menus

UPDATE `engine4_core_menuitems` SET `order` = 14 WHERE `name` = 'core_main_resume';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_zulu', 'zulu', 'Medical Record', '', '{"route":"zulu_extended", "module":"zulu", "controller":"edit", "action":"clinical"}', 'core_main', '', 15);

ALTER TABLE `engine4_users`
ADD COLUMN `parent_id` int(11) NOT NULL DEFAULT 0,
ADD INDEX PARENT_ID (`parent_id`)
;

INSERT IGNORE INTO `engine4_core_mailtemplates`(`type`, `module`, `vars`) VALUES('zulu_announcement', 'zulu', '[host],[recipient_title],[object_link]');
INSERT IGNORE INTO `engine4_core_mailtemplates`(`type`, `module`, `vars`) VALUES('zulu_no_profilephoto', 'zulu', '[host],[recipient_title],[object_link]');
-- ALTER TABLE `engine4_core_mail` ADD `designated_sending_time` TIMESTAMP NULL;
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES('Medical Record Announcement', 'zulu', 'Zulu_Plugin_Task_Announcement', 172800);