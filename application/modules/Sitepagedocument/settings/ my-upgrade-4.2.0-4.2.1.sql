UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepagedocument_home","action":"home"}' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_document' LIMIT 1 ;
UPDATE `engine4_core_pages` SET `name` = 'sitepagedocument_index_browse' WHERE `engine4_core_pages`.`name` ='sitepagedocument_index_documentlist' LIMIT 1 ;

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagedocument', 'documents', 'recent_pagedocuments', 'Recent', '1', '1', '24'),
('sitepagedocument', 'documents', 'liked_pagedocuments', 'Most Liked', '1', '2', '24'),
('sitepagedocument', 'documents', 'viewed_pagedocuments', 'Most Viewed', '1', '3', '24'),
('sitepagedocument', 'documents', 'commented_pagedocuments', 'Most Commented', '0', '4', '24'),
('sitepagedocument', 'documents', 'featured_pagedocuments', 'Featured', '0', '5', '24'),
('sitepagedocument', 'documents', 'random_pagedocuments', 'Random', '0', '6', '24');

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `custom`,`order` )VALUES
('sitepagedocument_gutter_documentofday', 'sitepagedocument', 'Make Document of the Day', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 4),
('sitepagedocument_gutter_share', 'sitepagedocument', 'Share Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 3),
('sitepagedocument_gutter_download', 'sitepagedocument', 'Download Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 3),
('sitepagedocument_gutter_email', 'sitepagedocument', 'Email Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 3),
('sitepagedocument_gutter_report', 'sitepagedocument', 'Report Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 3);