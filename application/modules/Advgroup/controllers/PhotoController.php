<?php
class Advgroup_PhotoController extends Core_Controller_Action_Standard {
	public function init() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('advgroup_photo', $photo_id))) {
				Engine_Api::_() -> core() -> setSubject($photo);
			} elseif (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id))) {
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}

		$this -> _helper -> requireUser -> addActionRequires(array('upload', 'upload-photo', // Not sure if this is the right
		'edit', ));

		$this -> _helper -> requireSubject -> setActionRequireTypes(array('list' => 'group', 'upload' => 'group', 'view' => 'advgroup_photo', 'edit' => 'advgroup_photo', ));
	}

	/**
	 * @method
	 * @param
	 */
	public function listAction() {
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $group -> getSingletonAlbum();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($group -> is_subgroup) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		/**
		 * TODO: some thing lese.
		 */
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if ($parent_group -> authorization() -> isAllowed(null, 'photo')) {
//				$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
//			} else {
//				$canUpload = false;
//			}
//		} else {
			$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
//		}
		$levelUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'photo');

		if ($canUpload && $levelUpload) {
			$this -> view -> canUpload = true;
		} else {
			$this -> view -> canUpload = false;
		}
	}

	public function viewAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $photo -> getCollection();
		$this -> view -> group = $group = $photo -> getGroup();
		$this -> view -> canEdit = $photo -> canEdit(Engine_Api::_() -> user() -> getViewer());

		if ($group -> is_subgroup) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		if (!$viewer || !$viewer -> getIdentity() || $photo -> user_id != $viewer -> getIdentity()) {
			$photo -> view_count = new Zend_Db_Expr('view_count + 1');
			$photo -> save();
		}
	}

	public function uploadAction() {
		if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
			return $this -> _forward('upload-photo', null, null, array('format' => 'json'));
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'photo') -> isValid()) {
			return;
		}
		
//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if ($parent_group -> authorization() -> isAllowed(null, 'photo')) {
//				$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
//			} else {
//				$canUpload = false;
//			}
//		} else {
			$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
//		}
		
		$levelUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'photo');
		if (!$canUpload || !$levelUpload) {
			$this -> renderScript("_error.tpl");
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$album_id = $this -> _getParam('album_id'))
			$album = $group -> getSingletonAlbum();
		else
			$album = Engine_Api::_() -> getItem('advgroup_album', $album_id);
//		$max = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'numberPhoto');
    $max = Engine_Api::_() -> advgroup() ->getNumberValue('group', $viewer->level_id, 'numberPhoto');
		if ($max > 0 && $album -> getPhotoCount($viewer -> getIdentity()) >= $max) {
			$this -> renderScript('/photo/max.tpl');
			return;
		}
		$this -> view -> album = $album;
		$this -> view -> group = $group;
		$this -> view -> form = $form = new Advgroup_Form_Photo_Upload();
		$form -> file -> setAttrib('data', array('group_id' => $group -> getIdentity()));

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('advgroup_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();
			$params = array('group_id' => $group -> getIdentity(), 'user_id' => $viewer -> getIdentity(), );

			// Add action and attachments
			$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $group, 'advgroup_photo_upload', null, array('count' => count($values['file'])));

			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id) {
				$photo = Engine_Api::_() -> getItem("advgroup_photo", $photo_id);
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				/*
				 if( $set_cover )
				 {
				 $album->photo_id = $photo_id;
				 $album->save();
				 $set_cover = false;
				 }
				 */

				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo->group_id = $group->group_id;
				$photo -> save();

				if ($action instanceof Activity_Model_Action && $count < 8) {
					$api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> _redirectCustom($album);
	}

	public function uploadPhotoAction() {
		$group = Engine_Api::_() -> getItem('group', $this -> _getParam('group_id'));

		//Check upload authorization
//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if (!$parent_group -> authorization() -> isAllowed(null, 'photo')) {
//				return $this->_helper->requireAuth->forward();
//			} elseif (!$group -> authorization() -> isAllowed(null, 'photo')) {
//				return $this->_helper->requireAuth->forward();
//			}
//		} else
      if (!$group -> authorization() -> isAllowed(null, 'photo')) {
			return $this->_helper->requireAuth->forward();
		}

		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		// @todo check auth
		//$group

		$values = $this -> getRequest() -> getPost();
		if (empty($values['Filename'])) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return;
		}

		if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return;
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if (!$album_id = $this -> _getParam('album_id')) {
				$album = $group -> getSingletonAlbum();
			} else
				$album = Engine_Api::_() -> getItem('advgroup_album', $album_id);

			$params = array(
				// We can set them now since only one album is allowed
				//'collection_id' => $album -> getIdentity(), 
				//'album_id' => $album -> getIdentity(),
				//'group_id' => $group -> getIdentity(), 
				'user_id' => $viewer -> getIdentity(), 
				);

			$photoTable = Engine_Api::_() -> getItemTable('advgroup_photo');
			$photo = $photoTable -> createRow();
			$photo -> setFromArray($params);
			$photo -> save();

			$photo -> setPhoto($_FILES['Filedata']);

			$this -> view -> status = true;
			$this -> view -> name = $_FILES['Filedata']['name'];
			$this -> view -> photo_id = $photo -> photo_id;

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			// throw $e;
			return;
		}
	}

	public function editAction() {
		$photo = Engine_Api::_() -> core() -> getSubject();
		$group = $photo -> getParent('group');
		
		//Check edit authorization
		$canEdit = $group -> authorization() -> isAllowed(null, 'photo.edit');
		
		if (!$canEdit && !$photo -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer)) {
			return $this -> renderScript('_private.tpl');
		}
			
		$this -> view -> form = $form = new Advgroup_Form_Photo_Edit();

		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> setFromArray($form -> getValues()) -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Changes saved')), 'layout' => 'default-simple', 'parentRefresh' => true, 'closeSmoothbox' => true, ));
	}

	public function deleteAction() {
		$photo = Engine_Api::_() -> core() -> getSubject();
		$album = $photo -> getCollection();
		$group = $photo -> getParent('group');
		
		//Check edit authorization
		$canEdit = $group -> authorization() -> isAllowed(null, 'photo.edit');
		
		if (!$canEdit && !$photo -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer)) {
			return $this -> renderScript('_private.tpl');
		}
		
		$this -> view -> form = $form = new Advgroup_Form_Photo_Delete();

		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Photo deleted')), 'layout' => 'default-simple', 'parentRedirect' => $album -> getHref(), 'closeSmoothbox' => true, ));
	}

}
