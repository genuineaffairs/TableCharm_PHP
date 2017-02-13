INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`,
`submenu`, `enabled`, `custom`, `order`) VALUES
('sitepageevent_admin_main_locations','sitepageevent', 'Page Event Locations', '','{"route":"admin_default","module":"sitepageevent","controller":"settings","action":"locations"}', 'sitepageevent_admin_main', '', '1', '0', '5');

UPDATE `engine4_core_menuitems` SET  `order` =  '999' WHERE  `engine4_core_menuitems`.`name` ='sitepageevent_admin_main_faq';

ALTER TABLE `engine4_sitepageevent_events` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;