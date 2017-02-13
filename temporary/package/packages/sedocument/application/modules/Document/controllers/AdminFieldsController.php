<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'document';

  protected $_requireProfileType = true;

	//ACTION FOR SHOWING THE CUSTOM FIELDS IN ADMIN
  public function indexAction()
  {
    //MAKE NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('document_admin_main', array(), 'document_admin_main_fields');

    parent::indexAction();
  }

	//ACTION FOR CREATING THE NEW FIELD
  public function fieldCreateAction(){

    parent::fieldCreateAction();

		//GET FORM
    $form = $this->view->form;

    if($form){
      //$form->setTitle('Add Document Question');

			$form->removeElement('show');
			$form->addElement('hidden', 'show', array('value' => 0));

      $display = $form->getElement('display');
      $display->setLabel('Show on document page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on document page',
          0 => 'Hide on document page'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
      )));
    }
  }

	//ACTION FOR EDIT THE FIELD
  public function fieldEditAction(){

    parent::fieldEditAction();

		//GET FORM
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Document Question');

			$form->removeElement('show');
			$form->addElement('hidden', 'show', array('value' => 0));

      $display = $form->getElement('display');
      $display->setLabel('Show on document page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on document page',
          0 => 'Hide on document page'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
      )));
    }
  }

	//ACTION FOR HEADING CREATION
  public function headingCreateAction() {
    parent::headingCreateAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

	//ACTION FOR HEADING EDITION
  public function headingEditAction() {
    parent::headingEditAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

	//ACTION FOR PROFILE DELETION
  public function typeDeleteAction() {
    $option_id = $this->_getParam('option_id');

    if (!empty($option_id)) {

      //DELETE FIELD ENTRIES IF EXISTS
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('document', 'maps');
      $select = $fieldmapsTable->select()->where('option_id =?', $option_id);
      $metaData = $fieldmapsTable->fetchAll($select)->toArray();
      if (!empty($metaData)) {
        foreach ($metaData as $key => $child_ids) {
          $child_id = $child_ids['child_id'];

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldmetaTable = Engine_Api::_()->fields()->getTable('document', 'meta');
          $fieldmetaTable->delete(array(
              'field_id = ?' => $child_id,
          ));
        }
      }

      $fieldmapsTable = Engine_Api::_()->fields()->getTable('document', 'maps');
      $fieldmapsTable->delete(array(
          'option_id = ?' => $option_id,
      ));

      $documentTable = Engine_Api::_()->getDbtable('documents', 'document');
      $select = $documentTable->select()
              ->from($documentTable->info('name'), array('document_id'))
              ->where('profile_type = ?', $option_id);
      $rows = $documentTable->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $document_ids) {
          $document_id = $document_ids['document_id'];

          $document = Engine_Api::_()->getItem('document', $document_id);
          $document->profile_type = 0;
          $document->save();

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldvalueTable = Engine_Api::_()->fields()->getTable('document', 'values');
          $fieldvalueTable->delete(array(
              'item_id = ?' => $document_id,
          ));

          $fieldsearchTable = Engine_Api::_()->fields()->getTable('document', 'search');
          $fieldsearchTable->delete(array(
              'item_id = ?' => $document_id,
          ));
        }
      }

			//DELETE MAPPING
			Engine_Api::_()->getDbtable('profilemaps', 'document')->delete(array('profile_type = ?' => $option_id));
    }
    parent::typeDeleteAction();
  }
}