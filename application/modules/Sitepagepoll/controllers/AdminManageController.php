<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING SITEPAGE-POLLS
  public function indexAction() {

    //CREATE NAVIGATION TABS
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagepoll_admin_main', array(), 'sitepagepoll_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitepagepoll_Form_Admin_Manage_Filter();

    //GET POLLS
    $page = $this->_getParam('page', 1);
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
    $tableSitepage = Engine_Api::_()->getItemTable('sitepage_page')->info('name');
    $tableSitepagepoll = Engine_Api::_()->getDbtable('polls', 'sitepagepoll');
    $rName = $tableSitepagepoll->info('name');
    $select = $tableSitepagepoll->select()
                    ->setIntegrityCheck(false)
                    ->from($rName)
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
                'order' => 'poll_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'poll_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = array();
    include APPLICATION_PATH . '/application/modules/Sitepagepoll/controllers/license/license2.php';
  }

  //ACTION FOR MULTI DELETE POLLS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          //DELETE POLLS FROM DATABASE AND SCRIBD
          $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', (int) $value);

          if (!empty($sitepagepoll)) {

            //FINALLY DELETE POLL MODEL
            $sitepagepoll->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

}
?>