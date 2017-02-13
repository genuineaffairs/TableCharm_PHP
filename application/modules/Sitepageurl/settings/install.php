<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageurl_Installer extends Engine_Package_Installer_Module {

  function onInstall() {

    //GET DB
    $db = $this->getDb();
    //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
    $sitepage_settings = $select->query()->fetchAll();
    if (!empty($sitepage_settings)) {
      $sitepage_is_active = $sitepage_settings[0]['value'];
    } else {
      $sitepage_is_active = 0;
    }
    
    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version >= ?', '4.2.1')
            ->where('enabled = ?', 1);
    $check_sitepage_version = $select->query()->fetchObject();
    if(!empty($check_sitepage_version)) {
      $check_sitepage_version = 1;

    }
    else {
      $check_sitepage_version = 0;
    }

    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage) && !empty($sitepage_is_active) && !empty($check_sitepage_version)) {

      parent::onInstall();
      
			$seocoreBannedUrlTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
			if (empty($seocoreBannedUrlTable)) {
				$db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_bannedpageurls` (
								`bannedpageurl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
								PRIMARY KEY (`bannedpageurl_id`),
								UNIQUE KEY `word` (`word`)
							) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
      }
      
      $table_url_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_bannedpageurls\'')->fetch();
      if (!empty($table_url_exist)) {
        $db->query("RENAME TABLE `engine4_sitepage_bannedpageurls` TO `engine4_seaocore_bannedpageurls` ");
      }
      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
			if (empty($table_exist)) {
				$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES
										('sitestaticpage'),('static'),('music'),('polls'),('blogs'),('videos'),	('classifieds'),('albums'),('events'),	('groups'),('group'),
										('forums'),('invite'),('recipeitems'),('ads'),	('likes'),('documents'),('sitepage'),
										('sitepagepoll'),('sitepageoffer'),('sitepagevideo'),('sitepagedocument'),('sitepagenote'),
										('sitepageevent'),('sitepagemusic'),('sitepageinvite'),('sitepagereview'),('sitepagebadge'),
									  ('sitepageform'),('sitepagealbum'),('sitepagediscussion'),('sitebusiness'),
										('sitebusinesspoll'),('sitebusinessoffer'),('sitebusinessvideo'),('sitebusinessdocument'),('sitebusinessnote'),
										('sitebusinessevent'),('sitebusinessmusic'),('sitebusinessinvite'),('sitebusinessreview'),('sitebusinessbadge'),
									  ('sitebusinessform'),('sitebusinessalbum'),('sitebusinessdiscussion'),('sitegroup'),
										('sitegrouppoll'),('sitegroupoffer'),('sitegroupvideo'),('sitegroupdocument'),('sitegroupnote'),
										('sitegroupevent'),('sitegroupmusic'),('sitegroupinvite'),('sitegroupreview'),('sitegroupbadge'),
									  ('sitegroupform'),('sitegroupalbum'),('sitegroupdiscussion'),('sitestore'),
										('sitestorepoll'),('sitestoreoffer'),('sitestorevideo'),('sitestoredocument'),('sitestorenote'),
										('sitestoreevent'),('sitestoremusic'),('sitestoreinvite'),('sitestorereview'),('sitestorebadge'),
									  ('sitestoreform'),('sitestorealbum'),('sitestorediscussion'),('recipe'),('sitelike'),('suggestion'),('advanceslideshow'),('feedback'),('grouppoll'),('groupdocumnet'),('sitealbum'),('siteslideshow'),('userconnection'),('communityad'),('list'),('article'),
										('listing'),('store'),('page-videos'),('pageitem'),('pageitems'),('page-events'),('page-documents'),('page-offers'),('page-notes'),('page-invites'),('page-form'),('page-music'),
										('page-reviews'),('businessitem'),('businessitems'),('business-events'),('business-documents'),('business-offers'),('business-notes'),('business-invites'),('business-form'),('business-music'),
										('business-reviews'),('group-videos'),('groupitem'),('groupitems'),('group-events'),('group-documents'),('group-offers'),('group-notes'),('group-invites'),('group-form'),('group-music'),('group-reviews'),('store-videos'),('storeitem'),('storeitems'),('store-events'),
									  ('store-documents'),('store-offers'),('store-notes'),('store-invites'),('store-form'),('store-music'),('store-reviews'),('listingitems'),('market'),('document'),('pdf'),('pokes'),('facebook'),('album'),('photo'),('files'),('file'),('page'),
									  ('store'),('backup'),('question'),('answer'),('questions'),('answers'),('newsfeed'),('birthday'),('wall'),('profiletype'),('memberlevel'),('members'),('member'),('memberlevel'),
					          ('level'),('slideshow'),('seo'),('xml'),('cmspages'),('favoritepages'),('help'),('rss'),
										('stories'),('story'),('visits'),('points'),('vote'),('advanced'),('listingitem');");
			}
      
 
      //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_modules')
							->where('name = ?', 'sitepageurl')
							->where('enabled = ?', 1);
			$check_sitepageurl = $select->query()->fetchObject();
      if(empty($check_sitepageurl)) {
				$includeModules = array("sitepage" => "sitepage","sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music","sitegroup" => "sitegroup","sitegroupdocument" => 'Documents', "sitegroupoffer" => 'Offers', "sitegroupform" => "Form", "sitegroupdiscussion" => "Discussions", "sitegroupnote" => "Notes", "sitegroupalbum" => "Photos", "sitegroupvideo" => "Videos", "sitegroupevent" => "Events", "sitegrouppoll" => "Polls", "sitegroupinvite" => "Invite & Promote", "sitegroupbadge" => "Badges", "sitegrouplikebox" => "External Badge", "sitegroupmusic" => "Music","sitestore" => "sitestore","sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "
sitestoreform" => "Form", "sitestorediscussion" => "Discussions","sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music","sitebusiness" => "sitebusiness","sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form","sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music","list"=>"list");
				$select = new Zend_Db_Select($db);
				$select
								->from('engine4_core_modules','name')
								->where('enabled = ?', 1);
				$enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
				$enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
		
				foreach ($enableAllModules as $moduleName) {
					if(!in_array($moduleName,$enableModules)) {
						$file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
						$contentItem = array();
						if (@file_exists($file_path)) {
							$ret = include $file_path;
							$is_exist = array();
							if (isset($ret['routes'])) {
								foreach ($ret['routes'] as $item) {
									$route = $item['route'];
									$route_array =  explode('/',$route);
									$route_url = strtolower($route_array[0]);
									
									if(!empty($route_url) && !in_array($route_url,$is_exist)) {
										$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES ('".$route_url. "')");
									}
									$is_exist[] = $route_url;
								}
							}
						} 
					}
					else {
						if($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore') {
							$name = $moduleName .'.manifestUrlS';
						}
						else {
							$name = $moduleName .'.manifestUrl';
						}
						$select = new Zend_Db_Select($db);
						$select
								->from('engine4_core_settings','value')
								->where('name = ?', $name)
								->limit(1);
						$route_url = strtolower($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));
						if(!empty($route_url)) {
							$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
						}
					}
				}
      }
    } 
   if(!empty($check_sitepage) && !empty($sitepage_is_active) && empty($check_sitepage_version)) {

      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: The version of the Directory / Pages Plugin on your website is less than the minimum required version: 4.1.8p3. Please download the latest version of this  plugin from your Client Area on SocialEngineAddOns and upgrade it on your website.
</span>");

    }
    elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      if (strstr($url_string, "manage/install")) {
        $calling_from = 'install';
      } else if (strstr($url_string, "manage/query")) {
        $calling_from = 'queary';
      }
      $explode_base_url = explode("/", $baseUrl);
      foreach ($explode_base_url as $url_key) {
        if ($url_key != 'install') {
          $core_final_url .= $url_key . '/';
        }
      }

      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Short Page URL Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    }
    elseif (empty($check_sitepage)) {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-short-page-url-extension' target='_blank'>Directory / Pages - Short Page URL Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
    
    //BANNED URL WORK IF SITEREVIEW PLUGIN IS ALREADY EXIST
    $this->bannedUrlWork();
  }
  
  //RUN THIS CODE ON FIRST TIME INSTALLATION OF PAGE URL PLUGIN
  function bannedUrlWork() {
    
    //GET DB
    $db = $this->getDb();

    $bannedpageurlsTableExist = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
    $listingtypeTableExist = $db->query('SHOW TABLES LIKE \'engine4_sitereview_listingtypes\'')->fetch();
    $select = new Zend_Db_Select($db);
    $isActivate = $select->from('engine4_core_settings', 'name')
                                    ->where('name = ?', 'sitepageurl.is.enable')
                                    ->where('value = ?', 1)
                                    ->query()
                                    ->fetchColumn();

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules', 'name')
            ->where('name = ?', 'sitepageurl')
            ->query()
            ->fetchcolumn();
    $isSitepageurlenabled = $select->query()->fetchObject();                                    

    if (!empty($bannedpageurlsTableExist) && !empty($listingtypeTableExist) && !empty($isActivate) && !empty($isSitepageurlenabled)) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_sitereview_listingtypes', array('slug_singular', 'slug_plural'));
      $listingTypeDatas = $select->query()->fetchAll();
      foreach($listingTypeDatas as $listingTypeData) {
        
				$urls = array("$listingTypeData->slug_plural","$listingTypeData->slug_singular");
				
				foreach($urls as $url) {

					$bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $url);
				
					$words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

          $select = new Zend_Db_Select($db);
					$data = $select->from('engine4_seaocore_bannedpageurls', 'word')
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
																			
					if(in_array($words[0],$data)) {
						return;
					}
          
          $words = array_map('strtolower', array_filter(array_values($words)));

          $select = new Zend_Db_Select($db);
          $data = $select
              ->from('engine4_seaocore_bannedpageurls', 'word')
              ->query()
              ->fetchAll(Zend_Db::FETCH_COLUMN);

          $newWords = array_diff($words, $data);
          foreach( $newWords as $newWord ) {
            $db->insert('engine4_seaocore_bannedpageurls', array(
              'word' => $newWord,
            ));
          }
				}        
      }
    }    
  }
    
  public function onPostInstall() {
  
		//Work for the word changes in the page plugin .csv file.
		$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		if($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
			$view = new Zend_View();
			$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			if ($actionName == 'install') {
				$redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/install');
			} else {
				$redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/query');
			}
		}
  }
}

?>
