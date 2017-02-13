-- SQL querys for Sponsored Story
DROP TABLE IF EXISTS `engine4_communityad_adtype`;
CREATE TABLE IF NOT EXISTS  `engine4_communityad_adtype` (
`adtype_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type` VARCHAR( 255 ) NOT NULL ,
`title` VARCHAR( 255 ) NOT NULL ,
`status` INT NOT NULL
) ENGINE = MYISAM;

UPDATE `engine4_communityad_modules` SET `table_name` = 'recipe' WHERE `engine4_communityad_modules`.`module_name` ='recipe' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`)VALUES
('communityad.title', 'Community Ads');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('communityad_main_adboard', 'communityad', 'Ad Board', '', '{"route":"communityad_display","action":"adboard","controller":"display"}', 'communityad_main', 'Communityad_Plugin_Menus::canViewAdvertiesment', 1, 0, 1),
('communityad_main_campaigns', 'communityad', 'My Campaigns', 'Communityad_Plugin_Menus::canManageAdvertiesment', '{"route":"communityad_campaigns","action":"index","controller":"statistics"}', 'communityad_main', '', 1, 0, 2),
('communityad_main_create', 'communityad', 'Create an Ad', 'Communityad_Plugin_Menus::canCreateAdvertiesment', '{"route":"communityad_listpackage","action":"index","controller":"index"}', 'communityad_main', '', 1, 0, 3),
('communityad_main_report', 'communityad', 'Reports', 'Communityad_Plugin_Menus::canManageAdvertiesment', '{"route":"communityad_reports","action":"export-report","controller":"statistics"}', 'communityad_main', '', 1, 0, 4),('communityad_main_help', 'communityad', 'Help & Learn More', '', '{"route":"communityad_help_and_learnmore","action":"help-and-learnmore","controller":"display"}', 'communityad_main', '', 1, 0, 5);

ALTER TABLE `engine4_communityad_userads` ADD `story_type` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `engine4_communityad_package` ADD `type` VARCHAR( 255 ) NOT NULL DEFAULT 'default';
ALTER TABLE  `engine4_communityad_userads` CHANGE  `cads_title`  `cads_title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
ALTER TABLE  `engine4_communityad_modules` ADD  `displayable` INT NOT NULL DEFAULT  '7';
ALTER TABLE `engine4_communityad_userads`  ADD `ad_type` ENUM('default','sponsored_stories') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default' AFTER `userad_id`;