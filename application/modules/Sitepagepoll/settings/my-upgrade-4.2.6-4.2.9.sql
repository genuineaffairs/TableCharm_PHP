INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagepoll_create', 'sitepagepoll', '{item:$subject} has created a page poll {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEPOLL_CREATENOTIFICATION_EMAIL", "sitepagepoll", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");