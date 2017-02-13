/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepageevent', 'Page Events', 'Sitepageevent', '4.8.2', 1, 'extra') ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` ,`custom`, `order` )VALUES
('sitepage_main_event', 'sitepageevent', 'Events', 'Sitepageevent_Plugin_Menus::canViewEvents', '{"route":"sitepageevent_home","action":"home"}', 'sitepage_main', '', 1,0,'999'),
('sitepageevent_gutter_day', 'sitepageevent', 'Make Event of the Day', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 1, 0, 7);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_seaocore_tabs`
--

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepageevent', 'events', 'recent_pageevents', 'Upcoming', '1', '1', '24'),
('sitepageevent', 'events', 'member_pageevents', 'Most Joined', '1', '2', '24'),
('sitepageevent', 'events', 'viewed_pageevents', 'Most Viewed', '1', '3', '24'),
('sitepageevent', 'events', 'featured_pageevents', 'Featured', '0', '4', '24'),
('sitepageevent', 'events', 'random_pageevents', 'Random', '0', '6', '24');

UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_member' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_share' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '6' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_invite' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '7' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_backtopage' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '8' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_day' LIMIT 1 ;

INSERT IGNORE  INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'sitepageevent_gutter_editlocation', 'sitepageevent', 'Edit Location', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 1, 0, 3);


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepageevent_create', 'sitepageevent', '{item:$subject} has created a page event {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEEVENT_CREATENOTIFICATION_EMAIL", "sitepageevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEEVENT_INVITE_EMAIL", "sitepageevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('sitepageevent_gutter_invite_members', 'sitepageevent', 'Invite Members', 'Sitepageevent_Plugin_Menus', '{"route":"sitepageevent_specific", "class":"buttonlink smoothbox sitepageevent_gutter_invite","action":"invite-members"}', 'sitepageevent_gutter', '', 0, 8);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitepageevent_categories`
--

DROP TABLE IF EXISTS `engine4_sitepageevent_categories` ;
CREATE TABLE IF NOT EXISTS `engine4_sitepageevent_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_sitepageevent_categories`
--

INSERT IGNORE INTO `engine4_sitepageevent_categories` (`title`) VALUES
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