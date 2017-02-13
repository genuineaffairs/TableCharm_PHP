--
-- Table structure for table `engine4_resume_awards`
--

CREATE TABLE IF NOT EXISTS `engine4_resume_awards` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL DEFAULT '0',
  `award` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `competition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`activity_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_coachinghistories`
--

CREATE TABLE IF NOT EXISTS `engine4_resume_coachinghistories` (
  `history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL DEFAULT '0',
  `team_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `competition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `is_current` tinyint(4) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`history_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_qualifications`
--

CREATE TABLE IF NOT EXISTS `engine4_resume_qualifications` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL DEFAULT '0',
  `team_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `competition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `is_current` tinyint(4) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`activity_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_resume_sportinghistories`
--

CREATE TABLE IF NOT EXISTS `engine4_resume_sportinghistories` (
  `history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL DEFAULT '0',
  `team_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `competition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `is_current` tinyint(4) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`history_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- DUMPING DEFAULT RESUME SECTIONS

-- DELETE FROM `engine4_resume_sections` WHERE `resume_id` = 0;
-- 
-- INSERT INTO `engine4_resume_sections` (`resume_id`, `child_type`, `title`, `description`, `photo_id`, `enabled`, `order`, `default_in_categories`) VALUES
-- (0, 'Text', 'Career Objective', '', 0, 1, 1, '[13,14,32,15,17]'),
-- (0, 'Text', 'Currently Seeking', 'Enter details of what you are seeking, ie. \r\n•	Contract\r\n•	Semi Pro\r\n•	Academy\r\n•	Scholarship\r\n•	Bursary\r\n•	Job\r\n•	Gap Year', 0, 1, 3, '[13,14,32,15,17,18]'),
-- (0, 'Sportinghistory', 'Sporting History', '', 0, 1, 6, '[13,14,32]'),
-- (0, 'Text', 'Current Agent or Manager', '', 0, 1, 4, '[13,14,32,15]'),
-- (0, 'Text', 'Physical Attributes', 'Enter your details below, the contents of this table are editable to suit your sport.\r\n\r\n<table style="background-color: #ffffff;" width="600px" border="5" cellspacing="3" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td><b>Performance </b></td>\r\n<td><b>Results      </b></td>\r\n<td><b>Test Type</b></td>\r\n</tr>\r\n<tr>\r\n<td><b>Height</b></td>\r\n<td> Enter your result here</td>\r\n<td>Enter your test type here</td>\r\n</tr>\r\n<tr>\r\n<td><b>Weight</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Aerobic Fitness</b><b></b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Anaerobic Capacity</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Endurance</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Speed</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Flexibility</b><b></b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Strength: Upper Body</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Strength: Leg</b><b></b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Strength</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Power: Upper Body</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Power: Leg</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Power:</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Body Fat: Skinfold</b><b></b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Body Fat: BMI</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Agility</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Hand Span</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Arm Length</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Skill Test 1</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Skill Test 2</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Skill Test 3</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Balance</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Specific 1</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td><b>Specific 2</b></td>\r\n<td></td>\r\n<td></td>\r\n</tr>\r\n</tbody>\r\n</table>', 0, 1, 5, '[13,14,32]'),
-- (0, 'Award', 'Honours & Awards', '', 0, 1, 13, '[13,14,32,17]'),
-- (0, 'Text', 'List of Referees', '', 0, 1, 17, '[13,14,32,15]'),
-- (0, 'Employment', 'Work History', '', 0, 1, 16, '[13,14,32,15]'),
-- (0, 'Education', 'Education', '', 0, 1, 15, '[13,14,32,15]'),
-- (0, 'Qualification', 'Coaching Qualifications', '', 0, 1, 9, '[15]'),
-- (0, 'Coachinghistory', 'Coaching History', '', 0, 1, 10, '[15]'),
-- (0, 'Text', 'About Us', '', 0, 1, 7, '[17,18]'),
-- (0, 'Text', 'Our Team', '', 0, 1, 8, '[17,18]'),
-- (0, 'Text', 'Offices', '', 0, 1, 18, '[17,18]'),
-- (0, 'Text', 'Sporting Highlights', '', 0, 1, 14, '[13,14,32]'),
-- (0, 'Text', 'Coaching Skills', '', 0, 1, 11, '[15]'),
-- (0, 'Text', 'Coaching Highlights', '', 0, 1, 12, '[15]'),
-- (0, 'Text', 'Company Objective', '', 0, 1, 2, '[18]'),
-- (0, 'Text', 'Testimonials', '', 0, 1, 19, '[18]');

UPDATE `engine4_core_content` SET `params` = '{"title":"CV Summary"}' WHERE `engine4_core_content`.`content_id` = 2051;

-- CV editing page
UPDATE `engine4_core_menuitems` SET `label` = 'View CV Summary' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_view';
UPDATE `engine4_core_menuitems` SET `label` = 'Edit CV Summary' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_edit';
UPDATE `engine4_core_menuitems` SET `label` = 'Edit & Manage CV Sections' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_sections';
UPDATE `engine4_core_menuitems` SET `label` = 'Edit CV Location' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_location';
UPDATE `engine4_core_menuitems` SET `label` = 'Upload photos to CV Photo Library' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_photo';
UPDATE `engine4_core_menuitems` SET `label` = 'Upload videos to CV Video Library' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_video';
UPDATE `engine4_core_menuitems` SET `label` = 'CV Payment history' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_epayments';
UPDATE `engine4_core_menuitems` SET `label` = 'Delete this CV' WHERE `engine4_core_menuitems`.`name` = 'resume_dashboard_delete';

-- CV viewing page
UPDATE `engine4_core_menuitems` SET `label` = 'All Submitter CVs' WHERE `engine4_core_menuitems`.`name` = 'resume_gutter_list';
UPDATE `engine4_core_menuitems` SET `label` = 'Post New CV' WHERE `engine4_core_menuitems`.`name` = 'resume_gutter_create';
UPDATE `engine4_core_menuitems` SET `label` = 'Edit This CV' WHERE `engine4_core_menuitems`.`name` = 'resume_gutter_edit';
UPDATE `engine4_core_menuitems` SET `label` = 'Delete This CV' WHERE `engine4_core_menuitems`.`name` = 'resume_gutter_delete';
UPDATE `engine4_core_menuitems` SET `label` = 'Publish This CV' WHERE `engine4_core_menuitems`.`name` = 'resume_gutter_publish';
UPDATE `engine4_core_menuitems` SET `label` = 'Print This CV' WHERE `engine4_core_menuitems`.`name` = 'resume_gutter_print';

-- Enable mobile friendly for this module
INSERT IGNORE INTO `engine4_sitemobile_modules` VALUE ('resume', 0, 1, 1, 1);