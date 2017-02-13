UPDATE `engine4_core_menuitems` SET `order` = 2 WHERE `name` = 'sitepage_main_home';
UPDATE `engine4_core_menuitems` SET `order` = 1 WHERE `name` = 'sitepage_main_manage';
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_general","action":"manage"}' WHERE `name` = 'core_main_sitepage';