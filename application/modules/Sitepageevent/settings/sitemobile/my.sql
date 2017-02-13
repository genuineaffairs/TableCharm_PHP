
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES ('sitepageevent_gutter', 'standard', 'Page Event Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitepageevent_photo', 'standard', 'Page Event Photo View Options Menu', '999');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitepage_main_event', 'sitepageevent', 'Events', 'Sitepageevent_Plugin_Menus::canViewEvents', '{"route":"sitepageevent_browse","action":"browse"}', 'sitepage_main', '','0','80', 1, 1),
('sitepageevent_gutter_create', 'sitepageevent', 'Create Event', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 0, 1,1,1),
('sitepageevent_gutter_edit', 'sitepageevent', 'Edit Event', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 0, 2,1,1),
('sitepageevent_gutter_member', 'sitepageevent', 'Member', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 0, 4,1,1),
('sitepageevent_gutter_share', 'sitepageevent', 'Share', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 0, 5,1,1),
('sitepageevent_gutter_invite', 'sitepageevent', 'Invite', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 0, 6,1,1),
('sitepageevent_gutter_invite_members', 'sitepageevent', 'Invite Members', 'Sitepageevent_Plugin_Menus', '{"route":"sitepageevent_specific", "class":"buttonlink smoothbox sitepageevent_gutter_invite","action":"invite-members"}', 'sitepageevent_gutter', '', 0, 8,1,1);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
 ('sitepageevent_photo_edit', 'sitepageevent', 'Edit', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_photo', NULL, '0', '1', '1', '1'),
 ('sitepageevent_photo_delete', 'sitepageevent', 'Delete', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_photo', NULL, '0', '2', '1', '1'),
('sitepageevent_photo_share', 'sitepageevent', 'Share', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_photo', NULL, '0', '3', '1', '1'),
('sitepageevent_photo_report', 'sitepageevent', 'Report', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_photo', NULL, '0', '4', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepageevent_index_browse', 'Sitepageevent_Form_Searchwidget', 'search_event', '', '', '');
-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepageevent_index_view', 'sitepageevent_gutter', 'sitepageevent_event'),
('sitepageevent_photo_view', 'sitepageevent_photo', 'sitepageevent_photo');