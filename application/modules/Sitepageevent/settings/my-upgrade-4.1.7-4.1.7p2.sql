UPDATE `engine4_core_content` SET `engine4_core_content`.`name` = 'seaocore.feed' WHERE `engine4_core_content`.`name` = 'activity.feed' and 
`engine4_core_content`.`page_id`=(SELECT  `engine4_core_pages`.`page_id`
FROM `engine4_core_pages`
WHERE  (`engine4_core_pages`.`name` = 'sitepageevent_index_view'   and   `engine4_core_pages`.`page_id`= `engine4_core_content`.`page_id`));
