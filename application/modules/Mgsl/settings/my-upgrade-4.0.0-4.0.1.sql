UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE `name` = 'mgsl_browse_members';

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('mgsl_print_sharing_med', 'mgsl', 'Print list of MGSL users whose Medical Record I have access to', 'Mgsl_Plugin_Menus', '{"route":"mgsl_general","controller":"index","action":"print-sharing-medical","icon":"application/modules/Mgsl/externals/images/print.png"}', 'mgsl_quick', '', 1, 1, 1);