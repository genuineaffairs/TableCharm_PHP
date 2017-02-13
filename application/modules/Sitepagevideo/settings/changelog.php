<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: changelog.php 9140 2011-08-03 23:47:47Z john $
 * @author     John
 */
return array(

  '4.1.8p3' => array (
		'views/scripts/_composeSitepageVideo.tpl' => 'Fixed Add Video issue in updates tab of Page Profile',
		'Plugin/Task/Encode.php' =>	'Fixed Notification issue during video conversion',
		'settings/my-upgrade-4.1.8p2-4.1.8p3.sql' => 'Added',
	),

  '4.1.7p2' => array(
		'Form/Admin/Widget.php' => 'Added new widgets Recent Video and Most Viewed Video',
		'Model/DbTable/Videos.php' => 'Added new widgets Recent Video and Most Viewed Video',
		'settings/content.php' => 'Added new widgets Recent Video and Most Viewed Video',
		'settings/content_user.php' => 'Added new widgets Recent Video and Most Viewed Video',
		'widgets/homerecent-sitepagevideos/Controller.php' => 'Added new widgets Recent Video and Most Viewed Video',
		'widgets/homerecent-sitepagevideos/index.tpl' => 'Added new widgets Recent Video and Most Viewed Video',
		'widgets/view-sitepagevideos/Controller.php' => 'Added new widgets Recent Video and Most Viewed Video',
		'widgets/view-sitepagevideos/index.tpl' => 'Added new widgets Recent Video and Most Viewed Video',
		'controllers/IndexController.php' => 'Added dashboard link on create Video page of Page Video,Minor modification for Suggestions / Recommendations Plugin,by using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'views/scripts/index/create.tpl' => 'Added dashboard link on create Video page of Page Video',
		'settings/install.php' => 'Minor modification for Suggestions / Recommendations Plugin',
		'Plugin/Composer.php' => ' By using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'externals/scripts/composer_video.js,By using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'externals/styles/main.css' => 'By using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'settings/manifest.php' => ' By using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'views/scripts/_composeSitepageVideo.tpl' => 'Minor modifications,by using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'views/scripts/_composeVideo.tpl' => 'By using the Link "Add Video" in updates tab of Activity feed, now you can add videos into the Page Video Extensions.',
		'externals/scripts/composer_video.js' => 'Minor modifications',
		'widgets/profile-sitepagevideos/index.tpl' => ' Modification for Activity Feed on Page Video Profile',
  ),

  '4.1.7' => array(
		'controllers/IndexController.php' => 'Fixed Activity Feed issue',
		'Model/Video.php - Fixed Issue',
		'widgets/profile-sitepagevideos/index.tpl' => 'Minor optimizations',
		'Api/Core.php - Fixed Issue',
		'Model/DbTable/Videos.php - Fixed Activity Feed issue',
		'controllers/AdminManageController.php' => 'Minor optimizations',
		'controllers/IndexController.php - Fixed Issue',
		'widgets/profile-sitepagevideos/Controller.php - Fixed Issue',
		'Form/Admin/Global.php' => 'Minor optimizations',
		'widgets/profile-sitepagevideos/index.tpl - Fixed Issue',
		'controllers/AdminSettingsController.php - Fixed Issue',
		'settings/my-upgrade-4.1.6p3-4.1.7.sql - Fixed Issue',
		'views/scripts/index/view.tpl - Fixed Issue',
  ),
)
?>
