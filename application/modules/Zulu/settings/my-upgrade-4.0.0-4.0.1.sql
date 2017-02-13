ALTER TABLE `engine4_users`
ADD COLUMN `parent_id` int(11) NOT NULL DEFAULT 0,
ADD INDEX PARENT_ID (`parent_id`)
;