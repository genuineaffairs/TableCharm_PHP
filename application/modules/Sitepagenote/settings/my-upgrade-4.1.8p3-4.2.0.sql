UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object} created a new note:', `displayable` = '6',`is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepagenote_admin_new' LIMIT 1;

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_note', 'sitepagenote', 'Notes', 'Sitepagenote_Plugin_Menus::canViewNotes', '{"route":"sitepagenote_notelist","action":"notelist"}', 'sitepage_main', '', 1, '22');
