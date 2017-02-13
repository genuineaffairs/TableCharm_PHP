<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminProfilemaps.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_AdminProfilemapsController extends Core_Controller_Action_Admin {

	//ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
  public function manageAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('document_admin_main', array(), 'document_admin_main_profilemaps');

		//FETCH MAPPING DATA
    $this->view->paginator = Engine_Api::_()->getDbTable('categories', 'document')->categoryMappingData();
		$this->view->paginator->setItemCountPerPage(500);
  }

	//ACTION FOR MAP THE PROFILE WITH CATEGORY
  public function mapAction() {

		//DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE THE FORM
    $this->view->form = $form = new Document_Form_Admin_Profilemaps_Map();

		//POST DATA
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

			//GET DATA
      $values = $form->getValues();

			//BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

				//SAVE THE NEW MAPPING
        $row = Engine_Api::_()->getDbtable('profilemaps', 'document')->createRow();
        $row->profile_type = $values['profile_type'];
        $row->category_id = $this->_getParam('category_id');
        $row->save();
        $db->commit();

				//IF YES BUTTON IS CLICKED THEN CHANGE MAPPING OF ALL DOCUMENTS
        if (isset($_POST['yes_button'])) {
	
					//SELECT DOCUMENTS WHICH HAVE THIS CATEGORY AND THIS PROFILE TYPE
          $rows = Engine_Api::_()->getDbtable('documents', 'document')->getCategoryDocument($row->category_id);

          if (!empty($rows)) {
            foreach ($rows as $key => $document_ids) {
              $document_id = $document_ids['document_id'];

							//GET FIELD VALUE TABLE
							$fieldvalueTable = Engine_Api::_()->fields()->getTable('document', 'values');

							//PUT NEW PROFILE TYPE
							$fieldvalueTable->insert(array(
									'item_id' => $document_id,
									'field_id' => Engine_Api::_()->getDbTable('metas', 'document')->defaultProfileId(),
									'index' => 0,
									'value' => $row->profile_type,
							));

							$document = Engine_Api::_()->getItem('document', $document_id);
							$document->profile_type = $row->profile_type;
							$document->save();
            }
          }
        }
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

    $this->renderScript('admin-profilemaps/map.tpl');
  }

	//ACTION FOR DELETE MAPPING 
  public function deleteAction() {

		//DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

		//GET MAPPING ID
    $this->view->profilemap_id = $profilemap_id = $this->_getParam('profilemap_id');

		//POST DATA
    if ($this->getRequest()->isPost()) {

			//BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

				//GET MAPPING ITEM
        $profileMapping = Engine_Api::_()->getItem('document_profilemap', $profilemap_id);

				//GET DOCUMENT TABLE
				$documentTable = Engine_Api::_()->getDbTable('documents', 'document');

				//SELECT DOCUMENTS WHICH HAVE THIS CATEGORY
				$rows = $documentTable->getCategoryDocument($profileMapping->category_id);

				if (!empty($rows)) {
					foreach ($rows as $key => $document_ids) {
		
						//GET DOCUMENT ID
						$document_id = $document_ids['document_id'];

						//DELETE ALL MAPPING VALUES FROM FIELD TABLES
						Engine_Api::_()->fields()->getTable('document', 'values')->delete(array('item_id = ?' => $document_id));
						Engine_Api::_()->fields()->getTable('document', 'search')->delete(array('item_id = ?' => $document_id));

						//UPDATE THE PROFILE TYPE OF ALREADY CREATED DOCUMENTS
						$documentTable->update(array('profile_type' => 0), array('document_id = ?' => $document_id));
					}
				}

				//DELETE MAPPING
        $profileMapping->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping deleted successfully !'))
      ));
    }
    $this->renderScript('admin-profilemaps/delete.tpl');
  }
}