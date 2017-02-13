/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
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
('sitepagepoll', 'Page Polls', 'Sitepage Polls', '4.7.0', 1, 'extra');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_poll', 'sitepagepoll', 'Polls', 'Sitepagepoll_Plugin_Menus::canViewPolls', '{"route":"sitepagepoll_browse","action":"browse"}', 'sitepage_main', '', 1, '999');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagepoll_create', 'sitepagepoll', '{item:$subject} has created a page poll {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEPOLL_CREATENOTIFICATION_EMAIL", "sitepagepoll", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");