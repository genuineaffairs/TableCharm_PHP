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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES ('sitepagenote_profile', 'standard', 'Page Note Profile Options Menu', '999'),('sitepagenote_photo', 'standard', 'Page Note Photo View Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitepage_main_note', 'sitepagenote', 'Notes', 'Sitepagenote_Plugin_Menus::canViewNotes', '{"route":"sitepagenote_browse","action":"browse"}', 'sitepage_main', '','0','90', '1', '1'),
('sitepagenote_write', 'sitepagenote', 'Write a Note', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_profile', NULL, '0', '1', '1', '1'),
('sitepagenote_publish', 'sitepagenote', 'Publish Note', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_profile', NULL, '0', '2', '1', '1'),
('sitepagenote_edit', 'sitepagenote', 'Edit Note', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_profile', NULL, '0', '3', '1', '1'),
('sitepagenote_delete', 'sitepagenote', 'Delete Note', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_profile', NULL, '0', '4', '1', '1'),
('sitepagenote_add', 'sitepagenote', 'Add Photos', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_profile', NULL, '0', '5', '1', '1'),
('sitepagenote_photo_edit', 'sitepagenote', 'Edit', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_photo', NULL, '0', '1', '1', '1'),
('sitepagenote_photo_delete', 'sitepagenote', 'Delete', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_photo', NULL, '0', '2', '1', '1'),
('sitepagenote_photo_share', 'sitepagenote', 'Share', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_photo', NULL, '0', '3', '1', '1'),
('sitepagenote_photo_report', 'sitepagenote', 'Report', 'Sitepagenote_Plugin_Menus', '', 'sitepagenote_photo', NULL, '0', '4', '1', '1');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_searchform`
--

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepagenote_index_browse', 'Sitepagenote_Form_Searchwidget', 'search_note', '', '', '');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_navigation`
--


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepagenote_index_view', 'sitepagenote_profile', 'sitepagenote_note'),
('sitepagenote_photo_view', 'sitepagenote_photo', 'sitepagenote_photo');