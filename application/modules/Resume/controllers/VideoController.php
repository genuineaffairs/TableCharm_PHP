<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Resume_VideoController extends Core_Controller_Action_Standard {


  public function init() {

    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('video_id')) &&
          null !== ($video = Engine_Api::_()->getItem('resume_video', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($video);
      }

      else if( 0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
          null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id)) )
      {
        Engine_Api::_()->core()->setSubject($resume);
      }
    }
    
    
    $this->view->headLink()->appendStylesheet(
            $this->view->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css');
    
//    $this->_helper->requireUser->addActionRequires(array(
//      'upload',
//      'upload-video', // Not sure if this is the right
//      'edit',
//    ));
//
//    $this->_helper->requireSubject->setActionRequireTypes(array(
//      'list' => 'resume',
//      'upload' => 'resume',
//      'view' => 'resume_video',
//      'edit' => 'resume_video',
//    ));
  }
  
  //ACTION FOR CREATE VIDEO
  public function createAction() {
    
    $resume_id = $this->_getParam('resume_id');
    $this->view->resume = $resume = Engine_Api::_()->getItem('resume', $resume_id);

    if (!$resume->authorization()->isAllowed(null, 'video'))  {
      return $this->_forward('requireauth', 'error', 'core');
    }
    
    //UPLOAD VIDEO
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      return $this->_forward('upload-video', null, null, array('format' => 'json'));
    }

    //GET NAVIGATION
//    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
//            ->getNavigation('sitepage_main');

    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

//    $getPackagevideoCreate = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
   
    // PACKAGE BASE PRIYACY START
//    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
//      if (!Engine_Api::_()->sitepage()->allowPackageContent($resume->package_id, "modules", "sitepagevideo")) {
//        return $this->_forwardCustom('requireauth', 'error', 'core');
//      }
//    } else {
//      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($resume, 'svcreate');
//      if (empty($isPageOwnerAllow) ) {
//        return $this->_forwardCustom('requireauth', 'error', 'core');
//      }
//    }
    // PACKAGE BASE PRIYACY END
    //VIDEO UPLOAD PROCESS
//    $this->view->imageUpload = Engine_Api::_()->sitepagevideo()->isUpload();

    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->_forwardCustom('requireauth', 'error', 'core');
//    }
//
//     $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'edit');
//    if (empty($isManageAdmin)) {
//      $this->view->can_edit = $can_edit = 0;
//    } else {
//      $this->view->can_edit = $can_edit = 1;
//    }
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'svcreate');
//    if (empty($isManageAdmin) && empty($can_edit)) {
//
//      return $this->_forwardCustom('requireauth', 'error', 'core');
//    }
    //END MANAGE-ADMIN CHECK
    //FORM GENERATON

//    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
//			$this->view->form = $form = new Sitepagevideo_Form_Video();
//    } else {
//      $this->view->form = $form = new Sitepagevideo_Form_SitemobileVideo();
//    }
    
    $this->view->form = $form = new Resume_Form_Video_Create();

    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue( $this->_getParam('type') );
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues('url');
      return;
    }

//    if (empty($getPackagevideoCreate)) {
//      return;
//    }

    //GET FORM VALUES
    $values = $form->getValues();

    $values['owner_id'] = $viewer->getIdentity();
    
    $insert_action = false;

    //VIDEO CREATION PROCESS
    $videoTable = Engine_Api::_()->getDbtable('videos', 'resume');

    $db = $videoTable->getAdapter();
    $db->beginTransaction();
    try {

      if ($values['type'] == 3) {
        $resume_video = Engine_Api::_()->getItem('resume_video', $this->_getParam('id'));
      } else {
        $resume_video = $videoTable->createRow();
      }

      $resume_video->setFromArray($values);
      $resume_video->resume_id = $resume_id;
      $resume_video->save();

      //THUMBNAIL CREATION
      $thumbnail = $this->handleThumbnail($resume_video->type, $resume_video->code);
      $ext = ltrim(strrchr($thumbnail, '.'), '.');
      $thumbnail_parsed = @parse_url($thumbnail);

      if (@GetImageSize($thumbnail)) {
        $valid_thumb = true;
      } else {
        $valid_thumb = false;
      }

      if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(120, 240)
                ->write($thumb_file)
                ->destroy();

        try {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
              'parent_type' => $resume_video->getType(),
              'parent_id' => $resume_video->getIdentity()
                  ));

          //REMOVE TEMP FILES
          @unlink($thumb_file);
          @unlink($tmp_file);
        } catch (Exception $e) {
          
        }
        $information = $this->handleInformation($resume_video->type, $resume_video->code);
        $resume_video->duration = $information['duration'];
        if( !$resume_video->description ) {
          $resume_video->description = $information['description'];
        }
        $resume_video->photo_id = $thumbFileRow->file_id;
        $resume_video->status = 1;
        $resume_video->featured = 0;
        $resume_video->save();

        //INSERT NEW ACTION ITEM
        $insert_action = true;
      }

      if ($values['ignore'] == true) {
        $resume_video->status = 1;
        $resume_video->save();
        $insert_action = true;
      }

      //COMMENT PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
      $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($resume_video, $role, 'comment', ($i <= $commentMax));
      }

      //TAG WORK
      if (!empty($values['tags'])) {
        $tags = preg_split('/[,]+/', $values['tags']);
        $resume_video->tags()->addTagMaps($viewer, $tags);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      if($insert_action && $resume_video->search == 1){
        $owner = $resume_video->getOwner();
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $resume_video, 'resume_video_new');
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $resume_video);
        }
      }

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($resume_video) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('resume_id' => $resume->resume_id, 'slug' => $resume->getSlug(), 'tab' => $tab_selected_id), 'resume_profile', true);
  }

  //ACTION FOR EDIT VIDEO
  public function editAction() {

    //CHECK USER VALIDATION
//    if (!$this->_helper->requireUser()->isValid()) {
//      return;
//    }

    //GET PAGE ID AND SUBJECT
    $resume_id = $this->_getParam('resume_id', 0);
    $this->view->resume = $resume = Engine_Api::_()->getItem('resume', $resume_id);
    
    if (!$resume->authorization()->isAllowed(null, 'video'))  {
      return $this->_forward('requireauth', 'error', 'core');
    }


    //GET VIDEO OBJECT
    $resume_video = Engine_Api::_()->getItem('resume_video', $this->_getParam('video_id'));

    //GET TAB ID
    $this->view->tab_selected_id = $this->_getParam('tab');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //PACKAGE BASE PRIYACY START
//    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
//      if (!Engine_Api::_()->sitepage()->allowPackageContent($resume->package_id, "modules", "sitepagevideo")) {
//        return $this->_forwardCustom('requireauth', 'error', 'core');
//      }
//    } else {
//      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($resume, 'svcreate');
//      if (empty($isPageOwnerAllow)) {
//        return $this->_forwardCustom('requireauth', 'error', 'core');
//      }
//    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'edit');
//    if (empty($isManageAdmin)) {
//      $can_edit = 0;
//    } else {
//      $can_edit = 1;
//    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, VIDEO OWNER AND PAGE OWNER CAN EDIT VIDEO
//    if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1) {
//      return $this->_forwardCustom('requireauth', 'error', 'core');
//    }

    //GET NAVIGATION
//    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
//            ->getNavigation('sitepage_main');

    //FORM GENERATION
    $this->view->form = $form = new Resume_Form_Video_Edit();

    //PREPARE TAGS
    $videoTags = $resume_video->tags()->getTagMaps();
    $tagString = '';
    foreach ($videoTags as $tagmap) {
      if ($tagString !== '') {
        $tagString .= ', ';
      }
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($resume_video->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    //PROCESS
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $resume_video->setFromArray($values);

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $resume_video->tags()->setTagMaps($viewer, $tags);
      $resume_video->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    
    $tab = $this->_getParam('tab', null);
    if($tab) {
      $tab_url = '/tab/' . $tab;
    } else {
      $tab_url = '';
    }

    //REDIRECTING TO THE EVENT VIEW PAGE
    return $this->_redirectCustom($resume_video->getHref() . $tab_url, array('prependBase' => false));
  }

  //ACTION FOR DELETE VIDEO
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    //GET VIDEO OBJECT
    $this->view->resume_video = $resume_video = Engine_Api::_()->getItem('resume_video', $this->getRequest()->getParam('video_id'));

    //GET VIDEO TITLE
    $this->view->title = $resume_video->title;

    //GET PAGE ID
    $resume_id = $resume_video->resume_id;

    //GET NAVIGATION 
//    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
//            ->getNavigation('sitepage_main');

    //GET SITEPAGE SUBJECT
    if (!empty($resume_id)) {
      $this->view->resume = $resume = Engine_Api::_()->getItem('resume', $resume_id);
      
      if (!$resume->authorization()->isAllowed(null, 'video'))  {
        return $this->_forward('requireauth', 'error', 'core');
      }
      
      //PACKAGE BASE PRIYACY START
//      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
//        if (!Engine_Api::_()->sitepage()->allowPackageContent($resume->package_id, "modules", "sitepagevideo")) {
//          return $this->_forwardCustom('requireauth', 'error', 'core');
//        }
//      } else {
//        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($resume, 'svcreate');
//        if (empty($isPageOwnerAllow)) {
//          return $this->_forwardCustom('requireauth', 'error', 'core');
//        }
//      }
      //PACKAGE BASE PRIYACY END
      //START MANAGE-ADMIN CHECK
//      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'edit');
//      if (empty($isManageAdmin)) {
//        $can_edit = 0;
//      } else {
//        $can_edit = 1;
//      }
      //END MANAGE-ADMIN CHECK
    } else {
//      $can_edit = 1;
    }
    if (!$resume_video) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    //VIDEO OWNER AND PAGE OWNER CAN DELETE VIDEO
//    if ($viewer_id != $resume_video->owner_id && $can_edit != 1) {
//      return $this->_forwardCustom('requireauth', 'error', 'core');
//    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $resume_video->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      Engine_Api::_()->getDbtable('videoRatings', 'resume')->delete(array('video_id =?' => $this->getRequest()->getParam('video_id')));

      $resume_video->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    return $this->_helper->redirector->gotoRoute(array('resume_id' => $resume->resume_id, 'slug' => $resume->getSlug(), 'tab' => $tab_selected_id), 'resume_profile', true);
  }

  //ACTION FOR VIEW VIDEO
  public function viewAction() {

    //IF SITEPAGEVIDEO SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('resume_video')->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET VIDEO ITEM
    $resume_video = Engine_Api::_()->getItem('resume_video', $this->getRequest()->getParam('video_id'));

    //GET SITEPAGE ITEM
    $resume = Engine_Api::_()->getItem('resume', $resume_video->resume_id);
    
    $this->view->video = $resume_video;
    $this->view->rating_count = Engine_Api::_()->getDbTable('videoRatings', 'resume')->ratingCount($resume_video->getIdentity());
    $this->view->rated = Engine_Api::_()->getDbTable('videoRatings', 'resume')->checkRated($resume_video->getIdentity(), $viewer->getIdentity());
    $this->view->videoSuggLink = false;
    $this->view->viewer_id = $viewer_id;
    $this->view->resume = $resume;
    $this->view->videoEmbedded = $embedded = "";
    
    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');
    
    if ($resume_video->type != 3) {
      // Embedded video content
      $this->view->videoEmbedded = $embedded = $resume_video->getRichContent(true);
    } else {
      // Uploaded video file location
      $this->view->video_location = Engine_Api::_()->storage()->get($resume_video->file_id, $resume_video->getType())->getHref();
    }

    //PACKAGE BASE PRIYACY START
//    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
//      if (!Engine_Api::_()->sitepage()->allowPackageContent($resume->package_id, "modules", "sitepagevideo")) {
//        return $this->_forwardCustom('requireauth', 'error', 'core');
//      }
//    } else {
//      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($resume, 'svcreate');
//      if (empty($isPageOwnerAllow)) {
//        return $this->_forwardCustom('requireauth', 'error', 'core');
//      }
//    }
    //PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'svcreate');
//    if (empty($isManageAdmin)) {
//      $this->view->can_create = 0;
//    } else {
//      $this->view->can_create = 1;
//    }
    
    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->_forwardCustom('requireauth', 'error', 'core');
//    }
//    //END MANAGE-ADMIN CHECK
//
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($resume, 'edit');
//    if (empty($isManageAdmin)) {
//      $can_edit = 0;
//    } else {
//      $can_edit = 1;
//    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE VIDEO OR NOT
//    if ($viewer_id != $resume_video->owner_id && $can_edit != 1 && ($resume_video->search != 1 || $resume_video->status != 1)) {
//      return $this->_forwardCustom('requireauth', 'error', 'core');
//    }
  }

  //ACTION FOR DO RATING
  public function rateAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $rating = $this->_getParam('rating');
    $video_id = $this->_getParam('video_id');

    $ratingTable = Engine_Api::_()->getDbtable('videoRatings', 'resume');
    $db = $ratingTable->getAdapter();
    $db->beginTransaction();

    try {

      $ratingTable->setRating($video_id, $user_id, $rating);

      $total = $ratingTable->ratingCount($video_id);

      $resume_video = Engine_Api::_()->getItem('resume_video', $video_id);

      //UPDATE CURRENT AVERAGE RATING IN VIDEO TABLE
      $rating = $ratingTable->rateVideo($video_id);

      $resume_video->rating = $rating;
      $resume_video->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  //ACTION FOR HANDLES THUMBNAIL
  public function handleThumbnail($type, $code = null) {
    switch ($type) {
      //youtube
      case "1":
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return "http://img.youtube.com/vi/$code/default.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
    }
  }

  //ACTION FOR VIDEO COMPOSE UPLOAD
  public function composeUploadAction() {

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composer_type = $this->_getParam('c_type', 'wall');

    // extract code
    //$code = $this->extractCode("http://www.youtube.com/watch?v=5osJ8-NttnU&feature=popt00us08", $video_type);
    //$code = parse_url("http://vimeo.com/3945157/asd243", PHP_URL_PATH);

    $code = $this->extractCode($video_url, $video_type);
    // check if code is valid
    // check which API should be used
    if ($video_type == 1) {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == 2) {
      $valid = $this->checkVimeo($code);
    }


    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    //$values['user_id'] = $viewer->getIdentity();
    //  $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);

    if ($valid) {
      $db = Engine_Api::_()->getDbtable('videos', 'resume')->getAdapter();
      $db->beginTransaction();

      try {
        $information = $this->handleInformation($video_type, $code);
        // create video
        $table = Engine_Api::_()->getDbtable('videos', 'resume');
        $video = $table->createRow();
        $video->title = $information['title'];
        $video->description = $information['description'];
        $video->duration = $information['duration'];
        $video->owner_id = $viewer->getIdentity();
        $video->code = $code;
        $video->type = $video_type;
        $video->save();

        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);

        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(120, 240)
                ->write($thumb_file)
                ->destroy();

        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
                ));

        // If video is from the composer, keep it hidden until the post is complete
        if ($composer_type)
          $video->search = 0;

        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }


      // make the video public
      if ($composer_type === 'wall') {
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $roles));
          $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
        }
      }

      $this->view->status = true;
      $this->view->video_id = $video->video_id;
      $this->view->photo_id = $video->photo_id;
      $this->view->title = $video->title;
      $this->view->description = $video->description;
      $this->view->src = $video->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
    } else {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
    }
  }

  //ACTION FOR VIDEO VALIDATON
  public function validationAction() {
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    // check which API should be used
    if ($video_type == "youtube") {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == "vimeo") {
      $valid = $this->checkVimeo($code);
    }

    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }

  //HELPER FUNCTIONS
  public function extractCode($url, $type) {
    switch ($type) {
      //youtube
      case "1":
        // change new youtube URL to old one
        $new_code = @pathinfo($url);
        $url = preg_replace("/#!/", "?", $url);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($url);
        $code = "code";
        $parameters = $arr["query"];
        parse_str($parameters, $data);
        $code = $data['v'];
        if ($code == "") {
          $code = $new_code['basename'];
        }

        return $code;
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);
        return $code['basename'];
    }
  }

  //YOUTUBE FUNCTIONS
  public function checkYouTube($code) {
    if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code))
      return false;
    if ($data == "Video not found")
      return false;
    return true;
  }

  //VIMEO FUNCTIONS
  public function checkVimeo($code) {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $video_id = count($data->video->id);
    if ($video_id == 0)
      return false;
    return true;
  }

  //ACTION FOR UPLOAD VIDEO
  public function uploadVideoAction() {
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }
    
    $resume_id = $this->_getParam('resume_id');
    $this->view->resume = $resume = Engine_Api::_()->getItem('resume', $resume_id);

    if (!$resume->authorization()->isAllowed(null, 'video'))  {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('videos', 'resume')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
          'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->resume()->createResumevideo($params, $_FILES['Filedata'], $values);
      $video->title = $_FILES['Filedata']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->video_id;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.') . $e;
      // throw $e;
      return;
    }
  }

  //ACTION FOR UPLOAD VIDEO
  public function uploadAction() {
    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forward('upload-video', null, null, array('format' => 'json'));

    if (!$this->_helper->requireUser()->isValid())
      return;
    
    $this->view->form = $form = new Resume_Form_Video_Create();
//    $this->view->navigation = $this->getNavigation();

    if (!$this->getRequest()->isPost()) {
      if (null !== ($video_id = $this->_getParam('video_id'))) {
        $form->populate(array(
            'video' => $video_id
        ));
      }
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $video = $form->saveValues();
  }

  //ACTION FOR HANDLE INFORMATION
  public function handleInformation($type, $code) {
    switch ($type) {
      //youtube
      case "1":
        $yt = new Zend_Gdata_YouTube();
        $youtube_video = $yt->getVideoEntry($code);
        $information = array();
        $information['title'] = $youtube_video->getTitle();
        $information['description'] = $youtube_video->getVideoDescription();
        $information['duration'] = $youtube_video->getVideoDuration();
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
    }
  }

  public function browseAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('resume', null, 'view')->isValid())
      return;

   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR EMBEDING THE VIDEO
  public function embedAction() {

    //GET SUBJECT
    $this->view->video = $video = Engine_Api::_()->core()->getSubject('resume_video');

    //CHECK THAT EMBEDDING IS ALLOWED OR NOT
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
      $this->view->error = 1;
      return;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $this->view->error = 2;
      return;
    }

    //GET EMBED CODE
    $this->view->embedCode = $video->getEmbedCode();
  }

  //ACTION FOR FETCHING THE VIDEO INFORMATION
  public function externalAction() {

    //GET SUBJECT
    $this->view->video = $video = Engine_Api::_()->core()->getSubject('resume_video');

    //CHECK THAT EMBEDDING IS ALLOWED OR NOT
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
      $this->view->error = 1;
      return;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $this->view->error = 2;
      return;
    }

    //GET EMBED CODE
    $this->view->videoEmbedded = "";
    if ($video->status == 1) {
      $video->view_count++;
      $video->save();
      $this->view->videoEmbedded = $video->getRichContent(true);
    }

    //TRACK VIEWS FROM EXTERNAL SOURCES
    Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('video.embedviews');

    //GET FILE LOCATION
    if ($video->type == 3 && $video->status == 1) {
      if (!empty($video->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
        }
      }
    }

    //GET RATING DATA
    $this->view->rating_count = Engine_Api::_()->getDbTable('videoRatings', 'resume')->ratingCount($video->getIdentity());
  }

}

?>