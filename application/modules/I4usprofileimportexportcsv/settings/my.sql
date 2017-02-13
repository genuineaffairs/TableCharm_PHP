delete from `engine4_core_modules` where `name`='i4usprofileimportexportcsv';

insert into `engine4_core_modules`(`name`,`title`,`description`,`version`,`enabled`,`type`) values ( 'i4usprofileimportexportcsv','I4usprofileimportexportcsv','I4US  Profile Import/Export using CSV File','4.0.0','1','extra');


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES

('core_admin_main_plugins_i4usprofileimportexportcsv', 'i4usprofileimportexportcsv', 'Profile Import/Export using CSV File', '', '{"route":"admin_default","module":"i4usprofileimportexportcsv","controller":"settings"}', 'core_admin_main_plugins', '', 999);