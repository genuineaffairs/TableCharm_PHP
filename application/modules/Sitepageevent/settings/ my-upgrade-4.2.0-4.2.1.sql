UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepageevent_home","action":"home"}' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_event' LIMIT 1 ;
UPDATE `engine4_core_pages` SET `name` = 'sitepageevent_index_browse' WHERE `engine4_core_pages`.`name` ='sitepageevent_index_eventlist' LIMIT 1 ;

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepageevent', 'events', 'recent_pageevents', 'Upcoming', '1', '1', '24'),
('sitepageevent', 'events', 'member_pageevents', 'Most Joined', '1', '2', '24'),
('sitepageevent', 'events', 'viewed_pageevents', 'Most Viewed', '1', '3', '24'),
('sitepageevent', 'events', 'featured_pageevents', 'Featured', '0', '4', '24'),
('sitepageevent', 'events', 'random_pageevents', 'Random', '0', '6', '24');