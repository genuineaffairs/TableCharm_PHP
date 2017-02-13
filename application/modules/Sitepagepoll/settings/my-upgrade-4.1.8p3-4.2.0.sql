UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object} added a new poll', `displayable` = '6',`is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepagepoll_admin_new' LIMIT 1;

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_poll', 'sitepagepoll', 'Polls', 'Sitepagepoll_Plugin_Menus::canViewPolls', '{"route":"sitepagepoll_polllist","action":"polllist"}', 'sitepage_main', '', 1, '999');