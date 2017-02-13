UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' , `is_generated` = '1'  WHERE `engine4_activity_actiontypes`.`type` = 'sitepagedocument_admin_new' LIMIT 1 ;


UPDATE `engine4_activity_actiontypes` SET `module` = 'sitepagedocument' WHERE `engine4_activity_actiontypes`.`type` = 'sitepagedocument_new' LIMIT 1 ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepagedocument_document' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepagedocument_document' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- --------------------------------------------------------