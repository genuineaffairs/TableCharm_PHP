UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepagepoll_browse","action":"browse"}' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_poll' LIMIT 1 ;

UPDATE `engine4_core_pages` SET `name` = 'sitepagepoll_index_browse' WHERE `engine4_core_pages`.`name` ='sitepagepoll_index_polllist' LIMIT 1 ;