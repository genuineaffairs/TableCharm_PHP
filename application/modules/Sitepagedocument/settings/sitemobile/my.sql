
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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitepagedocument_gutter', 'standard', 'Page Document Profile Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`custom`, `order`, `enable_mobile`,`enable_tablet`) VALUES
('sitepage_main_document', 'sitepagedocument', 'Documents', 'Sitepagedocument_Plugin_Menus::canViewDocuments', '{"route":"sitepagedocument_browse","action":"browse"}', 'sitepage_main', '','0','100', 1, 1),
('sitepagedocument_gutter_publish', 'sitepagedocument', 'Publish Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 1, 1, 1),
('sitepagedocument_gutter_add', 'sitepagedocument', 'Add Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 2, 1, 1),
( 'sitepagedocument_gutter_edit', 'sitepagedocument', 'Edit Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 3, 1, 1),
( 'sitepagedocument_gutter_share', 'sitepagedocument', 'Share Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 4, 1, 1),
( 'sitepagedocument_gutter_download', 'sitepagedocument', 'Download Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 5, 1, 1),
( 'sitepagedocument_gutter_email', 'sitepagedocument', 'Email Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 6, 1, 1),
( 'sitepagedocument_gutter_report', 'sitepagedocument', 'Report Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 9, 1, 1),
( 'sitepagedocument_gutter_delete', 'sitepagedocument', 'Delete Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 0, 10, 1, 1);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepagedocument_index_browse', 'Sitepagedocument_Form_Searchwidget', 'search_document', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepagedocument_index_view', 'sitepagedocument_gutter', 'sitepagedocument_document');