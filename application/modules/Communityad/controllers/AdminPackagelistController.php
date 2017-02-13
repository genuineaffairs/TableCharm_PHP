<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackageListController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminPackagelistController extends Core_Controller_Action_Admin {

  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;

  public function init() {
    $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_main_packagelist');
    $this->view->navigation = $this->_navigation;
    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->_viewer_id = $this->_viewer->getIdentity();
  }

  //DISPLAY PACKAGE LIST
  public function indexAction() {
    $table = Engine_Api::_()->getItemtable('package');
    $tableName = $table->info("name");
    $userAdName = Engine_Api::_()->getItemtable('userads')->info("name");
    $packages_select = $table->select()
            ->setIntegrityCheck(false)
            ->from($tableName)
            ->joinLeft($userAdName, $userAdName . '.package_id =' . $tableName . '.package_id', new Zend_Db_Expr('COUNT(userad_id) as total_ad'))
            ->group("$tableName.package_id")
            ->order('order ASC')
            ->order('creation_date DESC');
    $packages_select->where('type = ?', $this->_getParam('type', 'default'));
    $this->view->type = $this->_getParam('type', 'default');
    $paginator = Zend_Paginator::factory($packages_select);
    $paginator->setItemCountPerPage(100);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->getCommunityadTitle = Engine_Api::_()->communityad()->getCommunityadTitle();
    $this->view->adTypes = Engine_Api::_()->getItemTable('communityad_adtype')->fetchAll();
    $this->view->getAdTypeStatus = Engine_Api::_()->getItemTable('communityad_adtype')->getStatus($this->view->type);
  }

//ACTION FOR MAKE PACKAGES ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $package = Engine_Api::_()->getItem('package', $id);
      if ($package->enabled == 0) {
        $package->enabled = 1;
      } else {
        $package->enabled = 0;
      }
      $package->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($package->type != 'default') {
      $this->_redirectCustom(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'index', 'type' => 'sponsored_stories'));
    } else {
      $this->_redirect('admin/communityad/packagelist');
    }
  }

// ADD NEW PACKAGE
  public function createAction() {

    // Make form
    $this->view->form = $form = new Communityad_Form_Admin_Create();

    // Save new package
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $decLength = Engine_String::strlen($values['desc']);
      if ($decLength > 250) {
        $error = $this->view->translate('Description should not be more than 250 characters long.');
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->addError($error);
        return;
      }
      // For non-free package
      if (!empty($values['price'])) {

        // Test curl support
        if (!function_exists('curl_version') ||
                !($info = curl_version())) {
          $this->view->error = $this->view->translate('The PHP extension cURL does not appear to be installed, which is required for interaction with payment gateways. Please contact your hosting provider.');
        }
        // Test curl ssl support
        else if (!($info['features'] & CURL_VERSION_SSL) ||
                !in_array('https', $info['protocols'])) {
          $this->view->error = $this->view->translate('The installed version of the cURL PHP extension does not support HTTPS, which is required for interaction with payment gateways. Please contact your hosting provider.');
        }
        // For not enable gateways
        if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
          $form->getDecorator('errors')->setOption('escape', false);

          $error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  before creating a paid package.', '<a href="' . $this->view->baseUrl() . '/admin/payment/gateway" ' . " target='_blank'" . '">', '</a>');
          $this->view->status = false;
          $error = Zend_Registry::get('Zend_Translate')->_($error);
          return $form->addError($error);
        }
      }

      $detail = -1;
      if ($values['model_click'] != -1)
        $detail = $values['model_click'];
      if ($values['model_view'] != -1)
        $detail = $values['model_view'];
      if ($values['model_period'] != -1)
        $detail = $values['model_period'];
      $values['model_detail'] = $detail;

      if (($values['model_detail'] <= $values['renew_before']) && $values['model_detail'] != -1) {
        return $form->addError("Please enter low value from expiry");
      }

      if (@in_array('0', $values['level_id'])) {
        $values['level_id'] = 0;
      } else {
        $values['level_id'] = implode(',', $values['level_id']);
      }
      include_once(APPLICATION_PATH . "/application/modules/Communityad/controllers/license/license2.php");

      $package = Engine_Api::_()->getItem('package', $package_id);

      // Create package in gateways?
      if (!empty($values['price'])) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
          $gatewayPlugin = $gateway->getGateway();
          if (method_exists($gatewayPlugin, 'createProduct')) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
        }
        
        //START THIS CODE USE FOR COUPON EDIT WHEN CREATE A NEW PACKAGE AND SELECT ALL THOSE COUPON WHICH HAVE SELECT ALL OPTION FOR THIS PACKAGE TYPE.
        $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon') ;
        if (!empty($moduleEnabled)) {
					Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->editCouponsAfterCreateNewPackage($package->getType());
        }
        //END COUPON WORK.

      }
      $this->_helper->redirector->gotoRoute(array('action' => 'index','type'=>$values['type']));
    }
  }

// EDIT THE PACKAGE
  public function editAction() {
    $id = $this->_getParam('id');
    $packageTable = Engine_Api::_()->getItemTable('package');

    $formElementsContent = $packageTable->getVal($id)->toarray();
    $formElementsContent['urloption'] = explode(',', $formElementsContent['urloption']);
    $formElementsContent['level_id'] = explode(',', $formElementsContent['level_id']);

    // create Admin/Edit form
    $form = new Communityad_Form_Admin_Edit(array('item' => $packageTable->getVal($id)));
    if (!$this->getRequest()->isPost())
      $form->populate($formElementsContent);
    $this->view->form = $form;

    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $values = $form->getValues();
      $decLength = Engine_String::strlen($values['desc']);
      if ($decLength > 250) {
        $error = $this->view->translate('Description should not be more than 250 characters long.');
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->addError($error);
        return;
      }
      $values['package_id'] = $id;
      // UNSET THE VALUES WHICH ARE DISABLE
      unset($values['type']);
      unset($values['price']);
      unset($values['price_model']);

      $detail = -1;
      if ($formElementsContent['price_model'] == "Pay/click") {
        $detail = $values['model_click'];
      } elseif ($formElementsContent['price_model'] == "Pay/view") {
        $detail = $values['model_view'];
      } elseif ($formElementsContent['price_model'] == "Pay/period") {
        $detail = $values['model_period'];
      }
      $values['model_detail'] = $detail;
      // convert into string
      $values['urloption'] = implode(',', $values['urloption']);
      
      if (@in_array('0', $values['level_id'])) {
        $values['level_id'] = 0;
      } else {
        $values['level_id'] = implode(',', $values['level_id']);
      }
      // if renew is enable then renew is less tthen limit
      if (($values['model_detail'] <= $values['renew_before']) && $formElementsContent['model_detail'] != -1) {
        return $form->addError("Please enter low value from expiry");
      }

      $package_id = $packageTable->setValue($values);
      
      $package = $packageTable->getVal($id);
      //CREATE PACKAGE IN GATEWAYS
      if (!$package->isFree()) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
          $gatewayPlugin = $gateway->getGateway();
         
          if (!method_exists($gatewayPlugin, 'createProduct') ||
                  !method_exists($gatewayPlugin, 'editProduct') ||
                  !method_exists($gatewayPlugin, 'detailVendorProduct')) {
            continue;
          }

          //IF IT THROWS AN EXCEPTION, OR RETURNS EMPTY, ASSUME IT DOESN'T EXIST?
          try {
            $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
          } catch (Exception $e) {
            $info = false;
          }
          //CREATE
          if (!$info) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
          //EDIT
          else {
            $gatewayPlugin->editProduct($package->getGatewayIdentity(), $package->getGatewayParams());
          }
        }
      }
      
      $this->_helper->redirector->gotoRoute(array('action' => 'index','type'=>$formElementsContent['type']));
    }
  }

  // SHOW THE PACKAGE DETAILS
  public function packgeDetailAction() {
    $id = $this->_getParam('id');
    $table = Engine_Api::_()->getDbtable('packages', 'communityad');
    $rName = $table->info('name');
    $package_select = $table->select()
            ->where('enabled = ?', 1)
            ->where('package_id = ?', $id);
    $detail = $table->fetchAll($package_select);
    $detail = $detail->toarray();
    $this->view->package = $detail;
  }

  public function updateAction() {

    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {

          $package = Engine_Api::_()->getItem('package', (int) $value);
          if (!empty($package)) {
            $package->order = $key + 1;
            $package->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

}
