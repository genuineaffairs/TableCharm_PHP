
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_resumes`
--

DROP TABLE IF EXISTS `engine4_resume_resumes`;
CREATE TABLE `engine4_resume_resumes` (
  `resume_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  
  `parent_type` varchar(64) NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,    
  
  `user_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,

  `name` varchar(64) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,

  `location` varchar(128) DEFAULT NULL,

  `status` varchar(32) NOT NULL DEFAULT 'queued',
  `status_date` datetime NOT NULL DEFAULT '0000-00-00',  
  `expiration_settings` tinyint(1) NOT NULL DEFAULT '0',
  `expiration_date` datetime NOT NULL DEFAULT '0000-00-00',
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  
  `published` tinyint(1) NOT NULL DEFAULT '0',
  
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `sponsored` tinyint(1) NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',

  PRIMARY KEY (`resume_id`),
  KEY `parent_type_id` (`parent_type`, `parent_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `package_id` (`package_id`),
  KEY `status` (`status`),
  KEY `published` (`published`),
  KEY `featured` (`featured`),
  KEY `sponsored` (`sponsored`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_packages`
--

DROP TABLE IF EXISTS `engine4_resume_packages`;
CREATE TABLE `engine4_resume_packages` (
  `package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  
  `title` varchar(128) NOT NULL,
  `description` TEXT, 
  `body` LONGTEXT,

  `price` decimal(16,2) NOT NULL default '0.00',
  `duration` int(11) unsigned NOT NULL,
  `duration_type` varchar(16) NOT NULL,
    
  `photo_id` int(10) unsigned NOT NULL default '0',
  
  `featured` tinyint(1) NOT NULL default '0',
  `sponsored` tinyint(1) NOT NULL default '0',
  `auto_process` tinyint(1) NOT NULL default '1',
  
  `allow_upgrade` tinyint(1) NOT NULL default '1',
  `allow_renew` tinyint(1) NOT NULL default '1',

  `enabled` tinyint(1) NOT NULL default '1',
  `order` smallint(3) NOT NULL DEFAULT '999',
  
  PRIMARY KEY (`package_id`),
  KEY `enabled` (`enabled`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_resume_categories`
--

INSERT IGNORE INTO `engine4_resume_packages` (`package_id`, `order`, `title`, `description`, `body`, `price`, `duration`, `duration_type`, `featured`, `sponsored`, `auto_process`, `allow_upgrade`, `allow_renew`) VALUES
(1, 1, 'Standard Basic Listing', 'This is our standard free for basic limited listing.', 'For more details, please contact us.', '0.00', '3', 'month', '0', '0', '1', '0', '0')
;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_categories`
--

DROP TABLE IF EXISTS `engine4_resume_categories`;
CREATE TABLE `engine4_resume_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_name` varchar(128) NOT NULL,
  `description` TEXT NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_resume_categories`
--

INSERT IGNORE INTO `engine4_resume_categories` (`category_id`, `order`, `user_id`, `category_name`, `description`) VALUES
(1, 1, 0, 'Accounting/Finance',''),
(2, 2, 0, 'Admin/Management',''),
(3, 3, 0, 'Biotech/R&D/Science',''),
(4, 4, 0, 'Building/Construction',''),
(5, 5, 0, 'Creative/Design',''),
(6, 6, 0, 'Customer/Help Support',''),
(7, 7, 0, 'Education/Training',''),
(8, 8, 0, 'Engineering/IT',''),
(9, 9, 0, 'Medical/Health',''),
(10, 10, 0, 'Law/Paralegal',''),
(11, 11, 0, 'Sales/Marketing/Retail',''),
(12, 12, 0, 'Other','')
;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_sections`
--

DROP TABLE IF EXISTS `engine4_resume_sections`;
CREATE TABLE `engine4_resume_sections` (
  `section_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL DEFAULT '0',
  `child_type` varchar(128) NOT NULL default 'Text',
  `title` varchar(128) NOT NULL,
  `description` TEXT NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `enabled` int(11) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`section_id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_resume_sections`
--

INSERT IGNORE INTO `engine4_resume_sections` (`section_id`, `order`, `resume_id`, `child_type`, `title`, `description`, `enabled`) VALUES
(1, 1, 0, 'Text', 'Objective', '', 1),
(2, 2, 0, 'Text', 'Qualifications', '', 1),
(3, 3, 0, 'Employment', 'Experience', '', 1),
(4, 4, 0, 'Education', 'Education', '', 1),
(5, 5, 0, 'Text', 'Skills', '', 0),
(6, 6, 0, 'Text', 'References', '', 0),
(7, 7, 0, 'Text', 'Accomplishments', '', 0),
(8, 8, 0, 'Text', 'Activities', '', 0),
(9, 9, 0, 'Text', 'Affiliations', '', 0),
(10, 10, 0, 'Text', 'Core Competencies', '', 0),
(11, 11, 0, 'Text', 'Coursework', '', 0),
(12, 12, 0, 'Text', 'Honors and Awards', '', 0),
(13, 13, 0, 'Text', 'Licenses and Certifications', '', 0),
(14, 14, 0, 'Text', 'Military Experience', '', 0),
(15, 15, 0, 'Text', 'Patents', '', 0),
(16, 16, 0, 'Text', 'Personal Interests', '', 0),
(17, 17, 0, 'Text', 'Publications', '', 0),
(18, 18, 0, 'Text', 'Training', '', 0),
(19, 19, 0, 'Text', 'Custom Section Title', '', 0)
;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_educations`
--

DROP TABLE IF EXISTS `engine4_resume_educations`;
CREATE TABLE `engine4_resume_educations` (
  `education_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL default '0',
  
  `title` varchar(128) NOT NULL,
  `description` text  NOT NULL,
  `keywords` varchar(255),   

  `class_year` int(11) unsigned NOT NULL default '0',
  `concentration`  varchar(128),   
  `minor`  varchar(128),  
  `degree`  varchar(128),  
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,

  `order` smallint(3) NOT NULL DEFAULT '999',
  
  PRIMARY KEY (`education_id`),
  KEY `section_id` (`section_id`),
  KEY `class_year` (`class_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_employments`
--

DROP TABLE IF EXISTS `engine4_resume_employments`;
CREATE TABLE `engine4_resume_employments` (
  `employment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL default '0',

  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  
  `company` varchar(128) DEFAULT NULL,
  `location` varchar(128) DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `is_current` tinyint(4) NOT NULL DEFAULT '0',
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  
  `order` smallint(3) NOT NULL DEFAULT '999',
  
  PRIMARY KEY (`employment_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_fields_maps`
--

DROP TABLE IF EXISTS `engine4_resume_fields_maps`;
CREATE TABLE `engine4_resume_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_resume_fields_maps`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_fields_meta`
--

DROP TABLE IF EXISTS `engine4_resume_fields_meta`;
CREATE TABLE `engine4_resume_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL default '0',
  `show` tinyint(1) unsigned NOT NULL default '1',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_resume_fields_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_fields_options`
--

DROP TABLE IF EXISTS `engine4_resume_fields_options`;
CREATE TABLE `engine4_resume_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_resume_fields_options`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_fields_values`
--

DROP TABLE IF EXISTS `engine4_resume_fields_values`;
CREATE TABLE `engine4_resume_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_fields_search`
--

DROP TABLE IF EXISTS `engine4_resume_fields_search`;
CREATE TABLE `engine4_resume_fields_search` (
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_albums`
--

DROP TABLE IF EXISTS `engine4_resume_albums` ;
CREATE TABLE `engine4_resume_albums` (
  `album_id` int(11) unsigned NOT NULL auto_increment,
  `resume_id` int(11) unsigned NOT NULL,

  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`album_id`),
   KEY `resume_id` (`resume_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_photos`
--

DROP TABLE IF EXISTS `engine4_resume_photos`;
CREATE TABLE `engine4_resume_photos` (
  `photo_id` int(11) unsigned NOT NULL auto_increment,
  `album_id` int(11) unsigned NOT NULL,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,

  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `resume_id` (`resume_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

DELETE FROM engine4_core_menus WHERE name LIKE 'resume_%';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('resume_main', 'standard', 'Resume Main Navigation Menu'),
('resume_admin_main', 'standard', 'Resume Admin Main Navigation Menu'),
('resume_admin_epayment', 'standard', 'Resume Admin Payment Item Navigation Menu'),
('resume_quick', 'standard', 'Resume Post New Navigation Menu'),
('resume_packages', 'standard', 'Resume Post Packages Navigation Menu'),
('resume_gutter', 'standard', 'Resume Gutter Navigation Menu'),
('resume_dashboard', 'standard', 'Resume Dashboard Navigation Menu')
;

--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'resume';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_resume', 'resume', 'Resumes', '', '{"route":"resume_general"}', 'core_main', '', 4),
('core_sitemap_resume', 'resume', 'Resumes', '', '{"route":"resume_general"}', 'core_sitemap', '', 4),

('core_admin_main_plugins_resume', 'resume', 'Resumes', '', '{"route":"admin_default","module":"resume","controller":"settings"}', 'core_admin_main_plugins', '', 2),

('resume_main_browse', 'resume', 'Browse Resumes', 'Resume_Plugin_Menus::canViewResumes', '{"route":"resume_general","action":"browse"}', 'resume_main', '', 1),
('resume_main_manage', 'resume', 'My Resumes', 'Resume_Plugin_Menus::canCreateResumes', '{"route":"resume_general","action":"manage"}', 'resume_main', '', 2),
('resume_main_packages', 'resume', 'Resume Posting Packages', '', '{"route":"resume_package","action":"browse"}', 'resume_main', '', 3),
('resume_main_create', 'resume', 'Post New Resume', 'Resume_Plugin_Menus::canCreateResumes', '{"route":"resume_general","action":"create"}', 'resume_main', '', 4),

('resume_admin_epayment_process', 'resume', 'Process Payment', 'Resume_Plugin_Menus', '{"route":"admin_default","module":"resume","controller":"epayments","action":"process","class":"buttonlink icon_epayment_process"}', 'resume_admin_epayment', '', 1),
('resume_admin_epayment_view', 'resume', 'View Payment', 'Resume_Plugin_Menus', '{"route":"admin_default","module":"resume","controller":"epayments","action":"view","class":"buttonlink icon_epayment_view"}', 'resume_admin_epayment', '', 2),
('resume_admin_epayment_edit', 'resume', 'Edit Payment', 'Resume_Plugin_Menus', '{"route":"admin_default","module":"resume","controller":"epayments","action":"edit","class":"buttonlink icon_epayment_edit"}', 'resume_admin_epayment', '', 3),
('resume_admin_epayment_delete', 'resume', 'Delete Payment', 'Resume_Plugin_Menus', '{"route":"admin_default","module":"resume","controller":"epayments","action":"delete","class":"buttonlink icon_epayment_delete"}', 'resume_admin_epayment', '', 4),

('resume_quick_create', 'resume', 'Post New Resume', 'Resume_Plugin_Menus::canCreateResumes', '{"route":"resume_general","action":"create","class":"buttonlink icon_resume_new"}', 'resume_quick', '', 1),
('resume_packages_start', 'resume', 'Resume Posting Packages', 'Resume_Plugin_Menus', '{"route":"resume_package","action":"browse","class":"buttonlink icon_resume_packages_start"}', 'resume_packages', '', 1),

('resume_gutter_list', 'resume', 'All Submitter Resumes', 'Resume_Plugin_Menus', '{"route":"resume_general","action":"browse","class":"buttonlink icon_resume_viewall"}', 'resume_gutter', '', 1),
('resume_gutter_create', 'resume', 'Post New Resume', 'Resume_Plugin_Menus', '{"route":"resume_general","action":"create","class":"buttonlink icon_resume_create"}', 'resume_gutter', '', 2),
('resume_gutter_edit', 'resume', 'Edit This Resume', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"edit","class":"buttonlink icon_resume_edit"}', 'resume_gutter', '', 3),
('resume_gutter_delete', 'resume', 'Delete This Resume', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"delete","class":"buttonlink icon_resume_delete"}', 'resume_gutter', '', 4),
('resume_gutter_publish', 'resume', 'Publish This Resume', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"publish","class":"buttonlink icon_resume_publish smoothbox"}', 'resume_gutter', '', 5),
('resume_gutter_print', 'resume', 'Print This Resume', 'Resume_Plugin_Menus', '{"route":"resume_profile","class":"buttonlink icon_resume_print"}', 'resume_gutter', '', 6),

('resume_dashboard_view', 'resume', 'View Resume', 'Resume_Plugin_Menus', '{"route":"resume_profile","action":"index","class":"buttonlink icon_resume_view"}', 'resume_dashboard', '', 1),
('resume_dashboard_edit', 'resume', 'Edit Resume Details', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"edit","class":"buttonlink icon_resume_edit"}', 'resume_dashboard', '', 2),
('resume_dashboard_sections', 'resume', 'Manage Resume Sections', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"sections","class":"buttonlink icon_resume_sections"}', 'resume_dashboard', '', 3),
('resume_dashboard_location', 'resume', 'Edit Resume Location', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"location","class":"buttonlink icon_resume_location"}', 'resume_dashboard', '', 4),
('resume_dashboard_photo', 'resume', 'Upload Resume Photos', 'Resume_Plugin_Menus', '{"route":"resume_extended","controller":"photo","action":"upload","class":"buttonlink icon_resume_photo"}', 'resume_dashboard', '', 5),
('resume_dashboard_style', 'resume', 'Edit Resume Style', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"style","class":"buttonlink icon_resume_style"}', 'resume_dashboard', '', 6),
('resume_dashboard_epayments', 'resume', 'Resume Payment History', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"payments","class":"buttonlink icon_resume_epayments"}', 'resume_dashboard', '', 7),
('resume_dashboard_delete', 'resume', 'Delete This Resume', 'Resume_Plugin_Menus', '{"route":"resume_specific","action":"delete","class":"buttonlink icon_resume_delete"}', 'resume_dashboard', '', 8),

('resume_admin_main_manage', 'resume', 'View Resumes', '', '{"route":"admin_default","module":"resume","controller":"manage"}', 'resume_admin_main', '', 1),
('resume_admin_main_settings', 'resume', 'Global Settings', '', '{"route":"admin_default","module":"resume","controller":"settings"}', 'resume_admin_main', '', 2),
('resume_admin_main_level', 'resume', 'Member Level Settings', '', '{"route":"admin_default","module":"resume","controller":"level"}', 'resume_admin_main', '', 3),
('resume_admin_main_fields', 'resume', 'Resume Questions', '', '{"route":"admin_default","module":"resume","controller":"fields"}', 'resume_admin_main', '', 4),
('resume_admin_main_categories', 'resume', 'Categories', '', '{"route":"admin_default","module":"resume","controller":"categories"}', 'resume_admin_main', '', 5),
('resume_admin_main_sections', 'resume', 'Sections', '', '{"route":"admin_default","module":"resume","controller":"sections"}', 'resume_admin_main', '', 6),
('resume_admin_main_packages', 'resume', 'Packages', '', '{"route":"admin_default","module":"resume","controller":"packages"}', 'resume_admin_main', '', 7),
('resume_admin_main_epayments', 'resume', 'Payments', '', '{"route":"admin_default","module":"resume","controller":"epayments"}', 'resume_admin_main', '', 8),
('resume_admin_main_faq', 'resume', 'FAQ', '', '{"route":"admin_default","module":"resume","controller":"faq"}', 'resume_admin_main', '', 9)
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

DELETE FROM `engine4_core_modules` WHERE name = 'resume';

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('resume', 'Resume / Curriculum Vitae Plugin', 'This plugin let your member create resume on your social networking website.', '4.0.5', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--
DELETE FROM `engine4_core_settings` WHERE name LIKE 'resume.%';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('resume.license','XXXX-XXXX-XXXX-XXXX'),
('resume.perpage','10'),
('resume.preorder','1'),
('resume.distanceunit','ml');


-- --------------------------------------------------------
DELETE FROM `engine4_activity_actiontypes` WHERE module = 'resume';

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('resume_new', 'resume', '{item:$subject} posted a new resume:', 1, 5, 1, 3, 1, 1),
('comment_resume', 'resume', '{item:$subject} commented on {item:$owner}''s {item:$object:resume}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
DELETE FROM `engine4_activity_notificationtypes` WHERE module = 'resume';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('resume_status_update', 'resume', 'Your {item:$object} resume status has been updated to {var:$status}.', 0, '')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'resume';


-- ALL - except PUBLIC
-- auth_view, auth_comment, auth_html, auth_htmlattrs
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'auth_photo' as `name`,
    5 as `value`,
    '["owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'max_resumes' as `name`,
    3 as `value`,
    9999 as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');  
  
-- create, style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');   
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');    
  
-- ADMIN, MODERATOR
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'photo' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');  
  
-- USER
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');  
  
-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'resume' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


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

ALTER TABLE `engine4_resume_sections` ADD COLUMN `default_in_categories` VARCHAR(255);