INSERT IGNORE INTO `engine4_core_mailtemplates`(`type`, `module`, `vars`) VALUES('zulu_announcement', 'zulu', '[host],[recipient_title],[object_link]');
INSERT IGNORE INTO `engine4_core_mailtemplates`(`type`, `module`, `vars`) VALUES('zulu_no_profilephoto', 'zulu', '[host],[recipient_title],[object_link]');
-- ALTER TABLE `engine4_core_mail` ADD `designated_sending_time` TIMESTAMP NULL;
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES('Medical Record Announcement', 'zulu', 'Zulu_Plugin_Task_Announcement', 172800);