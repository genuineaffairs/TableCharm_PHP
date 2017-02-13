INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ecalendar', 'Event Calendar', 'This module allows to display events in the monthly calendar format', '4.2.4', 1, 'extra') ;

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('mycalendar', 'core', 'My Calendar', '', '{"route":"ecalendar_general","module":"ecalendar","controller":"index"}', 'event_main', '', 1, 1, 999);

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('all_calendar_events', 'core', 'Calendar- All Events', '', '{"route":"ecalendar_general","module":"ecalendar","controller":"index","action":"allevents"}', 'event_main', '', 1, 1, 999);

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_ecalendar', 'ecalendar', 'Ecalendar', '', '{"route":"admin_default","module":"ecalendar", "controller":"settings"}', 'core_admin_main_plugins', NULL, 1, 0, 1);

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ecalendar_admin_main_settings', 'ecalendar', 'iPragmatech Plugins', '', '{"route":"admin_default","module":"ecalendar","controller":"settings"}', 'ecalendar_admin_main', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ecalendar_main', 'standard', 'Ecalendar Main Navigation Menu', 999);