<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagedocument_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION TO MANAGE DOCUMENTS
  public function indexAction() {
    //CREATE NAVIGATION TABS
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagedocument_admin_main', array(), 'sitepagedocument_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitepagedocument_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    //GET DOCUMENTS
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
    $tableSitepage = Engine_Api::_()->getItemTable('sitepage_page')->info('name');
    $table = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');
    $rName = $table->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName, array('document_id', 'page_id', 'owner_id', 'sitepagedocument_title', 'creation_date', 'comment_count', 'like_count', 'views', 'featured', 'approved', 'status','highlighted'))
                    ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
                    ->joinLeft($tableSitepage, "$rName.page_id = $tableSitepage.page_id", 'title AS sitepage_title');

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'document_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'document_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    include APPLICATION_PATH . '/application/modules/Sitepagedocument/controllers/license/license2.php';
  }

  //ACTION FOR MULTI DELETE DOCUMENTS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          //DELETE DOCUMENTS FROM DATABASE AND SCRIBD
					$document_id = (int) $value;
					Engine_Api::_()->sitepagedocument()->deleteContent($document_id);
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}
?>