SET @sa_provinces_id = (SELECT `field_id` FROM `engine4_user_fields_meta` WHERE `label` = 'South African Rugby Union provinces');
SET @valke_option_id = (SELECT `option_id` FROM `engine4_user_fields_options` WHERE `label` = 'Valke' AND `field_id` = @sa_provinces_id);

-- Are you either coaching or playing at a school or club?

INSERT INTO `engine4_user_fields_meta` (`type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
('select', 'Are you either coaching or playing at a school or club?', '', '', 1, 0, 0, 0, 1, 999, '[]', NULL, NULL, '', '');

SET @club_or_school_id = (SELECT MAX(field_id) FROM `engine4_user_fields_meta`);

INSERT INTO `engine4_user_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(@sa_provinces_id, @valke_option_id, @club_or_school_id, 999);

INSERT INTO `engine4_user_fields_options` (`field_id`, `label`, `order`) VALUES
(@club_or_school_id, 'School', 1),
(@club_or_school_id, 'Club', 2),
(@club_or_school_id, 'Both', 3),
(@club_or_school_id, 'Neither', 4);

SET @school_option_id = (SELECT `option_id` FROM `engine4_user_fields_options` WHERE `label` = 'School' AND `field_id` = @club_or_school_id);
SET @club_option_id = (SELECT `option_id` FROM `engine4_user_fields_options` WHERE `label` = 'Club' AND `field_id` = @club_or_school_id);

-- List of schools

INSERT INTO `engine4_user_fields_meta` (`type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
('select', 'List of schools', '', '', 1, 0, 0, 0, 1, 999, '[]', NULL, NULL, '', '');

SET @list_of_school_id = (SELECT MAX(field_id) FROM `engine4_user_fields_meta`);

INSERT INTO `engine4_user_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(@club_or_school_id, @school_option_id, @list_of_school_id, 999);

-- List of clubs

INSERT INTO `engine4_user_fields_meta` (`type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
('select', 'List of clubs', '', '', 1, 0, 0, 0, 1, 999, '[]', NULL, NULL, '', '');

SET @list_of_club_id = (SELECT MAX(field_id) FROM `engine4_user_fields_meta`);

INSERT INTO `engine4_user_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(@club_or_school_id, @club_option_id, @list_of_club_id, 999);

--
-- Data for Valke clubs
--

INSERT INTO `engine4_user_fields_options` (`field_id`, `label`, `order`) VALUES
(@list_of_club_id, 'Benoni Rugby Club', 1),
(@list_of_club_id, 'Boksburg Rugby Club', 2),
(@list_of_club_id, 'Brakpan Rugby Club', 3),
(@list_of_club_id, 'Buffels Rugby Club', 4),
(@list_of_club_id, 'Delmas Rugby Club', 5),
(@list_of_club_id, 'East Rand United Rugby Club', 6),
(@list_of_club_id, 'Edenpark Rugby Club', 7),
(@list_of_club_id, 'Edenvale Panthers Rugby Club', 8),
(@list_of_club_id, 'Elsburg Rugby Club', 9),
(@list_of_club_id, 'Heidelberg Rugby Club', 10),
(@list_of_club_id, 'Kempton Wolwe Rugby Club', 11),
(@list_of_club_id, 'Meyerton Rugby Club', 12),
(@list_of_club_id, 'Nigel Rugby Club', 13),
(@list_of_club_id, 'NWU Vaal Pukke', 14),
(@list_of_club_id, 'Oos Rand Polisie Rugby Club', 15),
(@list_of_club_id, 'Sasolburg Rugby Club', 16),
(@list_of_club_id, 'Springs Rugby Club', 17),
(@list_of_club_id, 'Vaal Vikings Rugby Club', 18),
(@list_of_club_id, 'Vereeniging Rugby Club', 19),
(@list_of_club_id, 'VUT Rugby Club', 20);

--
-- Data for Valke schools
--

INSERT INTO `engine4_user_fields_options` (`field_id`, `label`, `order`) VALUES
(@list_of_school_id, 'Afrikaanse Hoërskool Sasolburg', 1),
(@list_of_school_id, 'Alrapark Geluksdal Youth Club', 2),
(@list_of_school_id, 'Belvedere High School', 3),
(@list_of_school_id, 'Benoni High School', 4),
(@list_of_school_id, 'Birchleigh High School', 5),
(@list_of_school_id, 'Brandwag High School', 6),
(@list_of_school_id, 'Curro Serengeti High School', 7),
(@list_of_school_id, 'Delmas High School', 8),
(@list_of_school_id, 'Die Anker Hoerskool', 9),
(@list_of_school_id, 'Die Anker''s Skool', 10),
(@list_of_school_id, 'Dr E G Jansen High School', 11),
(@list_of_school_id, 'DR Malan High School', 12),
(@list_of_school_id, 'Drie Riviere High School', 13),
(@list_of_school_id, 'Driehoek High School', 14),
(@list_of_school_id, 'Generaal Smuts High School', 15),
(@list_of_school_id, 'H T S Carel de Wet', 16),
(@list_of_school_id, 'H T S Elspark', 17),
(@list_of_school_id, 'H T S Rhodesfield', 18),
(@list_of_school_id, 'H T S Sasolburg ', 19),
(@list_of_school_id, 'H T S Springs', 20),
(@list_of_school_id, 'Hans Moore high School', 21),
(@list_of_school_id, 'Heidelberg Volkies High School', 22),
(@list_of_school_id, 'Heilbron High School', 23),
(@list_of_school_id, 'Hoerskool Balfour', 24),
(@list_of_school_id, 'Hoerskool Elsburg', 25),
(@list_of_school_id, 'Hoërskool Noorderlig', 26),
(@list_of_school_id, 'Hoërskool Primrose', 27),
(@list_of_school_id, 'Hugenote High School', 28),
(@list_of_school_id, 'Iketsetseng High School', 29),
(@list_of_school_id, 'Jeugland High School', 30),
(@list_of_school_id, 'Johan Jurgens High School', 31),
(@list_of_school_id, 'John Vorster High School', 32),
(@list_of_school_id, 'Kempton Panorama High School', 33),
(@list_of_school_id, 'Kempton Park High School', 34),
(@list_of_school_id, 'Oosterlig High School', 35),
(@list_of_school_id, 'Overvaal High School', 36),
(@list_of_school_id, 'Parys High School', 37),
(@list_of_school_id, 'Phoenix High School', 38),
(@list_of_school_id, 'Reiger Park High School', 39),
(@list_of_school_id, 'Riverside High School', 40),
(@list_of_school_id, 'Sasolburg Hoërskool', 41),
(@list_of_school_id, 'Sharpville High School', 42),
(@list_of_school_id, 'Sizanani High School', 43),
(@list_of_school_id, 'Springs Boys High School', 44),
(@list_of_school_id, 'Stoffberg High School', 45),
(@list_of_school_id, 'Suiderlig High School', 46),
(@list_of_school_id, 'Transvalia High School', 47),
(@list_of_school_id, 'Tsakane Youth Rugby Club', 48),
(@list_of_school_id, 'Vaalpark Articon High School', 49),
(@list_of_school_id, 'Vanderbijlpark High School', 50),
(@list_of_school_id, 'Vereeniging Gimnasium', 51),
(@list_of_school_id, 'Voortrekker High School', 52),
(@list_of_school_id, 'Zamdela High School', 53);
