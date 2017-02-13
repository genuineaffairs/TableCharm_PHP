-- Insert friends menu

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_friend', 'user', 'Friends', '', '{"route":"user_friend"}', 'core_main', '', 7);
