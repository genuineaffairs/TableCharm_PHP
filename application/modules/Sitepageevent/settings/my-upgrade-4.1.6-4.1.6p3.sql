UPDATE `engine4_activity_actiontypes` SET `enabled` = '0', `is_generated` = '1'  WHERE `engine4_activity_actiontypes`.`type` = 'sitepageevent_admin_new' LIMIT 1; 


UPDATE `engine4_activity_actiontypes` SET `module` = 'sitepageevent' WHERE `engine4_activity_actiontypes`.`type` = 'sitepageevent_new' LIMIT 1 ;