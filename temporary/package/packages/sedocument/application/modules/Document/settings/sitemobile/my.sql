

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_document', 'document', 'Documents',  'Document_Plugin_Menus::canViewDocuments', '{"route":"document_home"}', 'core_main', '',70, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('document', 1, 0, 0, 0);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('document_main_home', 'document', 'Documents Home', 'Document_Plugin_Menus::canViewDocuments', '{"route":"document_home"}', 'document_main', '', 1, 1, 1),
('document_main_browse', 'document', 'Browse Documents', 'Document_Plugin_Menus::canViewDocuments', '{"route":"document_browse"}', 'document_main', '', 2, 1, 1),
('document_main_manage', 'document', 'My Documents', 'Document_Plugin_Menus::canCreateDocuments', '{"route":"document_manage"}', 'document_main', '', 3, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('document_index_home', 'Document_Form_Search', 'search', '', '', ''),
('document_index_manage', 'Document_Form_Search', 'search', '', '', ''),
('document_index_browse', 'Document_Form_Search', 'search', '', '', '');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'document_gutter', 'standard', 'Document Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('document_index_view', 'document_gutter', 'document');

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`custom`, `order`, `enable_mobile`,`enable_tablet`) VALUES
('document_gutter_publish', 'document', 'Publish Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 0, 1, 1, 1),
( 'document_gutter_share', 'document', 'Share Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 0, 2, 1, 1),
( 'document_gutter_email', 'document', 'Email Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 0, 3, 1, 1),
( 'document_gutter_report', 'document', 'Report Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 0, 4, 1, 1);