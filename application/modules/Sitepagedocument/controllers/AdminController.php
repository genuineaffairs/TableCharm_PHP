<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_AdminController extends Core_Controller_Action_Admin {

  protected $_navigation;

  //ACTION FOR MAKE PAGE-DOCUMENT APPROVED/DIS-APPROVED
  public function approvedAction() {
    $document_id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);
      if ($sitepagedocument->approved == 0) {
        $sitepagedocument->approved = 1;
      } else {
        $sitepagedocument->approved = 0;
      }
      $sitepagedocument->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagedocument/manage');
  }

  //ACTION FOR MAKE PAGE-DOCUMENT APPROVED/DIS-APPROVED
  public function featuredAction() {
    $document_id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);
      if ($sitepagedocument->featured == 0) {
        $sitepagedocument->featured = 1;
      } else {
        $sitepagedocument->featured = 0;
      }
      $sitepagedocument->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagedocument/manage');
  }

   //ACTION FOR MAKE PAGE-DOCUMENT APPROVED/DIS-APPROVED
  public function highlightedAction() {
    $document_id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);
      if ($sitepagedocument->highlighted == 0) {
        $sitepagedocument->highlighted = 1;
      } else {
        $sitepagedocument->highlighted = 0;
      }
      $sitepagedocument->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagedocument/manage');
  }

  //ACTION FOR DELETE THE PAGE-DOCUMENT
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->document_id = $document_id = $this->_getParam('id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

				Engine_Api::_()->sitepagedocument()->deleteContent($document_id);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin/delete.tpl');
  }

}
?>