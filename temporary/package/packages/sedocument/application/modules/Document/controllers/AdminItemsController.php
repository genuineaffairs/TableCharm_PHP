<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminItemsController.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_AdminItemsController extends Core_Controller_Action_Admin {

	//ACTION FOR DOCUMENT OF THE DAY
  public function dayAction() {

		//TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('document_admin_main', array(), 'document_admin_main_dayitem');

		//FORM GENERATION
    $this->view->formFilter = $formFilter = new Document_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1); 

    $values = array(); 
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null == $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'start_date',
                'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

		//FETCH DATA
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->getItemOfDayList($values, 'document_id', 'document');
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator->setCurrentPageNumber($page);
  }

	//ACTION FOR ADDING DOCUMENT OF THE DAY
  public function addItemAction() {

		//SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Document_Form_Admin_Items_Item();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

			//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        
        $table = Engine_Api::_()->getDbtable('itemofthedays', 'document');
        $select = $table->select()->where('document_id = ?', $values["document_id"]);
        $row = $table->fetchRow($select);

        if (empty($row)) {
          $row = $table->createRow();
          $row->document_id = $values["document_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Document of the Day has been added successfully.'))
      ));
    }
  }

	//ACTION FOR DOCUMENT SUGGESTION DROP-DOWN
  public function getItemAction() {

		//GET SEARCHABLE TEXT AND CONTENT LIMIT
		$search_text = $this->_getParam('text', null);
		$limit = $this->_getParam('limit', 40);
		
    $data = array();

		//GET CONTENT
		$moduleContents = Engine_Api::_()->getItemTable('document')->getDayItems($search_text, $limit=10);
    foreach ($moduleContents as $moduleContent) {
		
			if(!empty($moduleContent->photo_id)) {
				$content_photo = $this->view->htmlLink($moduleContent->getHref(), $this->itemPhoto($moduleContent, 'thumb.icon'));
			}
			else {
				$content_photo = $this->view->htmlLink($moduleContent->getHref(), '<img height = "50px", src="'. Engine_Api::_()->document()->sslThumbnail($moduleContent->thumbnail) .'" class="thumb_icon" />');
			}

      $data[] = array(
              'id' => $moduleContent->document_id,
              'label' => $moduleContent->document_title,
              'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

	//ACTION FOR EDIT BLOCK IP
  public function editItemAction()
  {
		//SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

		//FORM GENERATION
    $this->view->form = $form = new Document_Form_Admin_Items_Edit();

		//REMOVE TITLE ELEMENT
		$form->removeElement('title');

 		//GET BLOCK IP ID AND CHECK VALIDATION
		$item_id = $this->_getParam('id');
    if(empty($item_id)) {
    	die('No identifier specified');
    }
 
 		//GET BLOCK IP ID OBJECT
		$item = Engine_Api::_()->getItem('document_itemofthedays', $item_id);

		$form->starttime->setValue($item->start_date);
		$form->endtime->setValue($item->end_date);

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			//GET FORM VALUES
      $values = $form->getValues();

			//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
				
				//SAVE VALUES IN DATABASE
        $item->start_date = $values['starttime'];
        $item->end_date = $values['endtime'];
        $item->save();

				//COMMIT
        $db->commit();
      }

      catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

		//RENDER SCRIPT
    $this->renderScript('admin-items/edit-item.tpl');
  }

	//ACTION FOR DOCUMENT DELETE ENTRY
  public function deleteItemAction() {

		//FORM POST
    if ($this->getRequest()->isPost()) {

			//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

				//DELETE ENTRY
        Engine_Api::_()->getDbtable('itemofthedays', 'document')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-items/delete.tpl');
  }

  //ACTION FOR MULTI DELETE DOCUMENT ENTRIES
  public function multiDeleteAction() {

		//FORM POST
    if ($this->getRequest()->isPost()) {

			//GET ITEM IDS
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $documentitemofthedays = Engine_Api::_()->getItem('document_itemofthedays', (int) $value);
          if (!empty($documentitemofthedays)) {
            $documentitemofthedays->delete();
          }
        }
      }
    }
		return $this->_helper->redirector->gotoRoute(array('action' => 'day'));
  }
}