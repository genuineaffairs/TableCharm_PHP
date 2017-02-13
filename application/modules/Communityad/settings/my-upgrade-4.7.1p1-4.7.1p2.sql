ALTER TABLE `engine4_communityad_adtype` ADD INDEX ( `type` ); 

ALTER TABLE `engine4_communityad_adcancels` ADD INDEX ( `user_id` );

ALTER TABLE `engine4_communityad_faqs` ADD INDEX ( `type` );

ALTER TABLE `engine4_communityad_likes` ADD INDEX ( `poster_id` , `ad_id` ) ;

ALTER TABLE `engine4_communityad_modules` ADD INDEX ( `module_name` ); 

ALTER TABLE `engine4_communityad_package` ADD INDEX ( `type` ); 

ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `ad_type` ); 
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `package_id` );
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `campaign_id` );
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `owner_id` ); 
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `sponsored` ); 
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `featured` ); 
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `approved` , `enable` , `status` , `declined` );
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `public` , `approved` , `enable` , `status` , `declined`  );
ALTER TABLE `engine4_communityad_userads` ADD INDEX ( `story_type` );

ALTER TABLE `engine4_communityad_adcampaigns` ADD INDEX ( `owner_id` ); 

ALTER TABLE `engine4_communityad_adcampaigns`
  DROP `end_settings`,
  DROP `start_time`,
  DROP `end_time`,
  DROP `limit_view`,
  DROP `limit_click`,
  DROP `limit_ctr`,
  DROP `network`,
  DROP `level`,
  DROP `views`,
  DROP `clicks`,
  DROP `public`;

ALTER TABLE `engine4_communityad_userads`
  DROP `placement`;
ALTER TABLE `engine4_communityad_package`
  DROP `placement`;

DROP TABLE `engine4_communityad_pagesettings`;