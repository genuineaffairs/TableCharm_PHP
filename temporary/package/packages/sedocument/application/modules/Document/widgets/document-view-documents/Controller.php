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
class Document_Widget_DocumentViewDocumentsController extends Seaocore_Content_Widget_Abstract
{
  public function indexAction()
  {
		//DON'T RENDER IF SUBJECT IS NOT SET
		if(!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}

		//GET DOCUMENT SUBJECT
		$this->view->document = $document = Engine_Api::_()->core()->getSubject();
		if(empty($document)) {
			return $this->setNoRender();
		}

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//GET WIDGET SETTINGS
		$this->view->documentViewerHeight = $this->_getParam('documentViewerHeight', 600);
		$this->view->documentViewerWidth = $this->_getParam('documentViewerWidth', 730);

    //SET SCRIBD API AND SCECRET KEY
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->document_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $scribd = new Scribd($scribd_api_key, $scribd_secret);

		//INCREMENT DOCUMENT VIEWS IF VIEWER IS NOT OWNER
		if (!$document->getOwner()->isSelf($viewer)) {
			$document->views++;
		}

    //CHECK THAT VIEWER CAN RATE THE DOCUMENT OR NOT
    $this->view->can_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

    //WHICH TYPE OF DOCUMENT READER WE HAVE TO SHOW TO USER
    $this->view->document_viewer = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.viewer', 1);

    //GET CATEGORY TABLE
		$this->view->categoryTable = $categoryTable = Engine_Api::_()->getDbtable('categories', 'document');

		//GET CATEGORIES DETAIL
		$this->view->category_name = $this->view->subcategory_name = $this->view->subsubcategory_name = '';
    $categoriesNmae = $categoryTable->getCategory($document->category_id);
    if (!empty($categoriesNmae->category_name)) {
      $this->view->category_name = $categoriesNmae->category_name;
    }
    $subcategory_name = $categoryTable->getCategory($document->subcategory_id);
    if (!empty($subcategory_name->category_name)) {
      $this->view->subcategory_name = $subcategory_name->category_name;
    }
    //GET SUB-SUB-CATEGORY
    $subsubcategory_name = $categoryTable->getCategory($document->subsubcategory_id);
    if (!empty($subsubcategory_name->category_name)) {
      $this->view->subsubcategory_name = $subsubcategory_name->category_name;
    }

    //SET SCRIBD USER ID
    $scribd->my_user_id = $document->owner_id;

		$this->view->doc_full_text = '';
		$document_include_full_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.include.full.text', 1);
		if(!empty($viewer_id)) {
			if ($document->download_allow) {
				if($document_include_full_text == 1) {
					$this->view->doc_full_text = $document->fulltext;
				}
			}
		}
		elseif($document->status == 1 && $document->download_allow) {
			$document_visitor_fulltext = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visitor.fulltext', 1);
			if ($document_include_full_text == 1 && $document_visitor_fulltext == 1) {
				$this->view->doc_full_text = $document->fulltext;
			}
		}

		$stat = null;
		if(!empty($document->doc_id)) {
			try {
				$stat = trim($scribd->getConversionStatus($document->doc_id));
			} catch (Exception $e) {
				$this->view->excep_message = $message = $e->getMessage();
				$this->view->excep_error = 1;
			}
		}

    if ($stat == 'DONE') {
      try {
        //GETTING DOCUMENT'S FULL TEXT
        $texturl = $scribd->getDownloadUrl($document->doc_id, 'txt');
        if ($document->status != 1) {
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

          $setting = $scribd->getSettings($document->doc_id);
          $thumbnail_url = trim($setting['thumbnail_url']);

          //UPDATING DOCUMENT STATUS AND FULL TEXT
          $document->fulltext = $full_text;
          $document->thumbnail = $thumbnail_url;
          $document->status = 1;

					//ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
					if ($document->draft == 0 && $document->approved == 1 && $document->status == 1 && $document->activity_feed == 0) {

						//GET DOCUMENT OWNER OBJECT
						$creator = Engine_Api::_()->getItem('user', $document->owner_id);

						//GET ACTIVITY TABLE
						$activityTable = Engine_Api::_()->getDbtable('actions', 'activity');

						$action = $activityTable->addActivity($creator, $document, 'document_new');

						//MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
						if($action != null) {
							$activityTable->attachActivity($action, $document);
							$document->activity_feed = 1;
							$document->save();
						}
					}
        }
      } catch (Exception $e) {
        if ($document->status != 3 && $e->getCode() == 619) {
          $document->status = 3;
					$document->save();

				//SEND EMAIL TO DOCUMENT OWNER IF DOCUMENT HAS BEEN DELETED FROM SCRIBD
				Engine_Api::_()->document()->emailDocumentDelete($document);
        }
      }
      $document->save();
    } elseif ($stat == 'ERROR') {
      if ($document->status != 2) {
        $document->status = 2;
        $document->save();			
      }
    } else {
      $document->save();
    }

		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.thumbs', 0) && empty($document->photo_id) && $document->status == 1 && !empty($document->thumbnail)) {
			$document->photo_id = $document->setPhoto();
			$document->save();
		}

		//DELETE DOCUMENT FROM SERVER IF ALLOWED BY ADMIN AND HAS STATUS ONE OR TWO
		$document_save_local_server = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.save.local.server', 1);
		if($document_save_local_server == 0 && ($document->status == 1 || $document->status == 2)) {
			Engine_Api::_()->document()->deleteServerDocument($document->document_id);
		}

    //SETTING PARAMETERS FOR SECURE DOCUMENT
    if ($viewer_id == 0) {
      $uid = mt_rand(1000, 100000);
    } else {
      $uid = $viewer_id;
    }
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $sessionId = session_id();
    $signature = md5($scribd_secret . 'document_id' . $document->doc_id . 'session_id' . $sessionId . 'user_identifier' . $uid);
    $this->view->uid = $uid;
    $this->view->sessionId = $sessionId;
    $this->view->signature = $signature;
		
    //FIND DOCUMENT OWNER TAGS
    $this->view->documentTags = $document->tags()->getTagMaps();

		//SAVE THUMBNAILS ON SITE SERVER
		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.thumbs', 0)) {
			Engine_Api::_()->getDbtable('documents', 'document')->updateThumbs();
		}

		//GET RATING TABLE
		$tableRating = Engine_Api::_()->getDbTable('ratings', 'document');
    $this->view->rating_count = $tableRating->countRating($document->getIdentity());
    $this->view->document_rated = $tableRating->previousRated($document->getIdentity(), $viewer_id);

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($document);
  }
}
?>
