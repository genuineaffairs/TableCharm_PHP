
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_epayment_epayments`
--
-- `state` enum('pending','cancelled','failed','incomplete','complete')

DROP TABLE IF EXISTS `engine4_epayment_epayments`;
CREATE TABLE IF NOT EXISTS `engine4_epayment_epayments` (
  `epayment_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,

  `resource_type` varchar(64) NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `package_id` int(11) unsigned NOT NULL,
  
  `payer_name` varchar(128),
  `payer_account` varchar(128),
  
  `transaction_code` varchar(64),
  
  `method` varchar(32) NOT NULL default 'Manual',
  `status` varchar(32) NOT NULL default 'pending',
  
  `amount` decimal(16,2) NOT NULL default '0.00',
  `currency` char(3) NOT NULL DEFAULT 'USD',
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,

  `notes` text,
  `data` longtext,

  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `processed_date` datetime NOT NULL,
  
  PRIMARY KEY  (`epayment_id`),
  KEY `user_id` (`user_id`),
  KEY `resource_type` (`resource_type`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'epayment';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES

('core_admin_main_plugins_epayment', 'epayment', 'ePayments', '', '{"route":"admin_default","module":"epayment","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('epayment_admin_main_settings', 'epayment', 'Global Settings', '', '{"route":"admin_default","module":"epayment","controller":"settings"}', 'epayment_admin_main', '', 2)
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('epayment', 'ePayment', 'ePayment Plugin', '4.0.2', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

DELETE FROM `engine4_core_settings` WHERE name LIKE 'epayment.%';

INSERT IGNORE INTO `engine4_core_settings` (`name` , `value`) VALUES
('epayment.currency', 'USD'),
('epayment.paypalemail', '');



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'epayment';


-- ALL - except PUBLIC
-- auth_view, auth_comment, auth_html, auth_htmlattrs
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- create, style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');   
  
-- ADMIN, MODERATOR
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'epayment' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
