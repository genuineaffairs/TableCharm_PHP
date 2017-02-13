INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ecalendar', 'Event Calendar', 'This module allows to display events in the monthly,weekly calendar format', '4.2.4', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('mycalendar', 'core', 'My Calendar', '', '{"route":"ecalendar_general","module":"ecalendar","controller":"index"}', 'event_main', '', 1, 1, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('allcalendarevents', 'core', 'Calendar- All Events', '', '{"route":"ecalendar_general","module":"ecalendar","controller":"index","action":"allevents"}', 'event_main', '', 1, 1, 999);