/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('sitepagedocument', 'Page Documents', 'Page Documents', '4.8.2p1', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepagedocument_document' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepagedocument_document' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `custom`,`order` )VALUES
 ('sitepage_main_document', 'sitepagedocument', 'Documents', 'Sitepagedocument_Plugin_Menus::canViewDocuments', '{"route":"sitepagedocument_home","action":"home"}', 'sitepage_main', '', 1,0, '999'),

('sitepagedocument_gutter_page', 'sitepagedocument', 'Back to Page', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 0),

('sitepagedocument_gutter_publish', 'sitepagedocument', 'Publish Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 1),

('sitepagedocument_gutter_add', 'sitepagedocument', 'Add Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 2),

('sitepagedocument_gutter_edit', 'sitepagedocument', 'Edit Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 3),

('sitepagedocument_gutter_share', 'sitepagedocument', 'Share Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 4),

('sitepagedocument_gutter_download', 'sitepagedocument', 'Download Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 5),

('sitepagedocument_gutter_email', 'sitepagedocument', 'Email Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 6),

('sitepagedocument_gutter_documentofday', 'sitepagedocument', 'Make Document of the Day', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 7),

('sitepagedocument_gutter_suggest', 'sitepagedocument', 'Suggest to Friends', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 8),

('sitepagedocument_gutter_report', 'sitepagedocument', 'Report Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 9),

('sitepagedocument_gutter_delete', 'sitepagedocument', 'Delete Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 10);

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitepagedocument_gutter', 'standard', 'Page document View Page Options Menu', 999);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_seaocore_tabs`
--

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagedocument', 'documents', 'recent_pagedocuments', 'Recent', '1', '1', '24'),
('sitepagedocument', 'documents', 'liked_pagedocuments', 'Most Liked', '1', '2', '24'),
('sitepagedocument', 'documents', 'viewed_pagedocuments', 'Most Viewed', '1', '3', '24'),
('sitepagedocument', 'documents', 'commented_pagedocuments', 'Most Commented', '0', '4', '24'),
('sitepagedocument', 'documents', 'featured_pagedocuments', 'Featured', '0', '5', '24'),
('sitepagedocument', 'documents', 'random_pagedocuments', 'Random', '0', '6', '24');
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagedocument_create', 'sitepagedocument', '{item:$subject} has created a page document {item:$object}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEDOCUMENT_CREATENOTIFICATION_EMAIL", "sitepagedocument", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitepagedocument_categories`
--

DROP TABLE IF EXISTS `engine4_sitepagedocument_categories` ;
CREATE TABLE IF NOT EXISTS `engine4_sitepagedocument_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_sitepagedocument_categories`
--

INSERT IGNORE INTO `engine4_sitepagedocument_categories` (`title`) VALUES
('Arts'),
('Business'),
('Conferences'),
('Festivals'),
('Food'),
('Fundraisers'),
('Galleries'),
('Health'),
('Just For Fun'),
('Kids'),
('Learning'),
('Literary'),
('Movies'),
('Museums'),
('Neighborhood'),
('Networking'),
('Nightlife'),
('On Campus'),
('Organizations'),
('Outdoors'),
('Pets'),
('Politics'),
('Sales'),
('Science'),
('Spirituality'),
('Sports'),
('Technology'),
('Theatre'),
('Other');

-- --------------------------------------------------------