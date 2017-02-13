INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepageevent_create', 'sitepageevent', '{item:$subject} has created a page event {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEEVENT_CREATENOTIFICATION_EMAIL", "sitepageevent", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitepageevent_categories`
--

DROP TABLE IF EXISTS `engine4_sitepageevent_categories` ;
CREATE TABLE IF NOT EXISTS `engine4_sitepageevent_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_sitepageevent_categories`
--

INSERT IGNORE INTO `engine4_sitepageevent_categories` (`title`) VALUES
('Arts'),
('Business'),
('Conferences'),
('Festivals'),
('Food'),
('Fundraisers'),
('Galleries'),
('Health'),
('Just For Fun'),
('Kids'),
('Learning'),
('Literary'),
('Movies'),
('Museums'),
('Neighborhood'),
('Networking'),
('Nightlife'),
('On Campus'),
('Organizations'),
('Outdoors'),
('Pets'),
('Politics'),
('Sales'),
('Science'),
('Spirituality'),
('Sports'),
('Technology'),
('Theatre'),
('Other');


-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitepageevent_admin_main_categories', 'sitepageevent', 'Categories', '', '{"route":"admin_default","module":"sitepageevent","controller":"settings","action":"categories"}', 'sitepageevent_admin_main', '', 4);

