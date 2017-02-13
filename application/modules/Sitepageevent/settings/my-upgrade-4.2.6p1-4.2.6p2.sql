UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_member' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_share' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '6' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_invite' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '7' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_backtopage' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '8' WHERE `engine4_core_menuitems`.`name` ='sitepageevent_gutter_day' LIMIT 1 ;

INSERT IGNORE  INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'sitepageevent_gutter_editlocation', 'sitepageevent', 'Edit Location', 'Sitepageevent_Plugin_Menus', '', 'sitepageevent_gutter', '', 1, 0, 3);