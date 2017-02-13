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
class Sitepagepoll_Plugin_Menus {

  public function canViewPolls() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.poll.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('polls', 'sitepagepoll');
    $rName = $table->info('name');
    $table_pages = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName_pages = $table_pages->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_pages, array('photo_id', 'title as sitepage_title'))
                    ->join($rName, $rName . '.page_id = ' . $rName_pages . '.page_id')
                    ->where($rName . '.search = ?', '1');

    $select = $select
                    ->where($rName_pages . '.closed = ?', '0')
                    ->where($rName_pages . '.approved = ?', '1')
                    ->where($rName_pages . '.search = ?', '1')
                    ->where($rName_pages . '.declined = ?', '0')
                    ->where($rName_pages . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($rName_pages . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }

  //SITEMOBILE PAGE POLL MENUS
  public function onMenuInitialize_SitepagepollShare($row) {
    $sitepagepoll = Engine_Api::_()->core()->getSubject('sitepagepoll_poll');
    if (empty($sitepagepoll)) {
      return false;
    }

    return array(
        'label' => 'Share',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => 'sitepagepoll_poll',
            'id' => $sitepagepoll->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitepagepollReport($row) {
    $sitepagepoll = Engine_Api::_()->core()->getSubject('sitepagepoll_poll');
    if (empty($sitepagepoll)) {
      return false;
    }

    return array(
        'label' => 'Report',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $sitepagepoll->getGuid(),
        )
    );
  }

  public function onMenuInitialize_SitepagepollCreate($row) {
    $sitepagepoll = Engine_Api::_()->core()->getSubject('sitepagepoll_poll');
    if (empty($sitepagepoll)) {
      return false;
    }

    $page_id = $sitepagepoll->page_id;
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }

    if (empty($can_create)) {
      return false;
    }
    return array(
        'label' => 'Create Poll',
        'route' => 'sitepagepoll_create',
        'class' => 'ui-btn-action',
        'params' => array(
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagepollDelete($row) {
    $sitepagepoll = Engine_Api::_()->core()->getSubject('sitepagepoll_poll');
    if (empty($sitepagepoll)) {
      return false;
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $owner_id = $sitepagepoll->owner_id;
    $page_id = $sitepagepoll->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if ($owner_id != $viewer_id && empty($can_edit)) {
      return false;
    }

    return array(
        'label' => 'Delete Poll',
        'route' => 'sitepagepoll_delete',
        'class' => 'ui-btn-danger',
        'params' => array(
            'poll_id' => $sitepagepoll->poll_id,
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

}

?>