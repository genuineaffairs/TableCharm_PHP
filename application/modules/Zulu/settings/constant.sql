-- Set alias for default questions

UPDATE `engine4_zulu_fields_meta` SET `alias` = 'currently affected' WHERE `label` LIKE 'Are you currently affected by this problem?';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'first occur' WHERE `label` LIKE 'When did this problem first occur?';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'action taken' WHERE `label` LIKE 'What Action Needs to be Taken';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'taking medications' WHERE `label` LIKE 'Are you taking any medications? Include over the counter drugs from your pharmacy or supermarket, vitamins, and dietary supplements.';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'medications list' WHERE `label` LIKE 'Please list the medication';

-- Questions of OTHERS part

UPDATE `engine4_zulu_fields_meta` SET `alias` = 'last visit dentist' WHERE `label` LIKE 'Date of last visit to the dentist';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'wear glasses' WHERE `label` LIKE 'Does your child wear glasses?';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'wear lenses' WHERE `label` LIKE 'Does your child wear contact lenses?';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'see psychologist' WHERE `label` LIKE 'Has your child ever seen a Psychologist or Psychiatrist?';
UPDATE `engine4_zulu_fields_meta` SET `alias` = 'discuss psychologist' WHERE `label` LIKE 'Please discuss with Sister or school psychologist, so that we can provide the necessary support';

UPDATE `engine4_zulu_fields_meta` SET `alias` = `label` WHERE `type` LIKE 'heading';

-- Set alias for user field

UPDATE `engine4_user_fields_meta` SET `alias` = 'have SAID' WHERE `label` LIKE 'Do you have a South African ID Number?';
UPDATE `engine4_user_fields_meta` SET `alias` = 'SA provinces' WHERE `label` LIKE 'South African Rugby Union provinces';

-- Update authorization permission

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'zulu' as `type`,
    'print' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- temporary updates, remove soon

-- ALTER TABLE `engine4_user_fields_search`
-- DROP `primary_sport`,
-- ADD `primary_sport` VARCHAR(255),
-- ADD INDEX (`primary_sport`),
-- DROP `country_of_residence`
-- ADD `country_of_residence` VARCHAR(255),
-- ADD INDEX (`country_of_residence`);
-- 
-- UPDATE `engine4_user_fields_search` SET `primary_sport`=`PrimarySport`, `country_of_residence`=`country`;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('zulu_clinical_edit', 'zulu', 'Edit My Medical Record', 'Zulu_Plugin_Menus', '', 'user_profile', '', 2);

UPDATE engine4_zulu_signup SET `enable` = 0 WHERE `class` IN ('Zulu_Plugin_Signup_ClinicalFields', 'Zulu_Plugin_Signup_ProfileSharing');