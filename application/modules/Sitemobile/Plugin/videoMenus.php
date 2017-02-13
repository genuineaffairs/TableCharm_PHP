<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: videoMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_videoMenus {

  public function onMenuInitialize_VideoProfileEdit() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->getType() !== 'video') {
      throw new Video_Model_Exception('Whoops, not a video!');
    }

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => 'Edit Video',
        'icon' => 'application/modules/Video/externals/images/edit.png',
        'route' => 'default',
        'params' => array(
            'controller' => 'index',
            'action' => 'edit',
            'video_id' => $subject->getIdentity(),
            'module' => 'video',
        )
    );
  }

  public function onMenuInitialize_VideoProfileDelete() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'video') {
      throw new Video_Model_Exception('Whoops, not a video!');
    }

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'delete')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'delete')) {
      return false;
    }

    return array(
        'label' => 'Delete Video',
        'icon' => 'application/modules/Video/externals/images/delete.png',
        'route' => 'default',
        'params' => array(
            'controller' => 'index',
            'action' => 'delete',
            'video_id' => $subject->getIdentity(),
            'module' => 'video',
        )
    );
  }

}