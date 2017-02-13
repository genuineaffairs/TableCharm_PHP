<?php
class Advgroup_Plugin_Core
{
  public function onStatistics($event)
  {
    $table = Engine_Api::_()->getItemTable('group');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'group');
  }
  
  public function onUserDeleteBefore($group)
  {
    $payload = $group->getPayload();
    if( $payload instanceof User_Model_User ) {
    	
      // Delete memberships
      $membershipApi = Engine_Api::_()->getDbtable('membership', 'advgroup');
      foreach( $membershipApi->getMembershipsOf($payload) as $group ) {
        $membershipApi->removeMember($group, $payload);
      }
	  
	  // Delete officers
      $listItemTable = Engine_Api::_()->getDbtable('ListItems', 'advgroup');
      $listItemSelect = $listItemTable->select()->where('child_id = ?', $payload->getIdentity());
      foreach( $listItemTable->fetchAll($listItemSelect) as $listitem ) {
        $list = Engine_Api::_()->getItem('advgroup_list', $listitem->list_id);
        if( !$list ) {
          $listitem->delete();
          continue;
        }
        if( $list->has($payload) ) {
          $list->remove($payload);
        }
      }	
      // Delete albums
      $albumTable = Engine_Api::_()->getItemTable('advgroup_album');
      $albumSelect = $albumTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $albumTable->fetchAll($albumSelect) as $groupAlbum ) {
      	$groupAlbum->delete();
      }

      // Delete topics
      $topicTable = Engine_Api::_()->getDbtable('topics', 'advgroup');
      $topicSelect = $topicTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $topicTable->fetchAll($topicSelect) as $topic ) {
        $topic->delete();
      }
	  
	  // Delete posts
      $postTable = Engine_Api::_()->getDbtable('posts', 'advgroup');
      $postSelect = $postTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $postTable->fetchAll($postSelect) as $post ) {
        $post->delete();
      }
	  
	  //Delete polls
		$pollTable = Engine_Api::_()->getDbTable('polls','advgroup');
	    $pollSelect = $pollTable->select()-> where('user_id = ?', $payload->getIdentity());
	    foreach( $pollTable->fetchAll($pollSelect) as $groupPoll){
	      $groupPoll->delete();
	    }
		
		//Delete reports
		$reportTable = Engine_Api::_()->getDbTable('reports','advgroup');
	    $reportSelect = $reportTable->select()-> where('user_id = ?', $payload->getIdentity());
	    foreach( $reportTable->fetchAll($reportSelect) as $groupReport){
	      $groupReport->delete();
	    }
	
      //Delete all events
	    if (Engine_Api::_()->hasItemType('event'))
	    {  
	      $eventTable = Engine_Api::_()->getItemTable('event');
	      $eventSelect = $eventTable->select()->where("parent_type = 'group' and user_id = ?", $payload->getIdentity());
	      foreach ($eventTable->fetchAll($eventSelect) as $groupEvent)
	      {
	        $groupEvent->delete();
	      }
	    }

	  //Delete all videos
	    if(Engine_Api::_()->hasItemType('video')){
	      $videoTable = Engine_Api::_()->getItemTable('video');
	      $videoSelect = $videoTable->select()->where("parent_type = 'group' and owner_id = ?", $payload->getIdentity());
	      foreach ($videoTable->fetchAll($videoSelect) as $groupVideo)
	      {
	        $groupVideo->delete();
	      }
	    } 
	
      // Delete invites
      $inviteTable = Engine_Api::_()->getDbtable('invites','advgroup');
      $inviteSelect = $inviteTable->select()->where('recipient = ?',$payload->email);
      foreach($inviteTable->fetchAll($inviteSelect) as $invite){
        $invite->delete();
      }
      
      // Delete groups
      $groupTable = Engine_Api::_()->getDbtable('groups', 'advgroup');
      $groupSelect = $groupTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $groupTable->fetchAll($groupSelect) as $group ) {
        $group->delete();
      }
    }
  }

  public function addActivity($event)
  {
    $payload = $event->getPayload();
    $subject = $payload['subject'];
    $object = $payload['object'];

    // Only for object=event
    if( $object instanceof Advgroup_Model_Group &&
        Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view') ) {
      $event->addResponse(array(
        'type' => 'group',
        'identity' => $object->getIdentity()
      ));
    }

  }

  public function getActivity($event)
  {
    // Detect viewer and subject
    $payload = $event->getPayload();
    $user = null;
    $subject = null;
    if( $payload instanceof User_Model_User ) {
      $user = $payload;
    } else if( is_array($payload) ) {
      if( isset($payload['for']) && $payload['for'] instanceof User_Model_User ) {
        $user = $payload['for'];
      }
      if( isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract ) {
        $subject = $payload['about'];
      }
    }
    if( null === $user ) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() ) {
        $user = $viewer;
      }
    }
    if( null === $subject && Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
    }

    // Get feed settings
    $content = Engine_Api::_()->getApi('settings', 'core')
      ->getSetting('activity.content', 'everyone');
    
    // Get event memberships
    if( $user ) {
      $data = Engine_Api::_()->getDbtable('membership', 'advgroup')->getMembershipsOfIds($user);
      if( !empty($data) && is_array($data) ) {
        $event->addResponse(array(
          'type' => 'group',
          'data' => $data,
        ));
      }
    }
  }
}
