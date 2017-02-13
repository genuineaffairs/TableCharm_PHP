INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagenote_create', 'sitepagenote', '{item:$subject} has created a page note {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGENOTE_CREATENOTIFICATION_EMAIL", "sitepagenote", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");