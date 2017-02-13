/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
--
-- Dumping data for table `engine4_core_modules`
--

INSERT  IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('communityad', 'CommunityAds', 'Create a plugin for Social Engine for providing ads', '4.8.2', 1, 'extra');

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('communityad_main_adboard', 'communityad', 'Ad Board', 'Communityad_Plugin_Menus::canViewAdvertiesment', '{"route":"communityad_display","action":"adboard","controller":"display"}', 'communityad_main', '', 1, 0, 1),
('communityad_main_campaigns', 'communityad', 'My Campaigns', 'Communityad_Plugin_Menus::canManageAdvertiesment', '{"route":"communityad_campaigns","action":"index","controller":"statistics"}', 'communityad_main', '', 1, 0, 2),
('communityad_main_create', 'communityad', 'Create an Ad', 'Communityad_Plugin_Menus::canCreateAdvertiesment', '{"route":"communityad_listpackage","action":"index","controller":"index"}', 'communityad_main', '', 1, 0, 3),
('communityad_main_report', 'communityad', 'Reports', 'Communityad_Plugin_Menus::canManageAdvertiesment', '{"route":"communityad_reports","action":"export-report","controller":"statistics"}', 'communityad_main', '', 1, 0, 4),('communityad_main_help', 'communityad', 'Help & Learn More', '', '{"route":"communityad_help_and_learnmore","action":"help-and-learnmore","controller":"display"}', 'communityad_main', '', 1, 0, 5);

ALTER TABLE `engine4_communityad_package` CHANGE `price` `price` DECIMAL( 16, 2 ) NULL DEFAULT '0';

INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`) VALUES
('sitestore', 'Store', 'sitestore_store', 'title', 'body', 'owner_id', 1),
('sitestoreproduct', 'Product', 'sitestoreproduct_product', 'title', 'body', 'owner_id', 1);
