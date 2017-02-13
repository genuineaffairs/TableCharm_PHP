<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagelikebox_Api_Core extends Core_Api_Abstract {

	  /**
   * Check for package is enable or not and page admin check.
   *
   * @param object $sitepage
   * @param string $module
   * @param string $hasPackageEnable
   * @return bool $canDo
   */
  public function allowModule( $sitepage , $module , $hasPackageEnable ) {

    // CHECK FOR PACKAGE IS ENABLE OR NOT.
    if ( $hasPackageEnable ) {
      return (bool) Engine_Api::_()->sitepage()->allowPackageContent( $sitepage->package_id , 'modules' , $module ) ;
    }
    else {
      $levelModules = array ( "offer" => "sitepageoffer" , "form" => "sitepageform" , "invite" => "sitepageinvite" , "sdcreate" => "sitepagedocument" , "sncreate" => "sitepagenote" , "splcreate" => "sitepagepoll" , "secreate" => "sitepageevent" , "svcreate" => "sitepagevideo" , "spcreate" => "sitepagealbum" , "comment" => "sitepagediscussion" , "smcreate" => "sitepagemusic") ;
      $search_Key = array_search( $module , $levelModules ) ;
      return (bool) Engine_Api::_()->sitepage()->isPageOwnerAllow( $sitepage , $search_Key ) ;
    }
  }

	  /**
   * Get the widget name and find the order of profile widget show on the page profile page.
   *
   * @return Array $widgetSettingsArray
   */
  public function getWidgteParams() {

		//GET THE PAGE LAYOUT.
    $layout = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitepage.layoutcreate' ) ;

		//GET THE PAGE LAYOUT TYPE.
    $layoutType = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitepage.layout.setting' ) ;

    $widgetArray = array ( 'activity.feed'  , 'advancedactivity.home-feeds', 'seaocore.feed' , 'sitepage.info-sitepage' , 'sitepage.location-sitepage' , 'sitepage.discussion-sitepage' , 'sitepage.photos-sitepage' , 'sitepageevent.profile-sitepageevents' , 'sitepagepoll.profile-sitepagepolls' , 'sitepagenote.profile-sitepagenotes' , 'sitepageoffer.profile-sitepageoffers' , 'sitepagevideo.profile-sitepagevideos' , 'sitepagemusic.profile-sitepagemusic' , 'sitepagedocument.profile-sitepagedocuments' , 'sitepagereview.profile-sitepagereviews' ) ;

    $widgetSettingsArray = array () ;
    if ( empty( $layout ) ) {
      if ( !empty( $layoutType ) ) {
        $pagesTable = Engine_Api::_()->getDbtable( 'pages' , 'core' ) ;
        $page_id = $pagesTable->select()
                ->from( $pagesTable->info( 'name' ) , array ( 'page_id' ) )
                ->where( "name = ?" , 'sitepage_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $page_id ) ) {
          $contentTable = Engine_Api::_()->getDbtable( 'content' , 'core' ) ;
          $allWidget = $contentTable->select()
                  ->from( $contentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "page_id = ?" , $page_id ) ;
          $contentTable = $allWidget->query()->fetchAll() ;

          foreach ( $contentTable as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
      else {
        $pagesTable = Engine_Api::_()->getDbtable( 'pages' , 'core' ) ;
        $page_id = $pagesTable->select()
                ->from( $pagesTable->info( 'name' ) , array ( 'page_id' ) )
                ->where( "name = ?" , 'sitepage_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $page_id ) ) {
          $contentTable = Engine_Api::_()->getDbtable( 'content' , 'core' ) ;
          $allWidget = $contentTable->select()
                  ->from( $contentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "page_id = ?" , $page_id ) ;
          $allWidget = $allWidget->query()->fetchAll() ;

          foreach ( $allWidget as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
    }
    else {
      if ( !empty( $layoutType ) ) {
        $contentpagesTable = Engine_Api::_()->getDbtable( 'contentpages' , 'sitepage' ) ;
        $contentpage_id = $contentpagesTable->select()
                ->from( $contentpagesTable->info( 'name' ) , array ( 'page_id' ) )
                ->where( "name = ?" , 'sitepage_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $contentpage_id ) ) {
          $sitepagecontentTable = Engine_Api::_()->getDbtable( 'content' , 'sitepage' ) ;
          $allWidget = $sitepagecontentTable->select()
                  ->from( $sitepagecontentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "contentpage_id = ?" , $contentpage_id ) ;
          $contentTable = $allWidget->query()->fetchAll() ;

          foreach ( $contentTable as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
      else {
        $contentpagesTable = Engine_Api::_()->getDbtable( 'contentpages' , 'sitepage' ) ;
        $page_id = $contentpagesTable->select()
                ->from( $contentpagesTable->info( 'name' ) , array ( 'page_id' ) )
                ->where( "name = ?" , 'sitepage_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $page_id ) ) {
          $sitepagecontentTable = Engine_Api::_()->getDbtable( 'content' , 'sitepage' ) ;
          $allWidget = $sitepagecontentTable->select()
                  ->from( $sitepagecontentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "contentpage_id = ?" , $page_id ) ;
          $allWidget = $allWidget->query()->fetchAll() ;

          foreach ( $allWidget as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
    }
	}
}
?>