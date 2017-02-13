INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagedocument_create', 'sitepagedocument', '{item:$subject} has created a page document {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEDOCUMENT_CREATENOTIFICATION_EMAIL", "sitepagedocument", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");