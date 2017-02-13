UPDATE `engine4_activity_actiontypes` SET  `body` = '{item:$object} created a new event:', `displayable` = '6',`is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepageevent_admin_new' LIMIT 1;

INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', 'sitepageevent_event', 'view', '1', NULL);

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_event', 'sitepageevent', 'Events', 'Sitepageevent_Plugin_Menus::canViewEvents', '{"route":"sitepageevent_eventlist","action":"eventlist"}', 'sitepage_main', '', 1, '999');