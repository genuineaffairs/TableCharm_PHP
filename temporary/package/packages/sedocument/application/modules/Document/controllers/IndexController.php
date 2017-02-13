<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Document/Api/Scribd.php';
class Document_IndexController extends Seaocore_Controller_Action_Standard {

	protected $TIME_LIMIT = 3456000;

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//DOCUMENT VIEW PRIVACY CHECK
    if( !$this->_helper->requireAuth()->setAuthParams('document', null, 'view')->isValid() ) return;

    //SET SCRIBD API AND SCECRET KEY
    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->document_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $this->scribd = new Scribd($this->scribd_api_key, $this->scribd_secret);
  }

  //ACTION FOR BROWSE LIST DOCUMENT
  public function browseAction() {

    //SHOW WIDGETS
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if($coreversion < '4.1.0') {
			$this->_helper->content->render();
    } 
    else {
			$this->_helper->content
         ->setNoRender()
         ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE HOME DOCUMENT
  public function homeAction() {

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //ACTION FOR MOBILE BROWSE LIST DOCUMENT
  public function mobiBrowseAction() {

    //SHOW WIDGETS
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if($coreversion < '4.1.0') {
			$this->_helper->content->render();
    } 
    else {
			$this->_helper->content
         ->setNoRender()
         ->setEnabled();
    }
  }

  //ACTION FOR MOBILE HOME LIST DOCUMENT
  public function mobiHomeAction() {

    //SHOW WIDGETS
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if($coreversion < '4.1.0') {
			$this->_helper->content->render();
    } 
    else {
			$this->_helper->content
         ->setNoRender()
         ->setEnabled();
    }
  }

  //ACTION FOR SHOW USERS DOCUMENTS
  public function manageAction() { 

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER DETAILS
		$viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
		$level_id = $viewer->level_id;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main', array(), 'document_main_manage');

    //VIEW, EDIT, DELETE AND PROFILE DOCUMENT CHECKS FOR DOCUMENT
    $this->view->can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
    $this->view->can_edit = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'edit');
    $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'delete');
		$this->view->can_profile_doc = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc');

    //CHECK VIEWER CAN CREATE DOCUMENT
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('document', null, 'create')->checkRequire();

    //CHECK THAT RATING IS VIEABLE OR NOT
    $this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

    //GET SEARCH FORM
    $this->view->form = $form = new Document_Form_Search();
    $form->removeElement('show');

		//GET FORM VALUES
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = $values;

    $values['user_id'] = $viewer_id;
    $this->view->assign($values);

		$documentTable = Engine_Api::_()->getDbtable('documents', 'document');

    //GET DOCUMENTS FOR CONVERSION
    $doc_forUpdate = $documentTable->updateDocs($viewer_id);

    foreach ($doc_forUpdate as $value) {

			if (empty($value->doc_id)) {
				continue;
			}

      //SET SCRIBD MY_USER_ID
      $this->scribd->my_user_id = $value->owner_id;

      try {
        $stat = trim($this->scribd->getConversionStatus($value->doc_id));
      }
			catch (Exception $e) {
        $message = $e->getMessage();
      }

      if ($stat == 'DONE') {
        try {
          //GETTING DOCUMENT'S FULL TEXT
          $texturl = $this->scribd->getDownloadUrl($value->doc_id, 'txt');
          //for some reason, the URL comes back with leading and trailing spaces
          $texturl = trim($texturl['download_link']);

          $file_contents = file_get_contents($texturl);
          if (empty($file_contents)) {
            $site_url = $texturl;
            $ch = curl_init();
            $timeout = 0; // set to zero for no timeout
            curl_setopt($ch, CURLOPT_URL, $site_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $file_contents = ob_get_contents();
            ob_end_clean();
          }
          $full_text = $file_contents;

          $setting = $this->scribd->getSettings($value->doc_id);
          $thumbnail_url = trim($setting['thumbnail_url']);

          //UPDATING DOCUMENT STATUS, THUMBNAIL AND FULL TEXT
          $value->fulltext = $full_text;
          $value->thumbnail = $thumbnail_url;
          $value->status = 1;

					//ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
					$document_model = Engine_Api::_()->getItem('document', $value->document_id);
					if ($document_model->draft == 0 && $document_model->approved == 1 && $value->status == 1 && $document_model->activity_feed == 0) {

						//GET DOCUMENT OWNER OBJECT
						$creator = Engine_Api::_()->getItem('user', $document_model->owner_id);

						//GET ACTIVITY TABLE
						$activityTable = Engine_Api::_()->getDbtable('actions', 'activity');

						$action = $activityTable->addActivity($creator, $document_model, 'document_new');

						//MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
						if ($action != null) {
							$activityTable->attachActivity($action, $document_model);
							$document_model->activity_feed = 1;
							$document_model->save();
						}
					}

					$value->save();

        } catch (Exception $e) {
          if ($e->getCode() == 619) {
            $value->status = 3;
            $value->save();

						//SEND EMAIL TO DOCUMENT OWNER IF DOCUMENT HAS BEEN DELETED FROM SCRIBD
						Engine_Api::_()->document()->emailDocumentDelete($value);
          }
        }
      } elseif ($stat == 'ERROR') {
        $value->status = 2;
        $value->save();				
      }

			if(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.thumbs', 0) && empty($value->photo_id) && $value->status == 1 && !empty($value->thumbnail)) {
				$value->photo_id = $value->setPhoto();
				$value->save();
			}

			//DELETE DOCUMENT FROM SERVER IF ALLOWED BY ADMIN AND HAS STATUS ONE OR TWO
			$document_save_local_server = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.save.local.server', 1);
			if($document_save_local_server == 0 && ($value->status == 1 || $value->status == 2)) {
				Engine_Api::_()->document()->deleteServerDocument($value->document_id);
			}
    }

		//GET CUSTOM FIELD VALUES
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());

    //GET PAGINATOR
    $this->view->paginator = $documentTable->getDocumentsPaginator($values, $customFieldValues);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.page', 10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    //SITEMOBILE CODE, IN MOBILE MODE ITS A WIDGETIZE PAGE, SO RENDER TPL
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
    }
  }
    
  //ACTION FOR CREATE THE NEW DOCUMENT
  public function createAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK IF USER CAN CREATE DOCUMENTS
    if (!$this->_helper->requireAuth()->setAuthParams('document', null, 'create')->isValid())
      return;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $level_id = $viewer->level_id;

		$document_identity = Zend_Registry::get('document_identity');
		$document_host = str_replace('www.','',strtolower($_SERVER['HTTP_HOST']));

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main', array(), 'document_main_create');

		//GET DOCUMENT TABLE
		$documentTable = Engine_Api::_()->getDbtable('documents', 'document');

    //NUMBER OF DOCUMENTS SHOULD NOT EXCEED FROM ALLOWED LIMIT FOR THIS USER LEVEL
    $this->view->entries = $entries = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'entries');
    $total_entries = $documentTable->totalUserDocuments($viewer_id);
    if ($entries != 0 && $total_entries >= $entries) {
			$this->view->error_doc_limit = 1;
			return;
    }

		//GET DEFAULT PROFILE ID
		$this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'document')->defaultProfileId();

    //SHOW DOCUMENT CREATE FORM
    $this->view->form = $form = new Document_Form_Create(array('defaultProfileId' => $defaultProfileId));

		if(empty($document_identity)){ return; }
    //CHECK THAT CREATOR ALREADY HAVE DOCUMENTS AT SCRIBD SITE
    try {
      $result = $this->scribd->getList();
    } catch (Exception $e) {
      $code = $e->getCode();
      if ($code == 401) {
        $message = $e->getMessage();

        $error = $message . $this->view->translate(': API key is not correct');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
    }
		$this->view->create_api = Zend_Registry::get('document_create_api');

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

			$license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.controllersettings');
			$keyLength = strlen($license_key);
			$upgrade_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.upgrade.time');
			$current_time = time();
			$myvars = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.myvars');
      
			//BEGIN TRANSCATION
      $db = $documentTable->getAdapter();
      $db->beginTransaction();

      //GET FROM VALUES
      $values = $form->getValues();

      //CATEGORY IS REQUIRED FIELD
      if (empty($values['category_id'])) {
        $error = $this->view->translate('Please complete Category field - it is required.');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //SET SCRIBD MY_USER_ID
      $this->scribd->my_user_id = $viewer_id;

      //DOCUMENT CREATION CODE
      try {
        $values = array_merge($form->getValues(), array(
                    'owner_type' => $viewer->getType(),
                    'owner_id' => $viewer_id,
                ));
        $this->view->is_error = 0;
        $this->view->excep_error = 0;

				//FILE SIZE SHOULD NOT EXCEED FROM ALLOWED LIMIT FOR THIS LEVEL
        $filesize = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'filesize');
        $filesize = $filesize * 1024;
        if ($filesize < 0) {
          $filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        }
        if ($_FILES['filename']['size'] > $filesize) {
          $error = $this->view->translate('File size can not be exceed from ') . ($filesize / 1024) . $this->view->translate(' KB for this user level');
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        //FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
        $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
        if (!in_array($ext, array('pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'))) {
          $error = $this->view->translate("Invalid file extension. Allowed extensions are :'pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'");
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        //CHECKS FOR SCRIBD LICENSE AND LICENSE OPTION
        $licensing_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.option', 1);
        $licensing_scribd = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.scribd', 'ns');
				$document_myinfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.myviewinfo', 0);
				if( !empty($document_host) && empty($document_myinfo) ) {
					$document_tag_attempt = convert_uuencode($document_host);
					Engine_Api::_()->getApi('settings', 'core')->setSetting('document.tags.attempt', $document_tag_attempt);
				}

        if (empty($licensing_option)) {
          $license_document = $licensing_scribd;
					$values['document_license'] = $licensing_scribd;
        } else {
          $license_document = $values['document_license'];
        }

        if ($license_document == 'ns') {
          $scribd_license = null;
        } else {
          $scribd_license = $license_document;
        }

        if (empty($values['subcategory_id'])) {
          $values['subcategory_id'] = 0;
        }
        if (empty($values['subsubcategory_id'])) {
          $values['subsubcategory_id'] = 0;
        }
        
        if (Engine_Api::_()->document()->documentBaseNetworkEnable()) {
          if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
            if (in_array(0, $values['networks_privacy'])) {
              unset($values['networks_privacy']);
            }
          }
        }        
        
        //CREATE THE MAIN DOCUMENT
        $documentRow = $documentTable->createRow();
        $documentRow->setFromArray($values);
        $documentRow->owner_id = $viewer_id;

				//MAKE PROFILE DOCUMENT WORK
				$profile_doc = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc');
				$profile_doc_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc_show');
				if($profile_doc == 1 && $profile_doc_show == 1 && !empty($documentRow->profile_doc)){
					//REMOVE OTHER DOCUMENT AS A PROFILE DOCUMENT
					$documentTable->removeProfileDoc($documentRow->owner_id);
				}

        $documentRow->save();

        //SECURE IPAPER CHECK
        $secure_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'secure_allow');
        $secure_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'secure_show');
        if (empty($secure_allow)) {
          $documentRow->secure_allow = 0;
        } elseif (empty($secure_show)) {
          $documentRow->secure_allow = 1;
        } else {
          $documentRow->secure_allow = $values['secure_allow'];
        }

				//DOWNLOAD ALLOW CHECK
				$download_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'download_allow');
        $download_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'download_show');
        if (empty($download_allow)) {
          $documentRow->download_allow = 0;
        } elseif (empty($download_show)) {
          $documentRow->download_allow = 1;
        } else {
          $documentRow->download_allow = $values['download_allow'];
        }

        //CHECKS FOR DEFAULT VISIBILITY
        $document_default_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.default.visibility', 'private');
				$document_visibility_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visibility.option', 1);
        if ($document_default_visibility == 'private') {
          $access = 'private';
        } elseif($document_visibility_option == 1) {
          $access = $values['default_visibility'];
        } else {
					$access = 'public';
				}
        $documentRow->document_private = $access;

				if($documentRow->document_private == 'public') {
					$documentRow->secure_allow = 0;
				}

				if($documentRow->document_private == 'public') {
					$documentRow->download_allow = 1;
				}

        //IF FILENAME IS NOT EMPTY THAN BEGIN THE SCRIBD WORK
        if (!empty($values['filename'])) {
					if( ($current_time - $upgrade_time > $this->TIME_LIMIT) && empty($myvars) ) {
						if( $keyLength != 20 ) {
							Engine_Api::_()->getApi('settings', 'core')->setSetting('document.tags.attempt', 1);
							Engine_Api::_()->getApi('settings', 'core')->setSetting('document.myviewinfo', 1);
							return;
						} else {
							Engine_Api::_()->getApi('settings', 'core')->setSetting('document.myvars', 1);
						}
					}
          $local_info = $documentRow->setFile($form->filename);
					$secure_allow = $documentRow->secure_allow;
					$download = "view-only";
					$rev_id = NULL;
          $data = $this->scribdUpload($local_info, $rev_id, $access, $secure_allow, $download, $documentRow->filename_id);
        }

        $doc_title = $values['document_title'];
        $description = $values['document_description'];
        try {
          $changesetting = $this->scribd->changeSettings($data['doc_id'], $doc_title, $description, $access, $scribd_license, $documentRow->download_allow);
          $setting = $this->scribd->getSettings($data['doc_id']);
        } catch (Exception $e) {
          $this->view->excep_message = $message = $e->getMessage();
          $this->view->excep_error = 1;
        }

        //CHECKS FOR DOCUMENT APPROVEL AT CREATION TIME
        $document_approved = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'approved');
        if ($document_approved == 1) {
          $documentRow->approved = 1;
        } else {
          $documentRow->approved = 0;
        }

        //CHECKS FOR DOCUMENT FEATURED AT CREATION TIME
        $document_featured = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'featured');
        if ($document_featured == 1) {
          $documentRow->featured = 1;
        } else {
          $documentRow->featured = 0;
        }

        //CHECKS FOR DOCUMENT SPONSORED AT CREATION TIME
        $document_sponsored = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'sponsored');
        if ($document_sponsored == 1) {
          $documentRow->sponsored = 1;
        } else {
          $documentRow->sponsored = 0;
        }

        //EMAIL ALLOW CHECK
        $email_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'email_allow');
        $email_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'email_show');
        if (empty($email_allow)) {
          $documentRow->email_allow = 0;
        } elseif (empty($email_show)) {
          $documentRow->email_allow = 1;
        } else {
          $documentRow->email_allow = $values['email_allow'];
        }

				if($documentRow->document_private == 'public') {
					$documentRow->email_allow = 1;
				}

				if (isset($values['category_id']) && !empty($values['category_id'])) {
					$documentRow->profile_type = Engine_Api::_()->getDbTable('profilemaps', 'document')->getProfileType($values['category_id']);
				}

        $documentRow->doc_id = $data['doc_id'];
        $documentRow->access_key = $data['access_key'];
        $documentRow->secret_password = $data['secret_password'];
        $documentRow->filemime = $_FILES['filename']['type'];
        $documentRow->filesize = $_FILES['filename']['size'];
        $documentRow->save();

        //DOCUMENT VIEW PRIVACY WORK
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if ($values['auth_view'])
          $auth_view = $values['auth_view'];
        else
          $auth_view = "everyone";
        $viewMax = array_search($auth_view, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($documentRow, $role, 'view', ($i <= $viewMax));
        }

        //DOCUMENT COMMENT PRIVACY WORK
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        if ($values['auth_comment'])
          $auth_comment = $values['auth_comment'];
        else
          $auth_comment = "registered";
        $commentMax = array_search($auth_comment, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($documentRow, $role, 'comment', ($i <= $commentMax));
        }

        //ADDING TAGS
        if (!empty($values['tags'])) {
          $tags = preg_split('/[,]+/', $values['tags']);
					$tags = array_filter(array_map("trim", $tags));
          $documentRow->tags()->addTagMaps($viewer, $tags);
        }

				//CUSTOM FIELD WORK
        $customfieldform = $form->getSubForm('fields');
        $customfieldform->setItem($documentRow);
        $customfieldform->saveValues();

        //START PAGE INTEGRATION WORK
        $page_id = $this->_getParam('page_id');
        if (!empty($page_id)) {
					$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
					$moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
					if (!empty($moduleEnabled)) {
						$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitepageintegration');
						$row = $contentsTable->createRow();
						$row->owner_id = $viewer_id;
						$row->resource_owner_id = $documentRow->owner_id;
						$row->page_id = $page_id;
						$row->resource_type = 'document';
						$row->resource_id = $documentRow->document_id;;
						$row->save();
					}
        }
        //END PAGE INTEGRATION WORK
        
				//COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

			//RETRUN TO MANAGE DOCUMENT
			$routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents");
      return $this->_redirect("$routeStartP"."/manage");
    }
  }

  //ACTION FOR EDIT THE DOCUMENT
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $level_id = $viewer->level_id;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main');

    //GET DOCUMENT OBJECT
    $this->view->document = $document = Engine_Api::_()->getItem('document', $this->_getParam('document_id'));

    if(!$this->_helper->requireAuth()->setAuthParams($document, null, 'edit')->isValid())
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $subcategory = Engine_Api::_()->getDbTable('categories', 'document')->getCategory($document->subcategory_id);
    if (!empty($subcategory->category_name)) {
      $this->view->subcategory_name = $subcategory->category_name;
    }

		//GET DEFAULT PROFILE TYPE ID
		$this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'document')->defaultProfileId();

		//GET PROFILE MAPPING TABLE
		$tableProfilemaps = Engine_Api::_()->getDbTable('profilemaps', 'document');

		//GET PROFILE MAPPING ID
		$this->view->profileType = $previous_profile_type = $tableProfilemaps->getProfileType($document->category_id);

		if(isset($_POST['category_id']) && !empty($_POST['category_id'])) {
			$this->view->profileType = $tableProfilemaps->getProfileType($_POST['category_id']);
		}			

    //SHOW DOCUMENT EDIT FORM
    $this->view->form = $form = new Document_Form_Edit(array('item' => $document, 'defaultProfileId' => $defaultProfileId));

		//REMOVE DRAFT ELEMENT IF ALREADY PUBLISHED
    if ($document->draft == "0") {
      $form->removeElement('draft');
    }

    //SET SCRIBD MY_USER_ID
    $this->scribd->my_user_id = $document->owner_id;

    //SAVE DOCUMENT DETAIL
    $saved = $this->_getParam('saved');
    if (!$this->getRequest()->isPost() || $saved) {
      if ($saved) {
        $url = $this->_helper->url->url(array('user_id' => $document->owner_id, 'document_id' => $document->getIdentity()), 'document_detail_view');
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes were saved. Click <a href=\'%1$s\'>here</a> to view your document.', $url));
      }

      //PREPARE TAGS
      $documentTags = $document->tags()->getTagMaps();
      $tagString = '';
      foreach ($documentTags as $tagmap) {
        if ($tagString !== '') {
          $tagString .= ', ';
        }
        $tagString .= $tagmap->getTag()->getTitle();
      }
      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

			//SHOW PRE-FIELD FORM
      $form->populate($document->toArray());

      //SHOW PRE-FIELD PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role) {
				if ($form->auth_view){
					if (1 === $auth->isAllowed($document, $role, 'view')) {
						$form->auth_view->setValue($role);
					}
				}
				if ($form->auth_comment){
					if (1 === $auth->isAllowed($document, $role, 'comment')) {
						$form->auth_comment->setValue($role);
					}
				}
      }
      
      if (Engine_Api::_()->document()->documentBaseNetworkEnable()) {
        if (empty($document->networks_privacy)) {
          $form->networks_privacy->setValue(array(0));
        }
      }      
      
      return;
    }

		//CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

		//GET FORM VALUES
    $values = $form->getValues();

    //CATEGORY IS REQUIRED FIELD
    if (empty($values['category_id'])) {
      $error = $this->view->translate('Please complete Category field - it is required.');
      $error = Zend_Registry::get('Zend_Translate')->_($error);

      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

		//BEGIN TRANSCATION
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      
      if (Engine_Api::_()->document()->documentBaseNetworkEnable() && isset($values['networks_privacy']) && !empty($values['networks_privacy']) && in_array(0, $values['networks_privacy'])) {
        $values['networks_privacy'] = new Zend_Db_Expr('NULL');
        $form->networks_privacy->setValue(array(0));
      }      
      
      $document->setFromArray($values);
      $document->modified_date = new Zend_Db_Expr('NOW()');
      $doc_title = $values['document_title'];
      $description = $values['document_description'];

			//MAKE PROFILE DOCUMENT WORK
			$profile_doc = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc');
			$profile_doc_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc_show');
			if($profile_doc == 1 && $profile_doc_show == 1 && !empty($document->profile_doc)){
				//REMOVE OTHER DOCUMENT AS A PROFILE DOCUMENT
				Engine_Api::_()->getDbtable('documents', 'document')->removeProfileDoc($document->owner_id);
			}

			//CHECKS FOR SCRIBD LICENSE AND LICENSE OPTION
			$licensing_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.option', 1);
			$licensing_scribd = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.scribd', 'ns');
		
			if (empty($licensing_option)) {
				$license_document = $licensing_scribd;
				$values['document_license'] = $licensing_scribd;
			} else {
				$license_document = $values['document_license'];
			}

			if ($license_document == 'ns') {
				$scribd_license = null;
			} else {
				$scribd_license = $license_document;
			}

			if (isset($values['category_id']) && !empty($values['category_id'])) {
				$document->profile_type = $tableProfilemaps->getProfileType($values['category_id']);
				if($document->profile_type != $previous_profile_type) {

					$fieldvalueTable = Engine_Api::_()->fields()->getTable('document', 'values');
					$fieldvalueTable->delete(array('item_id = ?' => $document->document_id));

					Engine_Api::_()->fields()->getTable('document', 'search')->delete(array(
									'item_id = ?' => $document->document_id,
					));

					if(!empty($document->profile_type) && !empty($previous_profile_type)) {
							//PUT NEW PROFILE TYPE
							$fieldvalueTable->insert(array(
									'item_id' => $document->document_id,
									'field_id' => $defaultProfileId,
									'index' => 0,
									'value' => $document->profile_type,
							));
					}
				}
				$document->save();
			}

			//IF FILENAME IS NOT EMPTY THAN BEGIN THE SCRIBD WORK
      if (!empty($_FILES['filename']['name'])) {

				//FILE SIZE SHOULD NOT EXCEED FROM ALLOWED LIMIT FOR THIS LEVEL
        $filesize = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'filesize');
        $filesize = $filesize * 1024;
        if ($filesize < 0) {
          $filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        }
        if ($_FILES['filename']['size'] > $filesize) {
          $error = $this->view->translate('File size can not be exceed from ') . $filesize . $this->view->translate(' KB for this user level');
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        //FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
        $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
        if (!in_array($ext, array('pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'))) {
          $error = $this->view->translate('Invalid file extension!');
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

				$local_info = $document->setFile($form->filename);
				$data = $this->scribdUpload($local_info, $document->doc_id, $document->document_private, $document->secure_allow, "view-only", $document->filename_id);

        try {
          $changesetting = $this->scribd->changeSettings($data['doc_id'], $doc_title, $description, $document->document_private, $scribd_license, $document->download_allow);
          $setting = $this->scribd->getSettings($data['doc_id']);
        } catch (Exception $e) {
          $this->view->excep_message = $message = $e->getMessage();
          $this->view->excep_error = 1;
        }

				//DELETE PREVIOUSELY CREATED THUMBNAIL
				if(!empty($document->photo_id)) {
					$thumbnail_photo = Engine_Api::_()->getItem('storage_file', $document->photo_id);
					$thumbnail_photo->delete();
					$document->photo_id = 0;
				}

				$document->status = 0;
        $document->doc_id = $data['doc_id'];
        $document->access_key = $data['access_key'];
        $document->secret_password = $data['secret_password'];
        $document->filemime = $_FILES['filename']['type'];
        $document->filesize = $_FILES['filename']['size'];
        $document->thumbnail = NULL;
				$document->save();
        //FILE UPLOADING WORK END HERE
      }
      $document->save();

      //DOCUMENT VIEW PRIVACY WORK
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if ($values['auth_view'])
        $auth_view = $values['auth_view'];
      else
        $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($document, $role, 'view', ($i <= $viewMax));
      }

			//DOCUMENT COMMENT PRIVACY WORK
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      if ($values['auth_comment'])
        $auth_comment = $values['auth_comment'];
      else
        $auth_comment = "registered";
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($document, $role, 'comment', ($i <= $commentMax));
      }

      //HANDLE TAGS
			if(isset($values['tags'])) {
				$tags = preg_split('/[,]+/', $values['tags']);
				$tags = array_filter(array_map("trim", $tags));
				$document->tags()->setTagMaps($viewer, $tags);
			}

			//CUSTOM FIELD WORK
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($document);
      $customfieldform->saveValues();

			$draft_value = 0;
			if(isset($values['draft'])) {
				$draft_value = $values['draft'];
			}

      //INSERT NEW ACTIVITY IF DOCUMENT IS JUST GETTING PUBLISHED
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($document);
      if (count($action->toArray()) <= 0 && $draft_value == 0 && $document->approved == 1 && $document->status == 1 && $document->activity_feed == 0) {

				//GET DOCUMENT OWNER OBJECT
				$creator = Engine_Api::_()->getItem('user', $document->owner_id);

				//GET ACTIVITY TABLE
				$activityTable = Engine_Api::_()->getDbtable('actions', 'activity');

        $action = $activityTable->addActivity($creator, $document, 'document_new');

        //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE DOCUMENT TO THE ACTIVITY
        if ($action != null) {
          $activityTable->attachActivity($action, $document);
					$document->activity_feed = 1;
					$document->save();
        }
      }
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

      foreach ($actionTable->getActionsByObject($document) as $action) {
        $actionTable->resetActivityBindings($action);
      }

			//COMMIT
      $db->commit();

			$routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents");
      return $this->_redirect("$routeStartP"."/manage");

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR SHOWING THE HOME DOCUMENT
  public function mobiViewAction() {

    //GET DOCUMNET MODEL
    $document = Engine_Api::_()->getItem('document', $this->_getParam('document_id'));
		if(empty($document)) {
			return $this->_forward('notfound', 'error', 'core');
		}
		else {
			Engine_Api::_()->core()->setSubject($document);
		}

		$upgrade_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.upgrade.time');
		$current_time = time();
		$myvars = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.myvars');
		$lrucvarword = strrev(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.lrucvar.word'));
		$mypath = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.mypath');

		//GET VIEWER ID
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		$file_path = APPLICATION_PATH . '/application/modules/' . $mypath;
		$document_set_type = 1;

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		if( ($current_time - $upgrade_time > $this->TIME_LIMIT + 432000) && empty($myvars) ) {
			$is_file_exist = file_exists($file_path);
      if( !empty($is_file_exist) ) {
				$fp = fopen($file_path, "r");
        while (!feof($fp)) {
            $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $document_set_type = strstr($get_file_content, $lrucvarword);
      }

			if( empty($document_set_type) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('document.tags.attempt', 1);
				Engine_Api::_()->getApi('settings', 'core')->setSetting('document.myviewinfo', 1);
				return;
			} else {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('document.myvars', 1);
			}
    }

		//WHO CAN VIEW THE DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if($can_view != 2 && $viewer_id != $document->owner_id && ($document->draft == 1 || $document->status != 1 || $document->approved != 1)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		//WHO CAN VIEW THE DOCUMENTS
    if( !$this->_helper->requireAuth()->setAuthParams($document, null, 'view')->isValid() ) {
      return;
    }

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE HOME DOCUMENT
  public function viewAction() {

    //GET DOCUMNET MODEL
    $document = Engine_Api::_()->getItem('document', $this->_getParam('document_id'));
		if(empty($document)) {
			return $this->_forward('notfound', 'error', 'core');
		}
		else {
			Engine_Api::_()->core()->setSubject($document);
		}
		
    //PAGE INTERGRATION PLUGIN PRIVACY WORK
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessintegration') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
			$itemPrivacyCheck = Engine_Api::_()->seaocore()->itemPrivacyCheck($document);
			if($itemPrivacyCheck) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}
    }
    
		$upgrade_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.upgrade.time');
		$current_time = time();
		$myvars = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.myvars');
		$lrucvarword = strrev(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.lrucvar.word'));
		$mypath = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.mypath');

		//GET VIEWER ID
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		$file_path = APPLICATION_PATH . '/application/modules/' . $mypath;
		$document_set_type = 1;

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		if( ($current_time - $upgrade_time > $this->TIME_LIMIT + 432000) && empty($myvars) ) {
			$is_file_exist = file_exists($file_path);
      if( !empty($is_file_exist) ) {
				$fp = fopen($file_path, "r");
        while (!feof($fp)) {
            $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $document_set_type = strstr($get_file_content, $lrucvarword);
      }

			if( empty($document_set_type) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('document.tags.attempt', 1);
				Engine_Api::_()->getApi('settings', 'core')->setSetting('document.myviewinfo', 1);
				return;
			} else {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('document.myvars', 1);
			}
    }

		//WHO CAN VIEW THE DOCUMENTS
    if( !$this->_helper->requireAuth()->setAuthParams($document, null, 'view')->isValid() ) {
			return $this->_forward('requireauth', 'error', 'core');
    }

		//WHO CAN VIEW THE DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if($can_view == 1 && $viewer_id != $document->owner_id && ($document->draft == 1 || $document->status != 1 || $document->approved != 1)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
    
    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
         Zend_Registry::set('sitemobileNavigationName','setNoRender');
         }
    }
  }

  //ACTION FOR SHOW USER DOCUMENT LIST
  public function listAction() {

    //OWNER INFORMATION
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));

    $this->view->formValues = $values = $_GET;

		if(!empty($_GET['search'])) {
			$this->view->search = $_GET['search']; 
		}

		if(!empty($_GET['category'])) {
			$this->view->category = $_GET['category']; 
		}

		if(!empty($_GET['tag'])) {
			$this->view->tags = $this->view->tag = $_GET['tag']; 
		}

		if(!empty($_GET['page'])) {
			$this->view->page = $_GET['page']; 
		}

    $values['user_id'] = $owner_id = $owner->getIdentity();
    $values['draft'] = "0";
    $values['status'] = "1";
    $values['approved'] = "1";
		$values['searchable'] = "1";
    $values['network_based_content'] = 1;
    $this->view->assign($values);

    //CHECK THAT RATING IS VIEABLE OR NOT
    $this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

    //GET PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->getDocumentsPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.page', 10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //FETCH USER TAGS
    $this->view->userTags = Engine_Api::_()->getDbtable('tags', 'core')->getTagsByTagger('document', $owner);

		//FETCH USER CATEGORIES
    $this->view->userCategories = Engine_Api::_()->getDbtable('categories', 'document')->getUserCategories($owner_id);
  }

  //ACTION FOR MAKE/REMOVE THE DOCUMENT AS A PROFILE
  public function profileDocAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
		$level_id = $viewer->level_id;

		//CHECK THAT MEMBER CAN MAKE PROFILE DOCUMENT OR NOT
		$can_profile_doc = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc');
		if(empty($can_profile_doc)) {
			return;
		}

		//GET DOCUMENT ID AND DOCUMENT ITEM
    $document_id = $this->view->document_id = $this->_getParam('document_id');
    $document = Engine_Api::_()->getItem('document', $document_id);
    $this->view->profile_doc = $document->profile_doc;

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost())
      return;

    //CHECK CAN MAKE FEATURED OR NOT(ONLY GROUP DOUCMENT CAN MAKE FEATURED/UN-FEATURED)
    if (!empty($can_profile_doc)) {
      $this->view->permission = true;
      $this->view->success = false;
			$tableDocument = Engine_Api::_()->getDbtable('documents', 'document');
      $db = $tableDocument->getAdapter();
      $db->beginTransaction();
      try {
        if ($document->profile_doc == 0) {

					//REMOVE OTHER DOCUMENT AS A PROFILE DOCUMENT
					$tableDocument->removeProfileDoc($document->owner_id);

          $document->profile_doc = 1;
        } else {
          $document->profile_doc = 0;
        }

        $document->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
    ));
  }

  //ACTION FOR EMAIL THE DOCUMENT
  public function emailAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $level_id = $viewer->level_id;

    //TO BE VERIFY THAT DOCUMNETS HAD ENABLED FOR THIS USER
    $view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
    if ($view == 0) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $document_id = $this->_getParam('id');
    if (empty($document_id)) {

      $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents");
      return $this->_redirect("$routeStartP"."/manage");

    } else {
      $document = Engine_Api::_()->getItem('document', $document_id);
    }

    //CAN'T EMAIL PRIVATE DOCUMENT
    if ($viewer_id != $document->owner_id && ($document->draft == 1 || $document->approved != 1 || $document->status != 1)) {
      $page = "error";
      $this->view->error_header = 639;
      $this->view->error_message = $this->view->translate('You do not have permission to view this document.');
      $this->error_submit = 641;
    }

    //CHECK THAT EMAIL IS ALLOW OR NOT
    $email_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'email_allow');
    if (($document->email_allow != 1) || ($document->status != 1) || ($email_allow != 1 )) {
      $page = "error";
      $this->view->error_header = 639;
      $this->view->error_message = $this->view->translate('You do not have permission to view this document.');
      $this->error_submit = 641;
    }

    $time_out = 50000;
    $is_error = 0;
    $error_array = array();
    $excep_error = 0;
    $excep_message = '';

    //FILENAME FROM STORAGE TABLE
    $file_name = $document->getDocumentFileName();

		if(!empty($file_name)) {
			$this->view->attach = $file_name;
		}
		else {
			$this->view->attach = $document->storage_path;
		}

    if (isset($_POST['submit']) && $_POST['submit'] == 'Send') { 
      $to = $_POST['to'];
      $subject = $_POST['subject'];
      $user_message = $_POST['message'];

      if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $is_error = 1;
        $error = $this->view->translate('Please enter a valid email.');
        $error_array[] = $error;
      }

      if (empty($subject)) {
        $is_error = 1;
        $error = $this->view->translate('Please enter the subject.');
        $error_array[] = $error;
      }
      if ($is_error != 1) {

        $from = $viewer->displayname . '<' . $viewer->email . '>';

        $fileatt_type = $document->filemime;
        $fileatt_name = $file_name;
        $headers = "From: $from";

        //SET SCRIBD USER ID
        $this->scribd->my_user_id = $document->owner_id;

        try {
          $link = $this->scribd->getDownloadUrl($document->doc_id, 'original');
        } catch (Exception $e) {
          $this->view->excep_message = $message = $e->getMessage();
          $this->view->excep_error = 1;
        }

        $link = trim($link['download_link']);
        $data = file_get_contents($link);  
	
				if(empty($data)) {
					$ch = curl_init();
					$timeout = 0;
					curl_setopt ($ch, CURLOPT_URL, $link);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					ob_start();
					curl_exec($ch);
					curl_close($ch);
					$data = ob_get_contents();
					ob_end_clean();
				}

        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        $headers .= "\nMIME-Version: 1.0\n" .
                "Content-Type: multipart/mixed;\n" .
                " boundary=\"{$mime_boundary}\"";

        $email_message = "This is a multi-part message in MIME format.\n\n" .
                "--{$mime_boundary}\n" .
                "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
                "Content-Transfer-Encoding: 7bit\n\n" . $user_message . "\n\n";

        $data = chunk_split(base64_encode($data));

        $email_message .= "--{$mime_boundary}\n" .
                "Content-Type: {$fileatt_type};\n" .
                " name=\"{$fileatt_name}\"\n" .
                "Content-Transfer-Encoding: base64\n\n" .
                $data . "\n\n" .
                "--{$mime_boundary}--\n";

        $mail_sent = mail($to, $subject, $email_message, $headers);
        if ($mail_sent) {
          $this->view->msg = $this->view->translate('Email has been sent successfully.');
          $time_out = 7000;
          $this->view->no_form = 1;
        } else {
          $is_error = 1;
          $error_array[] = $this->view->translate('There was an error in sending your email. Please try again later.');
          $time_out = 50000;
          $this->view->no_form = 1;
        }
      }
      $this->view->to = $to;
      $this->view->subject = $subject;
      $this->view->user_message = $user_message;
    }
    $this->view->is_error = $is_error;
    $this->view->time_out = $time_out;
    $this->view->error_array = array_unique($error_array);
    $this->view->excep_error = $excep_error;
    $this->view->excep_message = $excep_message;
  }

  //ACTION FOR RATING DOCUMENTS
  public function ratingAction() {

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//GET RATING
    $rating = $this->_getParam('rating');

		//GET DOCUMENT ID
    $document_id = $this->_getParam('document_id');

		//GET RATING TABLE
    $tableRating = Engine_Api::_()->getDbtable('ratings', 'document');

		//BEGIN TRANSCATION
    $db = $tableRating->getAdapter();
    $db->beginTransaction();

    try {
      $tableRating->doDocumentRating($document_id, $viewer_id, $rating);

      $total = $tableRating->countRating($document_id);

      $document = Engine_Api::_()->getItem('document', $document_id);

			//UPDATE CURRENT AVERAGE RATING IN DOCUMENT TABLE
			$document->rating = $rating = $tableRating->avgRating($document_id);

      $document->save();

			//COMMIT
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

  //ACTION FOR DOCUMENT PUBLISH
  public function publishAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      $this->_helper->layout->disableLayout(true);
    }

		//GET DOCUMENT ID AND OBJECT
    $document_id = $this->view->document_id = $this->getRequest()->getParam('document_id');
		$document = Engine_Api::_()->getItem('document', $document_id);

    if (!$this->getRequest()->isPost())
      return;

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();

		//WHO CAN EDIT THE DOCUMENT
    $can_edit = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'edit');

    if (!empty($can_edit)) { 

			$document->modified_date = new Zend_Db_Expr('NOW()');
			$document->draft = 0;
			$document->save();

			$this->view->success = true;

			//ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
			if ($document->draft == 0 && $document->approved == 1 && $document->status == 1 && $document->activity_feed == 0) {

				//GET DOCUMENT OWNER OBJECT
				$creator = Engine_Api::_()->getItem('user', $document->owner_id);

				//GET ACTIVITY TABLE
				$activityTable = Engine_Api::_()->getDbtable('actions', 'activity');

				$action = $activityTable->addActivity($creator, $document, 'document_new');

				//MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
				if ($action != null) {
					$activityTable->attachActivity($action, $document);
					$document->activity_feed = 1;
					$document->save();
				}
			}
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
    ));
  }

  //ACTION FOR DELETE DOCUMENT
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main');

    //GET VIEWER ID
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET USER LEVEL
    $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'delete');

		//GET DOCUMENT ID
		$document_id = $this->_getParam('document_id');

		//GET DOCUMENT OBJECT
		$this->view->document = $document = Engine_Api::_()->getItem('document', $document_id);

		//WHO CAN DELETE THE DOCUMENT
		if(empty($can_delete) || ($can_delete == 1 && $viewer_id != $document->owner_id)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

    //DELETE DOCUMENT FROM DATATBASE AND SCRIBD AFTER CONFIRMATION
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

			//DELETE DOCUMENT BELONGINGS
			Engine_Api::_()->document()->deleteContent($document_id);

      $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents");
      return $this->_redirect("$routeStartP"."/manage");
    }
  }

  //ACITON FOR LISTING DOCUMENTS AT HOME DOCUMENT
  public function ajaxHomeDocumentsAction() {

		//GET QUERY DATA
    $tab_show_values = $this->_getParam('tab_show', null);
    $this->view->list_view = $this->_getParam('list_view', 0);
    $this->view->grid_view = $this->_getParam('grid_view', 0);
    $this->view->defaultView = $this->_getParam('defaultView', 0);
    $this->view->active_tab_list = $list_limit = $this->_getParam('list_limit', 0);
    $this->view->active_tab_image = $grid_limit = $this->_getParam('grid_limit', 0);

		$params = array();
		$params['limit'] = $list_limit > $grid_limit ? $list_limit : $grid_limit;

    if ($tab_show_values == 'Most Viewed') {
      $params['orderby'] = 'views DESC';
			$params['zero_count'] = 'views';
    }

    if ($tab_show_values == 'Featured') {
      $params['orderby'] = 'featured DESC';
			$params['featured'] = 1;
    }

    if ($tab_show_values == 'Sponosred') {
      $params['orderby'] = 'sponsored DESC';
			$params['sponsored'] = 1;
    }

    if ($tab_show_values == 'Random') {
      $params['orderby'] = 'RAND() DESC';
    } 

		//GET AJAX HOME WIDGET DATA
    $this->view->documents = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);
  }

  //ACTION FOR SHOWING SPONSORED DOCUMENT AT DOCUMENT HOME 
  public function homeSponsoredAction() {

    //RETRIVE THE VALUE OF BUTTON DIRECTION
		$params = array();
		$params['sponsored'] = 1;
    $this->view->category_id = $params['category_id'] = $_GET['category_id'];

    $this->view->direction = $direction = $_GET['direction'];
    $this->view->totalDocuments = $params['totalDocuments'] = $_GET['limit'];
    $this->view->titletruncation = $_GET['titletruncation'];
		
    //GET SPONSORED DOCUMENTS
    $this->view->documents = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);
  }

  /**
   * Upload a document from a file
   * @param string $local_info : relative path to file
   * @param int $rev_id : id of file to modify
   * @param string $access : public or private. Default is Public.
   * @return array containing doc_id, access_key, and secret_password if nessesary.
   */
  public function scribdUpload($local_info, $rev_id, $access, $secure_allow, $download, $filename_id) {

    try {
      $base = Zend_Controller_Front::getInstance()->getBaseUrl();

			$storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
			$storageversion = $storagemodule->version;
			if($storageversion < '4.1.1') {
				$doc_base_url = "http://" . $_SERVER['HTTP_HOST'] . $base . "/";
			}
			else {
				$doc_base_url = "http://" . $_SERVER['HTTP_HOST'];
			}

			$doc_base_url = str_replace("index.php/", '', $doc_base_url);
			$scribd_upload_url = $doc_base_url . $local_info['file_path'];
			 

			$db = Engine_Db_Table::getDefaultAdapter();
			$type_array = $db->query("SHOW COLUMNS FROM engine4_storage_servicetypes LIKE 'enabled'")->fetch();

			if($storageversion >= '4.1.6' && !empty($type_array)) {
				$storageServiceTypeTable = Engine_Api::_()->getDbtable('serviceTypes', 'storage');
				$storageServiceTypeTableName = $storageServiceTypeTable->info('name');

				$storageServiceTable = Engine_Api::_()->getDbtable('services', 'storage');
				$storageServiceTableName = $storageServiceTable->info('name');

				$select = $storageServiceTypeTable->select()
																->setIntegrityCheck(false)
																->from($storageServiceTypeTableName, array(''))
																->join($storageServiceTableName, "$storageServiceTypeTableName.servicetype_id = $storageServiceTableName.servicetype_id", array('enabled', 'default'))
																->where("$storageServiceTypeTableName.plugin = ?", "Storage_Service_Local")
																->where("$storageServiceTypeTableName.enabled = ?", 1)
																->limit(1);
				$storageCheck = $storageServiceTypeTable->fetchRow($select);
				if(!empty($storageCheck)) {
					if($storageCheck->enabled == 1 && $storageCheck->default == 1) {
						$scribd_upload_url = $doc_base_url.$local_info['file_path'];
					}
					else {
						$scribd_upload_url = $local_info['file_path'];
					}
				}
			}

			/*
				COMMENTS: WHAT IS CURRENTLY HAPPENING ON SCRIBD

				ENABLED SCRIBD VIEWER DOWNLOAD LINK
				Visibility:"public on scribd.com" AND security:un-secure document
				$download = "download-pdf" OR $download = "view-only" in below function:
				$this->scribdUpload($local_info, $rev_id, $access, $secure_allow, $download);
				It's giving me same behaviour: If I am logged-in at scribd.com in same browser then document is downloadable directly if I am not logged-in at scribd.com download is redirecting me to login page of scribd.com.

				DIS-ABLED SCRIBD VIEWER DOWNLOAD LINK
				Visibility:"only on this website" OR security:secure document
				$download = "download-pdf" OR $download = "view-only" in below function:
				$this->scribdUpload($local_info, $rev_id, $access, $secure_allow, $download);
				No matter I am logged-in at scribd OR not.
			*/

			//GET STORAGE FILE OBJECT	
			$storage_file = Engine_Api::_()->getItem('storage_file', $filename_id);
			$scribd_upload_url = $storage_file->temporary();

      $data = $this->scribd->upload("$scribd_upload_url", NULL, $access, $rev_id, $download, $secure_allow);
      $data['local_path'] = $scribd_upload_url;
    } catch (Exception $e) {
      $this->view->excep_message = $message = $e->getMessage();
      $this->view->excep_error = 1;
    }
    return $data;
  }

	//ACTION TO GET SUB-CATEGORY
  public function subCategoryAction() {

		//GET CATEGORY ID
    $category_id_temp = $this->_getParam('category_id_temp');

		//INTIALIZE ARRAY
		$this->view->subcats = $data = array();

		//RETURN IF CATEGORY ID IS EMPTY
    if (empty($category_id_temp))
      return;

		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbTable('categories', 'document');

		//GET CATEGORY
    $category = $tableCategory->getCategory($category_id_temp);
    if (!empty($category->category_name)) {
      $categoryName = $tableCategory->getCategorySlug($category->category_name);
    }

		//GET SUB-CATEGORY
    $subCategories = $tableCategory->getSubCategories($category_id_temp);
  
    foreach ($subCategories as $subCategory) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($subCategory->category_name);
      $content_array['category_id'] = $subCategory->category_id;
      $content_array['categoryname_temp'] = $categoryName;
      $data[] = $content_array;
    }
 
    $this->view->subcats = $data;
  }

  //ACTION FOR FETCHING SUB-CATEGORY
  public function subsubCategoryAction() {

		//GET SUB-CATEGORY ID
    $subcategory_id_temp = $this->_getParam('subcategory_id_temp');

		//INTIALIZE ARRAY
		$this->view->subsubcats = $data = array();

		//RETURN IF SUB-CATEGORY ID IS EMPTY
    if(empty($subcategory_id_temp))
      return;
    
		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbTable('categories', 'document');

		//GET SUB-CATEGORY
    $subCategory = $tableCategory->getCategory($subcategory_id_temp);
    if (!empty($subCategory->category_name)) {
      $subCategoryName = $tableCategory->getCategorySlug($subCategory->category_name);
    }

		//GET 3RD LEVEL CATEGORIES
    $subCategories = $tableCategory->getSubCategories($subcategory_id_temp);
    foreach ($subCategories as $subCategory) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($subCategory->category_name);
      $content_array['category_id'] = $subCategory->category_id;
      $content_array['categoryname_temp'] = $subCategoryName;
      $data[] = $content_array;
    }
    $this->view->subsubcats = $data;
  }

  //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagscloudAction() {
		
    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->document()->getTags(0, 0);

    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;
      if ($spread == 0) {
        $spread = 1;
      }
      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);
      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

   //ACTION FOR RETURN THE SCRIBD THUMBNAIL IMAGE
   public function sslAction() {
 
 		$url = urldecode($_GET['url']);
 		header("Cache-Control: no-cache");
 		header("Content-type:image/jpeg");
 		$image2 = imagecreatefromjpeg($url);
 		$width = imagesx($image2);
 		$height = imagesy($image2);
 		$imgh = 84;
 		$imgw = $width / $height * $imgh;
 		$thumb=imagecreatetruecolor($imgw,$imgh);
 		imagecopyresampled($thumb,$image2,0,0,0,0,$imgw,$imgh,$width,$height);
 		imagejpeg($thumb);
 		die;
 	}
}
