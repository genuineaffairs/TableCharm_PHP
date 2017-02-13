INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('document_main', 'standard', 'Document Main Navigation Menu'),
('document_gutter', 'standard', 'Document View Page Options Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('mobi_home_document', 'document', 'Document', '', '{"route":"document_home"}', 'mobi_home', '', 1, 0, 998),

('mobi_browse_document', 'document', 'Document', '', '{"route":"document_home"}', 'mobi_browse', '', 1, 0, 999),

('document_admin_main_profilemaps', 'document', 'Category-Doc Profile Mapping', '', '{"route":"admin_default","module":"document","controller":"profilemaps","action":"manage"}', 'document_admin_main', '', 1, 0, 5),

('document_admin_main_form_search', 'document', 'Search Form Settings', '', '{"route":"admin_default","module":"document","controller":"settings","action":"form-search"}', 'document_admin_main', '', 1, 0, 6),

('document_admin_main_dayitem', 'document', 'Document of the Day', '', '{"route":"admin_default","module":"document","controller":"items","action":"day"}', 'document_admin_main', '', 1, 0, 8),

('document_main_home', 'document', 'Documents Home', 'Document_Plugin_Menus::canViewDocuments', '{"route":"document_home"}', 'document_main', '', 1, 0, 1),

('document_main_browse', 'document', 'Browse Documents', 'Document_Plugin_Menus::canViewDocuments', '{"route":"document_browse"}', 'document_main', '', 1, 0, 2),

('document_main_manage', 'document', 'My Documents', 'Document_Plugin_Menus::canCreateDocuments', '{"route":"document_manage"}', 'document_main', '', 1, 0, 3),

('document_main_create', 'document', 'Create New Document', 'Document_Plugin_Menus::canCreateDocuments', '{"route":"document_create"}', 'document_main', '', 1, 0, 4),

('document_gutter_list', 'document', 'Owner’s Documents', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 0),

('document_gutter_publish', 'document', 'Publish Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 1),

('document_gutter_edit', 'document', 'Edit Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 2),

('document_gutter_share', 'document', 'Share Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 3),

('document_gutter_download', 'document', 'Download Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 4),

('document_gutter_email', 'document', 'Email Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 5),

('document_gutter_report', 'document', 'Report Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 7),

('document_gutter_delete', 'document', 'Delete Document', 'Document_Plugin_Menus', '', 'document_gutter', '', 1, 0, 8);

DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'document_admin_main_widgets' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1;

UPDATE `engine4_core_menuitems` SET `plugin` = 'Document_Plugin_Menus::canViewDocuments', `params` = '{"route":"document_home"}' WHERE `engine4_core_menuitems`.`name` = 'core_main_document' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"document","controller":"settings","action":"level"}', `order` = 2  WHERE `engine4_core_menuitems`.`name` = 'document_admin_main_level' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = 3  WHERE `engine4_core_menuitems`.`name` = 'document_admin_main_categories' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `label` = 'Profile Fields', `order` = 4  WHERE `engine4_core_menuitems`.`name` = 'document_admin_main_fields' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `label` = 'Manage Documents', `order` = 7  WHERE `engine4_core_menuitems`.`name` = 'document_admin_main_manage' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = 9  WHERE `engine4_core_menuitems`.`name` = 'document_admin_main_faq' AND `engine4_core_menuitems`.`module` = 'document' LIMIT 1 ;

UPDATE `engine4_core_pages` SET `custom` = '0', `displayname` = 'Documents Browse Page', `title` = 'Documents Browse Page' WHERE `engine4_core_pages`.`name` = 'document_index_browse' LIMIT 1 ;

UPDATE `engine4_authorization_permissions`
SET params = replace(params, 'everyone', 'registered')
WHERE type = 'document' AND name = 'auth_comment';

UPDATE `engine4_authorization_allow`
SET `role` = replace(`role`, 'everyone', 'registered')
WHERE resource_type = 'document' AND action = 'comment';

UPDATE `engine4_authorization_permissions`
SET params = replace(params, '["', '["registered","')
WHERE type = 'document' AND name = 'auth_view';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "document" as `type`,
    "sponsored" as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "document" as `type`,
    "profile_doc" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "document" as `type`,
    "profile_doc_show" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "document" as `type`,
    "view_download" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "document" as `type`,
    "view_email" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");

INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
("document", "search", 1, 1, "Search Documents"),
("document", "orderby", 1, 2, "Browse By"),
("document", "draft", 1, 3, "Show (For ‘My Documents’ page)"),
("document", "show", 1, 4, "Show (For ‘Documents Home’ and ‘Browse Documents’ pages)"),
("document", "category_id", 1, 5, "Category");

DROP TABLE IF EXISTS `engine4_document_itemofthedays`;
CREATE TABLE IF NOT EXISTS `engine4_document_itemofthedays` (
  `itemoftheday_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `document_id` int(11) NOT NULL,
  PRIMARY KEY (`itemoftheday_id`),
  KEY `end_date` (`end_date`),
  KEY `start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;