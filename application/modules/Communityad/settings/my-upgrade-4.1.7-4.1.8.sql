--
-- Dumping data for table `engine4_core_menus`
--
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES ('communityad_main', 'standard', 'Advertising Main Navigation Menu', '999');

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('communityad_main_adboard', 'communityad', 'Ad Board', '', '{"route":"communityad_display","action":"adboard","controller":"display"}', 'communityad_main', 'Communityad_Plugin_Menus::canViewAdvertiesment', 1, 0, 1),
('communityad_main_campaigns', 'communityad', 'My Campaigns', 'Communityad_Plugin_Menus::canManageAdvertiesment', '{"route":"communityad_campaigns","action":"index","controller":"statistics"}', 'communityad_main', '', 1, 0, 2),
('communityad_main_create', 'communityad', 'Create an Ad', 'Communityad_Plugin_Menus::canCreateAdvertiesment', '{"route":"communityad_listpackage","action":"index","controller":"index"}', 'communityad_main', '', 1, 0, 3),
('communityad_main_report', 'communityad', 'Reports', 'Communityad_Plugin_Menus::canManageAdvertiesment', '{"route":"communityad_reports","action":"export-report","controller":"statistics"}', 'communityad_main', '', 1, 0, 4),('communityad_main_help', 'communityad', 'Help & Learn More', '', '{"route":"communityad_help_and_learnmore","action":"help-and-learnmore","controller":"display"}', 'communityad_main', '', 1, 0, 5);
