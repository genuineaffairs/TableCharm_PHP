


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_ecalendar', 'ecalendar', 'Ecalendar', '', '{"route":"admin_default","module":"ecalendar", "controller":"settings"}', 'core_admin_main_plugins', NULL, 1, 0, 1);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ecalendar_admin_main_updates', 'ecalendar', 'iPragmatech Plugins', '', '{"route":"admin_default","module":"ecalendar","controller":"settings"}', 'ecalendar_admin_main', '', 1, 0, 999);


INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ecalender_main', 'standard', 'Ecalendar Main Navigation Menu', 999);