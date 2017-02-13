
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2010-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepagelikebox', 'Directory / Pages - Embeddable Badges', ' Directory / Pages - Embeddable Badges, Like Box Extension', '4.7.0', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('modules.likebox','a:10:{i:0;s:13:"sitepagealbum";i:1;s:12:"sitepagepoll";i:2;s:16:"sitepagedocument";i:3;s:13:"sitepageoffer";i:4;s:13:"sitepagevideo";i:5;s:13:"sitepageevent";i:6;s:12:"sitepagenote";i:7;s:18:"sitepagediscussion";i:8;s:13:"sitepagemusic";i:9;s:6:"review";}');

