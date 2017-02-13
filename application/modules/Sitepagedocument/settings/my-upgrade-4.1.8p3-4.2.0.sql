UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object} created a new document:', `displayable` = '6', `is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepagedocument_admin_new' LIMIT 1;

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `custom`,`order` )VALUES
 ('sitepage_main_document', 'sitepagedocument', 'Documents', 'Sitepagedocument_Plugin_Menus::canViewDocuments', '{"route":"sitepagedocument_documentlist","action":"documentlist"}', 'sitepage_main', '', 1,0, '999'),

('sitepagedocument_gutter_page', 'sitepagedocument', 'Back to Page', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 0),

('sitepagedocument_gutter_publish', 'sitepagedocument', 'Publish Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 1),

('sitepagedocument_gutter_add', 'sitepagedocument', 'Add Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 2),

('sitepagedocument_gutter_edit', 'sitepagedocument', 'Edit Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 3),

('sitepagedocument_gutter_delete', 'sitepagedocument', 'Delete Document', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 4),

('sitepagedocument_gutter_suggest', 'sitepagedocument', 'Suggest to Friends', 'Sitepagedocument_Plugin_Menus', '', 'sitepagedocument_gutter', '', 1, 0, 5);

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitepagedocument_gutter', 'standard', 'Page document View Page Options Menu', 999);