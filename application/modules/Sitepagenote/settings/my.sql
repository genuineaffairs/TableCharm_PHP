/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepagenote', 'Page Notes', 'sitepagenote', '4.7.1', 1, 'extra') ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_note', 'sitepagenote', 'Notes', 'Sitepagenote_Plugin_Menus::canViewNotes', '{"route":"sitepagenote_home","action":"home"}', 'sitepage_main', '', 1, '22');


--
-- Dumping data for table `engine4_seaocore_tabs`
--

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagenote', 'notes', 'recent_pagenotes', 'Recent', '1', '1', '24'),
('sitepagenote', 'notes', 'liked_pagenotes', 'Most Liked', '1', '2', '24'),
('sitepagenote', 'notes', 'viewed_pagenotes', 'Most Viewed', '1', '3', '24'),
('sitepagenote', 'notes', 'commented_pagenotes', 'Most Commented', '0', '4', '24'),
('sitepagenote', 'notes', 'featured_pagenotes', 'Featured', '0', '5', '24'),
('sitepagenote', 'notes', 'random_pagenotes', 'Random', '0', '6', '24'),
('sitepagenote', 'notes', 'random_pagenotes', 'Random', '0', '6', '24');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagenote_create', 'sitepagenote', '{item:$subject} has created a page note {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGENOTE_CREATENOTIFICATION_EMAIL", "sitepagenote", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitepagenote_categories`
--

DROP TABLE IF EXISTS `engine4_sitepagenote_categories` ;
CREATE TABLE IF NOT EXISTS `engine4_sitepagenote_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_sitepagenote_categories`
--

INSERT IGNORE INTO `engine4_sitepagenote_categories` (`title`) VALUES
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