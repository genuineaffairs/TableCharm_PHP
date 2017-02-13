-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('document', 'Documents', 'Documents', '4.6.0', 1, 'extra');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('document_main_home', 'document', 'Documents Home', 'Document_Plugin_Menus::canViewDocuments', '{"route":"document_home"}', 'document_main', '', 1, 0, 1),
('document_main_browse', 'document', 'Browse Documents', 'Document_Plugin_Menus::canViewDocuments', '{"route":"document_browse"}', 'document_main', '', 1, 0, 2),
('document_main_manage', 'document', 'My Documents', 'Document_Plugin_Menus::canCreateDocuments', '{"route":"document_manage"}', 'document_main', '', 1, 0, 3),
('document_main_create', 'document', 'Create New Document', 'Document_Plugin_Menus::canCreateDocuments', '{"route":"document_create"}', 'document_main', '', 1, 0, 4);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('document_new', 'document', '{item:$subject} created a new document:', 1, 5, 1, 3, 1, 1), 
('comment_document', 'document', '{item:$subject} commented on {item:$owner}''s {item:$object:document}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "document" as `type`,
    "profile_doc_show" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");

-- --------------------------------------------------------