INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`) VALUES
('recipe', 'Recipe', 'recipe', 'title', 'body', 'owner_id', 1);

-- -----------------------------------------------------------------------------------

ALTER TABLE `engine4_communityad_userads` CHANGE `cads_url` `cads_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `cads_title` `cads_title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `cads_body` `cads_body` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `placement` `placement` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `resource_type` `resource_type` VARCHAR( 65 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `payment_status` `payment_status` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'free',
CHANGE `price_model` `price_model` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `gateway_profile_id` `gateway_profile_id` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;