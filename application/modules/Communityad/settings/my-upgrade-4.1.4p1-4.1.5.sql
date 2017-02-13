
INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`) VALUES
('sitepage', 'Page', 'sitepage_page', 'title', 'body', 'owner_id', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES
('communityad_notify_admindisapproved', 'communityad', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[userad_title],[userad_description],[userad_owner],[object_link]');