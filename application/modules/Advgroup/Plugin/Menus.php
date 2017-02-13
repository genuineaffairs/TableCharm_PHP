<?php
class Advgroup_Plugin_Menus {

	public function canCreateGroups()
  {
		// Must be logged-in
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity()) {
			return false;
		}

		// Must be able to create events
		if (!Engine_Api::_() -> authorization() -> isAllowed('group', $viewer, 'create')) {
			return false;
		}

		return true;
	}

 	public function canViewGroups()
  {
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Must be able to view groups
		if (!Engine_Api::_() -> authorization() -> isAllowed('group', $viewer, 'view')) {
			return false;
		}

		return true;
	}

	public function onMenuInitialize_AdvgroupMainManage()
  {
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity()) {
			return false;
		}
		return true;
	}

	public function onMenuInitialize_AdvgroupMainCreate()
  {
		$viewer = Engine_Api::_() -> user() -> getViewer();

    // Must be logged-in
		if (!$viewer -> getIdentity()) {
			return false;
		}
    // Must be able to create groups
		if (!Engine_Api::_() -> authorization() -> isAllowed('group', null, 'create')) {
			return false;
		}

		return true;
	}

 	public function onMenuInitialize_AdvgroupManageAnnouncement()
  {
    // Get viewer, group and manage settings
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$allow_manage = Engine_Api::_() -> authorization() -> getAdapter("levels") -> getAllowed('group', $viewer, 'announcement');

    // Must be a group
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

    // Must be logged-in
		if (!$viewer -> getIdentity()) {
			return false;
		}

    // Checking manage announcement permission
		if ($subject -> is_subgroup) {
			if (!$subject -> isParentGroupOwner($viewer) && !$subject -> isOwner($viewer) && !$allow_manage) {
				return false;
			}
		}
		else
		if (!$subject -> isOwner($viewer) && !$allow_manage) {
			return false;
		}

		return array(
			'label' => 'Manage Announcement',
			'icon' => 'application/modules/Advgroup/externals/images/member/promote.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'announcement',
				'action' => 'manage',
				'group_id' => $subject -> getIdentity(),
			)
		);

	}

	public function onMenuInitialize_AdvgroupProfileEdit()
  {
    // Get viewer, group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

    // Must be a group
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

    // Checking group edit permission
		if ($subject -> is_subgroup) {
			if (!$viewer -> getIdentity() || (!$subject -> isParentGroupOwner($viewer) && !$subject -> authorization() -> isAllowed($viewer, 'edit'))) {
				return false;
			}
		}
		else
		if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit')) {
			return false;
		}

		return array(
			'label' => 'Edit Group Details',
			'icon' => 'application/modules/Advgroup/externals/images/edit.png',
			'route' => 'group_specific',
			'params' => array(
				'controller' => 'group',
				'action' => 'edit',
				'group_id' => $subject -> getIdentity(),
				'ref' => 'profile'
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileStyle()
  {
    // Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

    //Must be a group
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

    // Checking style manage permission
		if ($subject -> is_subgroup) {
			if (!$viewer -> getIdentity() || (!$subject -> isParentGroupOwner($viewer) && !$subject -> authorization() -> isAllowed($viewer, 'edit'))) {
				return false;
			}
		}
		else
		if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit')) {
			return false;
		}

		return array(
			'label' => 'Edit Group Style',
			'icon' => 'application/modules/Advgroup/externals/images/style.png',
			'class' => 'smoothbox',
			'route' => 'group_specific',
			'params' => array(
				'action' => 'style',
				'group_id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			)
		);
	}

  // Delete for parent group owner
	public function onMenuInitialize_AdvgroupProfileDelete() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		if (!$viewer -> getIdentity()) {
			return false;
		}

		if (($viewer->isAdmin()&& !$subject->isOwner($viewer))
			||($subject -> is_subgroup && $subject -> isParentGroupOwner($viewer) && !$subject->isOwner($viewer)))
    {
      return array(
        'label' => 'Delete Group',
        'icon' => 'application/modules/Advgroup/externals/images/delete.png',
        'class' => 'smoothbox',
        'route' => 'group_specific',
        'params' => array(
          'action' => 'delete',
          'group_id' => $subject -> getIdentity()
        ),
      );
    }
    return false;
	}

	//Request Tab, Cancel Tab, Accept Tab, Ignore Tab, Join Tab, Leave Tab for user
	//Delete, Reject for owner group
	public function onMenuInitialize_AdvgroupProfileMember() {
    // Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

    //Must be a group
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity()) {
			return false;
		}

		$row = $subject -> membership() -> getRow($viewer);
		
		// Not yet associated at all
		if (null === $row||$row->rejected_ignored) {
     if($subject->is_subgroup){
       $parent_group = $subject->getParentGroup();
       
       if( $parent_group-> membership() -> isResourceApprovalRequired()){
         return array(
					'label' => 'Request Membership',
					'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'request',
						'group_id' => $subject -> getIdentity(),
					),
				);
       }
       elseif($subject -> membership() -> isResourceApprovalRequired()){
        return array(
					'label' => 'Request Membership',
					'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'request',
						'group_id' => $subject -> getIdentity(),
					),
				);
       }
       else{
         return array(
					'label' => 'Join Group',
					'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'join',
						'group_id' => $subject -> getIdentity()
					),
				);
       }
     }
     else{
     	
			if ($subject -> membership() -> isResourceApprovalRequired()) {
				
				return array(
					'label' => 'Request Membership',
					'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'request',
						'group_id' => $subject -> getIdentity(),
					),
				);
			}
			else {
				return array(
					'label' => 'Join Group',
					'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'join',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
     }
		}

		// Full member
		// @todo consider owner
		else
		if ($row -> active) {
			if (!$subject -> isOwner($viewer) && !$subject -> isParentGroupOwner($viewer)) {
				return array(
					'label' => 'Leave Group',
					'icon' => 'application/modules/Advgroup/externals/images/member/leave.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'leave',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
			else {
				return array(
					'label' => 'Delete Group',
					'icon' => 'application/modules/Advgroup/externals/images/delete.png',
					'class' => 'smoothbox',
					'route' => 'group_specific',
					'params' => array(
						'action' => 'delete',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
		}
		
else
		if (!$row -> resource_approved && $row -> user_approved ) {
			return array(
				'label' => 'Cancel Membership Request',
				'icon' => 'application/modules/Advgroup/externals/images/member/cancel.png',
				'class' => 'smoothbox',
				'route' => 'group_extended',
				'params' => array(
					'controller' => 'member',
					'action' => 'cancel',
					'group_id' => $subject -> getIdentity()
				),
			);
		}
		
else
		if (!$row -> user_approved && $row -> resource_approved ) {
			return array(
				array(
					'label' => 'Accept Membership Request',
					'icon' => 'application/modules/Advgroup/externals/images/member/accept.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'accept',
						'group_id' => $subject -> getIdentity()
					),
				),
				array(
					'label' => 'Ignore Membership Request',
					'icon' => 'application/modules/Advgroup/externals/images/member/reject.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'reject',
						'group_id' => $subject -> getIdentity()
					),
				)
			);
		}

		else {
			throw new Advgroup_Model_Exception('Wow, something really strange happened.');
		}

		return false;
	}

//Report Tab 
	public function onMenuInitialize_AdvgroupProfileReport() {
    // Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

    //Must be a group
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		
		if (!$viewer -> getIdentity()) {
			return false;
		}
		
		return array(
				'label' => 'Report Group',
				'icon' => 'application/modules/Advgroup/externals/images/report.png',
				'class' => 'smoothbox',
				'route' => 'group_report',
				'params' => array(
						'group_id' => $subject -> getIdentity(),
						'format' => 'smoothbox',
				),
		);
		
	}
	//Invite Friends Tab
	public function onMenuInitialize_AdvgroupProfileInvite() {
    // Get group and viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

    // Must be a group
		if ($subject -> getType() !== 'group') {
			throw new Group_Model_Exception('Whoops, not a group!');
		}

    // Checking invite permission
		if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "invite")) {
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "invite")) {
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "invite")) {
			return false;
		}

		return array(
			'icon' => 'application/modules/Advgroup/externals/images/member/invite.png',
			'class' => 'smoothbox',
			'route' => 'group_extended',
			'params' => array(
				//'module' => 'group',
				'controller' => 'member',
				'action' => 'invite',
				'group_id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			),
		);
	}
	
	//Invite All Users Tab
	public function onMenuInitialize_AdvgroupProfileInviteAll() {
		return false;
	}
	
	// Invite Management Tab
	public function onMenuInitialize_AdvgroupProfileInviteManage() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$allow_manage = Engine_Api::_() -> authorization() -> getAdapter("levels") -> getAllowed('group', $viewer, 'invitation');

		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity()) {
			return false;
		}

		if ($subject -> is_subgroup) {
			if (!$subject -> isParentGroupOwner($viewer) && !$subject -> isOwner($viewer) && !$allow_manage) {
				return false;
			}
		}
		else
		if (!$subject -> isOwner($viewer) && !$allow_manage) {
			return false;
		}

		return array(
			'label' => 'Invitations Management',
			'icon' => 'application/modules/Advgroup/externals/images/member/invite.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'invite-manage',
				'action' => 'manage',
				'group_id' => $subject -> getIdentity(),
			)
		);
	}
	//Share Tab
	public function onMenuInitialize_AdvgroupProfileShare() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity()) {
			return false;
		}

		return array(
			'label' => 'Share Group',
			'icon' => 'application/modules/Advgroup/externals/images/share.png',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'activity',
				'controller' => 'index',
				'action' => 'share',
				'type' => $subject -> getType(),
				'id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			),
		);
	}

	public function onMenuInitialize_AdvgroupProfileMessage() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity() || !$subject -> membership() -> isMember($viewer)) {
			return false;
		}

		return array(
			'label' => 'Message Members',
			'icon' => 'application/modules/Messages/externals/images/send.png',
			'route' => 'messages_general',
			'params' => array(
				'action' => 'compose',
				'to' => $subject -> getIdentity(),
				'multi' => 'group'
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileInvitenew() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup) {
      return false;
    }
    
		if (!$subject -> authorization() -> isAllowed($viewer, "invite")) {
			return false;
		}

		return array(
			'icon' => 'application/modules/Advgroup/externals/images/member/invite.png',
			'route' => 'group_extended',
			'class' => 'smoothbox',
			'params' => array(
				//'module' => 'group',
				'controller' => 'invite',
				'action' => 'invite',
				'group_id' => $subject -> getIdentity(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileAlbum() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
			return false;
		}

		return array(
			'label' => 'Group Albums',
			'icon' => 'application/modules/Advgroup/externals/images/photo/view.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'album',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileDiscussion() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
			return false;
		}

		return array(
			'label' => 'Group Discussions',
			'icon' => 'application/modules/Advgroup/externals/images/types/post.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'topic',
				'action' => 'index',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileEvent() {
		return false;
	}

	public function onMenuInitialize_AdvgroupProfilePoll() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
			return false;
		}

		return array(
			'label' => 'Group Polls',
			'icon' => 'application/modules/Advgroup/externals/images/poll/poll.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'poll',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

  public function onMenuInitialize_AdvgroupProfileVideo(){
    //Check ynvideo plugin is installed and enabled
    $video_enable = Engine_Api::_()->advgroup()->checkYouNetPlugin('ynvideo');
    if(!$video_enable) return false;

    //Get viewer and subject
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

    //Group privacy checking
    if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
			return false;
		}

    return array(
			'label' => 'Group Videos',
			'icon' => 'application/modules/Advgroup/externals/images/video/video.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'video',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
  }
	public function onMenuInitialize_AdvgroupProfileUsefulLink() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
    if(!$viewer -> getIdentity()){
      return false;
    }
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup) {
			if (!$subject->isOwner($viewer) && !$subject -> isParentGroupOwner($viewer)) {
				return false;
			}
		}
		else
		if (!$subject->isOwner($viewer)) {
			return false;
		}

		return array(
			'label' => 'Group Useful Links',
			'icon' => 'application/modules/Advgroup/externals/images/edit.png',
			'route' => 'group_link',
			'params' => array(
				'action' => 'manage',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileCreateSubGroup() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup) {
			return false;
		}

		if (!$subject -> authorization() -> isAllowed(null, 'sub_group')) {
			return false;
		}
		return array(
			'label' => 'Create Sub Group',
			'icon' => 'application/modules/Advgroup/externals/images/create.png',
			'route' => 'group_general',
			'params' => array(
				'action' => 'create',
				'parent' => $subject -> getIdentity(),
			)
		);

	}

	public function onMenuInitialize_AdvgroupProfileTransfer() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		if (!$viewer->isAdmin() && !$subject -> isOwner($viewer) && !$subject -> isParentGroupOwner($viewer)) {
			return false;
		}

		return array(
			'label' => 'Transfer Owner',
			'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
			'route' => 'group_specific',
		  'class' => 'smoothbox',
			'params' => array(
				'action' => 'transfer',
				'group_id' => $subject -> getIdentity(),
			),
		);
	}

  public function onMenuInitialize_AdvgroupProfileWiki()
  {
    //Check ynvideo plugin is installed and enabled
    $wiki_enable = Engine_Api::_()->advgroup()->checkYouNetPlugin('ynwiki');
    if(!$wiki_enable) return false;

    //Get viewer and subject
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()-> getSubject();
    if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
    
    //Group privacy checking
    if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
			return false;
		}
    //generate Group Wiki Menu
    return array(
			'label' => 'Group Wikis',
			'icon' => 'application/modules/Advgroup/externals/images/wiki/page-icon.png',
			'route' => 'group_extended',
			'params' => array(
			'controller' => 'wiki',
			'action' => 'list',
			'subject' => $subject -> getGuid(),
			)
		);
  }
  
	public function onMenuInitialize_AdvgroupProfileActivity()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_() -> core() -> getSubject();

    if ($subject -> getType() !== 'group')
    {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
      if ($subject -> is_subgroup)
      {
          if (!$viewer -> getIdentity() || (!$subject -> isParentGroupOwner($viewer) && !$subject -> authorization() -> isAllowed($viewer, 'edit')))
          {
            return false;
          }
      }
      else
      {
          if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit')) {
            return false;
          }
      }

      return array(
			'label' => 'Group Activities',
			'icon' => 'application/modules/Activity/externals/images/activity/post.png',
			'class' => 'smoothbox',
			'route' => 'group_activity',
			'params' => array(
				'action' => 'activity',
				'group_id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			)
		);
  }
  
	public function onMenuInitialize_AdvgroupProfileInviteContactImport()
  {
		return false;
	}

}
