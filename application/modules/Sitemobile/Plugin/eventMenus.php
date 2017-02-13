<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: eventMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_eventMenus {

  public function onMenuInitialize_EventProfileAddPhoto() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $album = $subject->getSingletonAlbum();

    // Must be able to view events
    if (!Engine_Api::_()->authorization()->isAllowed('event', $viewer, 'view')) {
      return false;
    }

    // Must be able to view events
    if (!$subject->authorization()->isAllowed(null, 'photo')) {
      return false;
    }

    return array(
        'label' => 'Upload Photos',
        'data-icon' => 'picture',
        'route' => 'event_extended',
        'params' => array(
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $subject->getGuid()
        )
    );
  }

  public function onMenuInitialize_EventTopicWatch() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $event = $subject->getParentEvent();
    $isWatching = null;

    $canPost = $event->authorization()->isAllowed($viewer, 'comment');

    if(!$canPost && !$viewer->getIdentity())
      return false;

		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
		$isWatching = $topicWatchesTable
						->select()
						->from($topicWatchesTable->info('name'), 'watch')
						->where('resource_id = ?', $event->getIdentity())
						->where('topic_id = ?', $subject->getIdentity())
						->where('user_id = ?', $viewer->getIdentity())
						->limit(1)
						->query()
						->fetchColumn(0)
		;

		if (false === $isWatching) {
			$isWatching = null;
		} else {
			$isWatching = (bool) $isWatching;
		}

    if(!$isWatching) {
      return array(
        'label' => 'Watch Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'event',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 1,
            'topic_id' => $subject->getIdentity()
        )
			);
    } else {
			return array(
        'label' => 'Stop Watching Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'event',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 0
        )
			);
    }

	}

  public function onMenuInitialize_EventTopicRename() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $event = $subject->getParentEvent();
    $canEdit = $event->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

		return array(
			'label' => 'Rename',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-action',
			'params' => array(
					'module' => 'event',
					'controller' => 'topic',
					'action' => 'rename',
					'topic_id' => $subject->getIdentity()
			)
		);

	}

  public function onMenuInitialize_EventTopicDelete() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $event = $subject->getParentEvent();
    $canEdit = $event->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

		return array(
			'label' => 'Delete Topic',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-danger',
			'params' => array(
					'module' => 'event',
					'controller' => 'topic',
					'action' => 'delete',
					'topic_id' => $subject->getIdentity()
			)
		);

	}

  public function onMenuInitialize_EventTopicOpen() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $event = $subject->getParentEvent();
    $canEdit = $event->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

    if(!$subject->closed) {
			return array(
				'label' => 'Close',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'event',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 1
				)
			);
    } else {
			return array(
				'label' => 'Open',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'event',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 0
				)
			);
    }

	}

  public function onMenuInitialize_EventTopicSticky() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $event = $subject->getParentEvent();
    $canEdit = $event->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

    if(!$subject->sticky) {
			return array(
				'label' => 'Make Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'event',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 1
				)
			);
    } else {
			return array(
				'label' => 'Remove Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'event',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 0
				)
			);
    }

	}

  //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_EventPhotoEdit($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(null, 'edit') && ($this->subject->user_id != $this->viewer()->user_id)) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'event_extended',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'edit',
           'photo_id' => $subject->photo_id
        )
    );
  }

 //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_EventPhotoDelete($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(null, 'edit') && ($this->subject->user_id != $this->viewer()->user_id)) {
      return false;
    }

    return array(
        'label' => 'Delete',
        'route' => 'event_extended',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'delete',
           'photo_id' => $subject->photo_id
        )
    );
  }

  public function onMenuInitialize_EventPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_EventPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

  public function onMenuInitialize_EventPhotoMakeProfilePhoto($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }

    return array(
        'label' => 'Make Profile Photo',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'route' => 'user_extended',
        'params' => array(
            'module' => 'user',
            'controller' => 'edit',
            'action' => 'external-photo',
            'photo' => $subject->getGuid(),
        )
    );
  }

}  