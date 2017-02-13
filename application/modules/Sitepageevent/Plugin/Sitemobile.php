<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;

  public function onIntegrated() {

    $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //page events
    $this->addSitepageEventProfileContent();
    $this->addSitepageEventPhotoViewPage();
    $this->addSitepageEventBrowsePage();
    $this->addSitepageEventCreatePage();
    $this->addSitepageEventViewPage();
    include APPLICATION_PATH . "/application/modules/Sitepageevent/controllers/license/mobileLayoutCreation.php";
  }

  //page Event
  public function addSitepageEventProfileContent() {

    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_view');


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepageevent.sitemobile-profile-sitepageevents')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.sitemobile-profile-sitepageevents',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 1500,
          'params' => '{"title":"Events","titleCount":true}',
      ));
    }
  }

  public function addSitepageEventViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepageevent_index_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepageevent_index_view',
          'displayname' => 'Directory / Pages - Event View Page',
          'title' => 'Event View Page',
          'description' => 'This is event view page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      // Insert breadcrumb
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepageevent.sitemobile-breadcrumb',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-photo-status',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.sitemobile-profile-info',
          'parent_content_id' => $middle_id,
          'order' => 4,
          'params' => '',
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.profile-rsvp',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 7,
          'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 8,
          'params' => '{"title":"Updates"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.profile-members',
          'parent_content_id' => $tab_id,
          'order' => 10,
          'params' => '{"title":"Guests","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.profile-photos',
          'parent_content_id' => $tab_id,
          'order' => 11,
          'params' => '{"title":"Photos","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.sitemobile-profile-discussions',
          'parent_content_id' => $tab_id,
          'order' => 12,
          'params' => '{"title":"Discussions","titleCount":true}',
      ));
    }
  }

  public function addSitepageEventPhotoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepageevent_photo_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepageevent_photo_view',
          'displayname' => 'Directory / Pages - Event Photo View Page',
          'title' => 'Event Photo View Page',
          'description' => 'This is event photo view page.',
          'provides' => 'subject=sitepageevent_photo',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }

    return $this;
  }

  public function addSitepageEventBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepageevent_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepageevent_index_browse',
          'displayname' => 'Directory / Pages - Browse Events',
          'title' => 'Browse Events',
          'description' => 'This is browse events page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepageevent.sitepage-event',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

  public function addSitepageEventCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepageevent_index_create');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepageevent_index_create',
          'displayname' => 'Directory / Pages - Create Event',
          'title' => 'Create new Event',
          'description' => 'This is event create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

}