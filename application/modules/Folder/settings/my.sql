
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_folders`
--

DROP TABLE IF EXISTS `engine4_folder_folders`;
CREATE TABLE `engine4_folder_folders` (
  `folder_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  
  `parent_type` varchar(64) NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,  
  
  `user_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,

  `secret_code` varchar(128) DEFAULT NULL,
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `sponsored` tinyint(1) NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  
  PRIMARY KEY (`folder_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `parent_type_id` (`parent_type`, `parent_id`),
  KEY `featured` (`featured`),
  KEY `sponsored` (`sponsored`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_attachments`
--

DROP TABLE IF EXISTS `engine4_folder_attachments`;
CREATE TABLE `engine4_folder_attachments` (
  `attachment_id` int(11) unsigned NOT NULL auto_increment,
  `folder_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` int(11) unsigned NOT NULL default '0',
  `owner_type` varchar(64) NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  `download_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attachment_id`),
  KEY `folder_id` (`folder_id`),
  KEY `owner_type` (`owner_type`, `owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_categories`
--

DROP TABLE IF EXISTS `engine4_folder_categories`;
CREATE TABLE `engine4_folder_categories` (
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
-- Dumping data for table `engine4_folder_categories`
--

INSERT IGNORE INTO `engine4_folder_categories` (`category_id`, `order`, `user_id`, `category_name`, `description`) VALUES
(1, 1, 0, 'Applications',''),
(2, 2, 0, 'Creatives',''),
(3, 3, 0, 'Documents',''),
(4, 4, 0, 'Forms',''),
(5, 5, 0, 'Resources',''),
(6, 6, 0, 'Others','')
;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_fields_maps`
--

DROP TABLE IF EXISTS `engine4_folder_fields_maps`;
CREATE TABLE `engine4_folder_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `engine4_folder_fields_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_fields_meta`
--

DROP TABLE IF EXISTS `engine4_folder_fields_meta`;
CREATE TABLE `engine4_folder_fields_meta` (
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



-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_fields_options`
--

DROP TABLE IF EXISTS `engine4_folder_fields_options`;
CREATE TABLE `engine4_folder_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_fields_values`
--

DROP TABLE IF EXISTS `engine4_folder_fields_values`;
CREATE TABLE `engine4_folder_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_folder_fields_search`
--

DROP TABLE IF EXISTS `engine4_folder_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_folder_fields_search` (
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

DELETE FROM engine4_core_menus WHERE name LIKE 'folder_%';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('folder_main', 'standard', 'Folder Main Navigation Menu'),
('folder_admin_main', 'standard', 'Folder Admin Main Navigation Menu'),
('folder_admin_epayment_item', 'standard', 'Folder Admin Payment Item Navigation Menu'),
('folder_quick', 'standard', 'Folder Post New Navigation Menu'),
('folder_gutter', 'standard', 'Folder Gutter Navigation Menu'),
('folder_dashboard', 'standard', 'Folder Dashboard Navigation Menu')
;

--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'folder';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_folder', 'folder', 'Folders', '', '{"route":"folder_general"}', 'core_main', '', 4),
('core_sitemap_folder', 'folder', 'Folders', '', '{"route":"folder_general"}', 'core_sitemap', '', 4),

('core_admin_main_plugins_folder', 'folder', 'Folder / File Sharings', '', '{"route":"admin_default","module":"folder","controller":"settings"}', 'core_admin_main_plugins', '', 2),

('authorization_admin_level_folder', 'folder', 'Folder / File Sharings', '', '{"route":"admin_default","module":"folder","controller":"level"}', 'authorization_admin_level', '', 999),

('folder_main_browse', 'folder', 'Browse Folders', 'Folder_Plugin_Menus::canViewFolders', '{"route":"folder_general","action":"browse"}', 'folder_main', '', 1),
('folder_main_manage', 'folder', 'My Folders', 'Folder_Plugin_Menus::canCreateFolders', '{"route":"folder_general","action":"manage"}', 'folder_main', '', 2),
('folder_main_create', 'folder', 'Share New Files', 'Folder_Plugin_Menus::canCreateFolders', '{"route":"folder_general","action":"create"}', 'folder_main', '', 4),

('folder_quick_create', 'folder', 'Share New Files', 'Folder_Plugin_Menus::canCreateFolders', '{"route":"folder_general","action":"create","class":"buttonlink icon_folder_new"}', 'folder_quick', '', 1),

('folder_gutter_create', 'folder', 'Share New Files', 'Folder_Plugin_Menus', '{"route":"folder_general","action":"create","class":"buttonlink icon_folder_create"}', 'folder_gutter', '', 2),
('folder_gutter_edit', 'folder', 'Edit This Folder', 'Folder_Plugin_Menus', '{"route":"folder_specific","action":"edit","class":"buttonlink icon_folder_edit"}', 'folder_gutter', '', 3),
('folder_gutter_delete', 'folder', 'Delete This Folder', 'Folder_Plugin_Menus', '{"route":"folder_specific","action":"delete","class":"buttonlink icon_folder_delete"}', 'folder_gutter', '', 4),

('folder_dashboard_view', 'folder', 'View Folder / Files', 'Folder_Plugin_Menus', '{"route":"folder_profile","action":"index","class":"buttonlink icon_folder_view"}', 'folder_dashboard', '', 1),
('folder_dashboard_edit', 'folder', 'Edit Folder Details', 'Folder_Plugin_Menus', '{"route":"folder_specific","action":"edit","class":"buttonlink icon_folder_edit"}', 'folder_dashboard', '', 2),
('folder_dashboard_manage', 'folder', 'Manage Folder Files', 'Folder_Plugin_Menus', '{"route":"folder_specific","action":"manage","class":"buttonlink icon_folder_manage"}', 'folder_dashboard', '', 3),
('folder_dashboard_upload', 'folder', 'Upload Folder Files', 'Folder_Plugin_Menus', '{"route":"folder_specific","action":"upload","class":"buttonlink icon_folder_upload"}', 'folder_dashboard', '', 4),
('folder_dashboard_delete', 'folder', 'Delete This Folder', 'Folder_Plugin_Menus', '{"route":"folder_specific","action":"delete","class":"buttonlink icon_folder_delete"}', 'folder_dashboard', '', 6),

('folder_admin_main_manage', 'folder', 'View Folders', '', '{"route":"admin_default","module":"folder","controller":"manage"}', 'folder_admin_main', '', 1),
('folder_admin_main_settings', 'folder', 'Global Settings', '', '{"route":"admin_default","module":"folder","controller":"settings"}', 'folder_admin_main', '', 2),
('folder_admin_main_level', 'folder', 'Member Level Settings', '', '{"route":"admin_default","module":"folder","controller":"level"}', 'folder_admin_main', '', 3),
('folder_admin_main_fields', 'folder', 'Folder Questions', '', '{"route":"admin_default","module":"folder","controller":"fields"}', 'folder_admin_main', '', 4),
('folder_admin_main_categories', 'folder', 'Categories', '', '{"route":"admin_default","module":"folder","controller":"categories"}', 'folder_admin_main', '', 5),
('folder_admin_main_faq', 'folder', 'FAQ', '', '{"route":"admin_default","module":"folder","controller":"faq"}', 'folder_admin_main', '', 6)

;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

DELETE FROM `engine4_core_modules` WHERE name = 'folder';

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('folder', 'Folder / File Sharing Plugin', 'This plugin let your member create folder and upload files.', '4.1.8', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--
DELETE FROM `engine4_core_settings` WHERE name LIKE 'folder.%';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('folder.license','XXXX-XXXX-XXXX-XXXX'),
('folder.enabletypes',''),
('folder.disabletypes',''),
('folder.perpage','10'),
('folder.preorder','0')
;


-- --------------------------------------------------------
DELETE FROM `engine4_activity_actiontypes` WHERE module = 'folder';

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('folder_new', 'folder', '{item:$subject} posted a new folder:', 1, 5, 1, 3, 1, 1),
('comment_folder', 'folder', '{item:$subject} commented on {item:$owner}''s {item:$object:folder}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
DELETE FROM `engine4_activity_notificationtypes` WHERE module = 'folder';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('comment_folder', 'folder', '{item:$subject} has commented on your {item:$object:folder}.', 0, ''),
('like_folder', 'folder', '{item:$subject} likes your {item:$object:folder}.', 0, ''),
('commented_folder', 'folder', '{item:$subject} has commented on a {item:$object:folder} you commented on.', 0, ''),
('liked_folder', 'folder', '{item:$subject} has commented on a {item:$object:folder} you liked.', 0, '')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'folder';


-- ALL - except PUBLIC
-- auth_view, auth_comment, auth_html, auth_htmlattrs
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'file_extensions' as `name`,
    3 as `value`,
    'doc, docx, log, txt, csv, pps, ppt, pptx, xml, vcf, mp3, wav, wma, avi, flv, mov, mp4, mpg, rm, swf, vob, wmv, bmp, gif, jpg, jpeg, png, psd, tif, wks, xls, xlsx, db, sql, mdb, pdf, css, htm, html, js, xhtml, ttf, dll, bin, cue, pkg, rar, sit, zip, iso' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'max_folders' as `name`,
    3 as `value`,
    9999 as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');  
  
-- create, style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public'); 
  
-- ADMIN, MODERATOR
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


