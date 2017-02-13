<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';
class Sitepagedocument_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitepagedocument_Form_Admin_Global') {

        }
        return true;
    }
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
		if( $this->getRequest()->isPost() ) {
			$sitepageKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', null);
			if( !empty($sitepageKeyVeri) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.lsettings', trim($sitepageKeyVeri));
			}
			if( $_POST['sitepagedocument_lsettings'] ) {
				$_POST['sitepagedocument_lsettings'] = trim($_POST['sitepagedocument_lsettings']);
			}

			if( isset($_POST['sitepagedocument_api_key']) && !empty($_POST['sitepagedocument_api_key']) ) {
				$_POST['sitepagedocument_api_key'] = trim($_POST['sitepagedocument_api_key']);
			}

			if( isset($_POST['sitepagedocument_secret_key']) && !empty($_POST['sitepagedocument_secret_key']) ) {
				$_POST['sitepagedocument_secret_key'] = trim($_POST['sitepagedocument_secret_key']);
			}
		}
    include_once(APPLICATION_PATH . "/application/modules/Sitepagedocument/controllers/license/license1.php");

		//_SITEPAGEDOCUMENT_VISIBILITY_SECURE_IPAPER_MESSAGE
		//WE ARE SHOWING THIS MESSAGE BECAUSE 'Make iPaper Page documents secure. Do not allow embedding on other sites.' AND 'Public on Scribd.com' BOTH SETTINGS CAN'T COME TOGATHER. BOTH CONFLICTING STATEMENT. 
  }

  //CHECK SCRIBD API AND SECERET KEY
  public function checkscribd($values, $is_active) {

		//GET SCRIBD DETAIL
    $api_key = $values['sitepagedocument_api_key'];
    $secreat_key = $values['sitepagedocument_secret_key'];

    $this->scribdsitepage = new Scribdsitepage($api_key, $secreat_key);

    try {
      $result = $this->scribdsitepage->getList();
      if (empty($is_active)) {
        return;
      }
    } catch (Exception $e) {
      $is_error = 1;
      $code = $e->getCode();
      if ($code == 401) {
        $message = $e->getMessage();
        $error = $message . $this->view->translate(': Api key is not correct');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        return $error;
      }
    }

		//CHECK FOR VALID FILE SIZE
    if (!empty($is_active)) {
      $is_error = 0;
      $filesize = (int) ini_get('upload_max_filesize') * 1024;
      $show_description = Zend_Registry::get('Zend_Translate')->_("Please enter the valid Page document file size. Valid values are from 1 KB to $filesize KB.");
      if (!is_numeric($values['sitepagedocument_filesize']) || $values['sitepagedocument_filesize'] < 1 || $values['sitepagedocument_filesize'] > $filesize) {
        $is_error = 1;
      }

      if ($is_error == 1) {
        $error = $message . $this->view->translate("$show_description");
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        return $error;
      }
    }
  }

  //ACTION FOR WIDGET SETTINGS
  public function widgetAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagedocument_admin_main', array(), 'sitepagedocument_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagedocument_admin_submain', array(), 'sitepagedocument_admin_submain_document_tab');
    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepagedocument', 'type' => 'documents'));
  } 

   //ACTION FOR DOCUMENT OF THE DAY
  public function manageDayItemsAction() {

		//TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagedocument_admin_main', array(), 'sitepagedocument_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagedocument_admin_submain', array(), 'sitepagedocument_admin_submain_dayitems');
   
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepagedocument_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $this->view->documentOfDaysList = $documentOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->getItemOfDayList($values, 'document_id', 'sitepagedocument_document');
    $documentOfDay->setItemCountPerPage(50);
    $documentOfDay->setCurrentPageNumber($page);
  }

  //ACTION FOR ADDING DOCUMENT OF THE DAY
  public function addDocumentOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepagedocument_Form_Admin_ItemOfDayday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setTitle('Add an Document of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Document from the auto-suggest Document field. The selected Document will be displayed as "Document of the Day" for this duration and if more than one documents are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Document Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitepagedocument_document');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepagedocument_document';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Document of the Day has been added successfully.'))
              ));
    }
  }

  //ACTION FOR DOCUMENT SUGGESTION DROP-DOWN
  public function getDocumentAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $documentTable = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');
    $documentName = $documentTable->info('name');
    $data = array();
    $select = $documentTable->select()
														->setIntegrityCheck(false)
														->from($documentName) 
                            ->join($pageTableName, $pageTableName . '.page_id = '. $documentName . '.page_id',array('title AS page_title', 'photo_id as page_photo_id'))
                            ->join($allowName, $allowName . '.resource_id = '. $pageTableName . '.page_id', array('resource_type','role'))
														->where($allowName.'.resource_type = ?', 'sitepage_page')
														->where($allowName.'.role = ?', 'registered')
														->where($allowName.'.action = ?', 'view')
														->where($documentName.'.search = ?', 1)
														->where($documentName.'.sitepagedocument_title  LIKE ? ', '%' . $title . '%')
														->limit($limit)
														->order($documentName.'.creation_date DESC');
    $select = $select
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $documents = $documentTable->fetchAll($select);

    foreach ($documents as $document) {
			//SSL WORK
			if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
				$manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
				$thumbnail = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl().'/'.$manifest_path."/ssl?url=".urlencode($document->thumbnail);
		  }
      else {
        $thumbnail = $document->thumbnail;
      }
      $content_photo = $this->view->htmlLink($document->getHref(), '<img height = "50px", src="'. $thumbnail .'" class="thumb_icon" />');
      
      $data[] = array(
          'id' => $document->document_id,
          'label' => $document->sitepagedocument_title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE DOCUMENT OF DAY ENTRY
  public function deleteDocumentOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }

  //ACTION FOR MULTI DELETE DOCUMENT ENTRIES
  public function multiDeleteDocumentAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $sitepageitemofthedays = Engine_Api::_()->getItem('sitepage_itemofthedays', (int) $value);
          if (!empty($sitepageitemofthedays)) {
            $sitepageitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-day-items'));
  }
 
  // ACTION FOR CHANGE SETTINGS OF TABBED DOCUMENT WIDZET TAB
  public function editTabAction() {
    //FORM GENERATION
    $this->view->form = $form = new Sitepagedocument_Form_Admin_EditTab();
    $id = $this->_getParam('tab_id');

    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $values = $tab->toarray();
      $form->populate($values);
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $tab->setFromArray($values);
      $tab->save();
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR UPDATE ORDER  OF DOCUMENTS WIDGTS TAB
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {
          $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
          if (!empty($tab)) {
            $tab->order = $key + 1;
            $tab->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'widget'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE TAB ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('tab_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    try {
      $tab->enabled = !$tab->enabled;
      $tab->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagedocument/settings/widget');
  }
  
 
  public function categoriesAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepagedocument_admin_main', array(), 'sitepagedocument_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sitepagedocument')->fetchAll();
  }  
  
  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Sitepagedocument_Form_Admin_Category();
    $form->setAction($this->view->url());
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $categoryTable->insert(array(
        'title' => $values['label'],
      ));
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->document_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
    $documentTable = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');
    $category = $categoryTable->find($id)->current();
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/delete-category.tpl');
      return;
    }
    
    // Process
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->delete();
      
      $documentTable->update(array(
        'category_id' => 0,
      ), array(
        'category_id = ?' => $category->getIdentity(),
      ));
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->document_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
    $category = $categoryTable->find($id)->current();

    // Generate and assign form
    $form = $this->view->form = new Sitepagedocument_Form_Admin_Category();
    $form->setAction($this->view->url());
    $form->setField($category);
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    // Ok, we're good to add field
    $values = $form->getValues();

    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->title = $values['label'];
      $category->save();
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }  

  //ACTION FOR FAQ
  public function faqAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagedocument_admin_main', array(), 'sitepagedocument_admin_main_faq');

    $this->view->show = $this->_getParam('show');
  }

  public function readmeAction() {
    
  }

}
?>