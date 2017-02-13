<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Document/Api/Scribd.php';
class Document_Widget_BrowseDocumentsController extends Seaocore_Content_Widget_Abstract
{
  public function indexAction()
  { 
		//GET VIEWER ID
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//WHO CAN VIEW THE DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if(empty($can_view)) {
			return $this->setNoRender();
		}

    //CHECK THAT VIEWER CAN CREATE NEW DOCUMENT
		$this->view->can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'create');

		//CHECK THAT RATING IS VIEABLE OR NOT
		$this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

		//FORM GENERATION
		$form = new Document_Form_Search();

//		if( $form->isValid($this->_getAllParams()) ) {
			if(!empty($_GET)) {
				$form->populate($_GET);
			}
			$values = $form->getValues();
//    } else {
//      $values = array();
//    }

		$values = array_merge($_GET, $values);

    $this->view->formValues = $values ;

    if(empty($_GET['page'])) {
    	$values['page'] = 1;
    }

		//GET USER IDS FOR SHOW DOCUMENT
    if( @$values['show'] == 2 ) {
      //FETCHING FRIENDS ID ARRAY
      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);

      $ids = array();
      foreach( $friends as $friend ) {
        $ids[] = $friend->user_id;
      }
      $values['users'] = $ids;
      
      if(empty($values['users']) && $values['show'] == 2) {
      	$values['owner_id'] = "0";
      }
    }

    if (isset($form->show) && $form->show->getValue() == 3 && !isset($_GET['show'])) {
      $values['show'] = 3;
    }    

    $values['draft'] = "0";
    $values['status'] = "1";
    $values['approved'] = "1";
    
//    ADD CODE FOR MOBILE SITE CATEGORY AND TAG LINK REDIRECTION USING GET PARAMS METHOD
  $values['tag'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null);
    if (!empty($values['tag']))
      $_GET['tag'] = $values['tag'];

    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $tag = $_GET['tag'];
      $_GET['tag'] = $tag;
    }
    //CATEGORY PARAMETER
   $values['category'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);
    if (!empty($values['category']))
      $_GET['category'] = $values['category'];

    if (isset($_GET['category']) && !empty($_GET['category'])) {
      $category = $_GET['category'];
      $_GET['category'] = $category;
    }
    
   $values['category_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    if (!empty($values['category_id']))
      $_GET['category_id'] = $values['category_id'];

    if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
      $category_id = $_GET['category_id'];
      $_GET['category_id'] = $category_id;
    }
    
     //SUB-CATEGORY PARAMETER
   $values['subcategory'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);
    if (!empty($values['subcategory']))
      $_GET['subcategory'] = $values['subcategory'];

    if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
      $category = $_GET['subcategory'];
      $_GET['subcategory'] = $category;
    }
    
   $values['subcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    if (!empty($values['subcategory_id']))
      $_GET['subcategory_id'] = $values['subcategory_id'];

    if (isset($_GET['subcategory_id']) && !empty($_GET['subcategory_id'])) {
      $subcategory_id = $_GET['subcategory_id'];
      $_GET['subcategory_id'] = $subcategory_id;
    }
    
     //SUB-SUB-CATEGORY PARAMETER
   $values['subsubcategory'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);
    if (!empty($values['subsubcategory']))
      $_GET['subsubcategory'] = $values['subsubcategory'];

    if (isset($_GET['subsubcategory']) && !empty($_GET['subsubcategory'])) {
      $subsubcategory = $_GET['subsubcategory'];
      $_GET['subsubcategory'] = $subsubcategory;
    }
    
   $values['subsubcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    if (!empty($values['subsubcategory_id']))
      $_GET['subsubcategory_id'] = $values['subsubcategory_id'];

    if (isset($_GET['subsubcategory_id']) && !empty($_GET['subsubcategory_id'])) {
      $subsubcategory_id = $_GET['subsubcategory_id'];
      $_GET['subsubcategory_id'] = $subsubcategory_id;
    }
    //END
    
		$values['searchable'] = "1";
		if(!isset($_GET['orderby'])) {
			$values['orderby'] = $this->_getParam('orderby', 'document_id');
		}
		elseif(isset($_GET['orderby']) && !empty($_GET['orderby'])) {
			$values['orderby'] = $_GET['orderby'];
		}
    $this->view->assign($values);

    //BLOCK FOR UPDATING CONVERSION STATUS OF THE DOCUMENT
		$scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->document_api_key;
		$scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
		$browse_api = Zend_Registry::get('document_browse_api');
		$scribd = new Scribd($scribd_api_key, $scribd_secret);    

		//GET DOCUMENT TABLE
    $tableDocument = Engine_Api::_()->getDbtable('documents', 'document');
		
		//GET DOCUMENTS FOR CONVERSION
		$doc_forUpdate = $tableDocument->updateDocs(0);

		foreach($doc_forUpdate as $value) {

			if (empty($value->doc_id)) {
				continue;
			}
			if( empty($browse_api) ){
				return $this->setNoRender();
			}
			$scribd->my_user_id = $value->owner_id; 
  
			try {
				$stat = trim($scribd->getConversionStatus($value->doc_id));
			}
			catch(Exception $e) {
				$message = $e->getMessage();
			} 
  
			if($stat == 'DONE') {  	
				try {
					//GETTING DOCUMENT'S FULL TEXT
					$texturl = $scribd->getDownloadUrl($value->doc_id, 'txt');
					//for some reason, the URL comes back with leading and trailing spaces
					$texturl = trim($texturl['download_link']);
			
					$file_contents = file_get_contents($texturl);
					if (empty($file_contents)) {
						$site_url = $texturl;
						$ch = curl_init();
						$timeout = 0; // set to zero for no timeout
						curl_setopt ($ch, CURLOPT_URL, $site_url);
						curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				
						ob_start();
						curl_exec($ch);
						curl_close($ch);
						$file_contents = ob_get_contents();
						ob_end_clean();
					}
					$full_text = $file_contents;
			
					$setting = $scribd->getSettings($value->doc_id);
					$thumbnail_url = trim($setting['thumbnail_url']);
			
					//UPDATING DOCUMENT STATUS AND FULL TEXT
					$value->fulltext = $full_text;
					$value->thumbnail = $thumbnail_url;
					$value->status = 1;
					
					//ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
					$document_model = Engine_Api::_()->getItem('document', $value->document_id);
					if ($document_model->draft == 0 && $document_model->approved == 1 && $value->status == 1 && $document_model->activity_feed == 0) {

						//GET DOCUMENT OWNER OBJECT
						$creator = Engine_Api::_()->getItem('user', $document->owner_id);

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
				}
				catch(Exception $e) {
					if($e->getCode() == 619) {
						$value->status = 3;
						$value->save();

						//SEND EMAIL TO DOCUMENT OWNER IF DOCUMENT HAS BEEN DELETED FROM SCRIBD
						Engine_Api::_()->document()->emailDocumentDelete($value);
					}
				}
			}
			elseif ($stat == 'ERROR') {
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
		
		$customFieldValues = array_intersect_key($values, $form->getFieldElements());
		$this->view->current_api = Zend_Registry::get('document_current_api');

		if(!empty($values['category_id'])) {$this->view->category = $values['category_id'];} else { $this->view->category = $values['category']; }
    
    $values['network_based_content'] = 1;
		
		//GET PAGINATOR
		$this->view->paginator = $tableDocument->getDocumentsPaginator($values, $customFieldValues);
		$total_documents = $this->_getParam('itemCount', 10);
		$this->view->paginator->setItemCountPerPage($total_documents);
		$this->view->paginator->setCurrentPageNumber($values['page']);
	}  		
}
