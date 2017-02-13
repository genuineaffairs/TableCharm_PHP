
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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitepagepoll_profile', 'standard', 'Page Poll Profile Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('sitepage_main_poll', 'sitepagepoll', 'Polls', 'Sitepagepoll_Plugin_Menus::canViewPolls', '{"route":"sitepagepoll_browse","action":"browse"}', 'sitepage_main', '','0', '120', '1', '1'),
('sitepagepoll_share', 'sitepagepoll', 'Share', 'Sitepagepoll_Plugin_Menus', '', 'sitepagepoll_profile', NULL, '0', '1', '1', '1'),
('sitepagepoll_report', 'sitepagepoll', 'Report', 'Sitepagepoll_Plugin_Menus', '', 'sitepagepoll_profile', NULL, '0', '2', '1', '1'),
('sitepagepoll_create', 'sitepagepoll', 'Create Poll', 'Sitepagepoll_Plugin_Menus', '', 'sitepagepoll_profile', NULL, '0', '3', '1', '1'),
('sitepagepoll_delete', 'sitepagepoll', 'Delete Poll', 'Sitepagepoll_Plugin_Menus', '', 'sitepagepoll_profile', NULL, '0', '4', '1', '1');

-- --------------------------------------------------------


INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepagepoll_index_browse', 'Sitepagepoll_Form_Searchwidget', 'search_poll', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepagepoll_index_view', 'sitepagepoll_profile', 'sitepagepoll_poll');