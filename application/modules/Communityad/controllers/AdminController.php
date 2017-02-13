<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminController extends Core_Controller_Action_Admin {

//ACTION FOR MAKE FEATURED/UNFEATURED
  public function featuredAction() {
    $id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $userads = Engine_Api::_()->getItem('userads', $id);
      $userads->featured = !$userads->featured;
      $userads->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/communityad/viewad');
  }

  //ACTION FOR MAKE SPONSORED /UNSPONSORED
  public function sponsoredAction() {
    $id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $userads = Engine_Api::_()->getItem('userads', $id);
      $userads->sponsored = !$userads->sponsored;
      $userads->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/communityad/viewad');
  }

  //ACTION FOR MAKE  APPROVE/DIS-APPROVE
  public function approvedAction() {
    $id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $userads = Engine_Api::_()->getItem('userads', $id);

      if (empty($userads->approved)) {
        $userads->approved = 1;
        if (empty($userads->status) || $userads->status == 3) {
          $userads->status = 1;
          $userads->enable = 1;
        }
        $package = Engine_Api::_()->getItem('package', $userads->package_id);
        // FOR FREE PACKAGE
        if (empty($userads->approve_date) && $package->isFree())
          $userads->approve_date = date('Y-m-d H:i:s');
        // FOR NON-FREE PACKAGE
        if (empty($userads->approve_date) && !$package->isFree() && $userads->payment_status == 'active')
          $userads->approve_date = date('Y-m-d H:i:s');

        switch ($userads->price_model) {
          // FOR VIEWS
          case "Pay/view":

            if (empty($userads->limit_view)) {

              if (!$package->isFree()) {
                //  NON ACTIVE STATUS OF PAYMENT THEN GIVE 5 VIEWS
                if ($userads->payment_status != "active")
                  $userads->limit_view = 5;
                else
                  $userads->limit_view = $package->model_detail;
              }else {
                $userads->limit_view = $package->model_detail;
              }
            }

            break;
          // FOR CLICKS
          case "Pay/click":
            if (empty($userads->limit_click)) {

              if (!$package->isFree()) {
                //  NON ACTIVE STATUS OF PAYMENT THEN GIVE 1 CLICKS
                if ($userads->payment_status != "active")
                  $userads->limit_click = 1;
                else
                  $userads->limit_click = $package->model_detail;
              }else {
                $userads->limit_click = $package->model_detail;
              }
            }
            break;
          // FOR DAYS
          case "Pay/period":
            $diff_days = 0;
            // GET THE REMAING DAYS
            if (!empty($userads->expiry_date) && date('Y-m-d', strtotime($userads->expiry_date)) > date('Y-m-d')) {
              $diff_days = round((strtotime($userads->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
            }


            if (($diff_days <= 0 && $userads->expiry_date !== '2250-01-01') || empty($userads->expiry_date)) {
              // FOR UNLIMITED DAYS
              if ($package->model_detail == -1) {

                if (!$package->isFree()) {
                  //  NON ACTIVE STATUS OF PAYMENT THEN GIVE 1 DAYS
                  if ($userads->payment_status != "active")
                    $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate(1);
                  else
                    $userads->expiry_date = '2250-01-01';
                }else {
                  $userads->expiry_date = '2250-01-01';
                }
              } else {
                // FOR LIMITED DAYS
                $days = $package->model_detail;
                if (!$package->isFree()) {
                  if ($userads->payment_status != "active")
                    $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate(1);
                  else
                    $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($days);
                }else {
                  $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($days);
                }
              }
            }
            break;
        }


        // SEND APPROVED MAIL
        Engine_Api::_()->communityad()->sendMail("APPROVED", $userads->userad_id);
      } else {
        $userads->approved = 0;
        // SEND DISAPPROVED MAIL
        Engine_Api::_()->communityad()->sendMail("DISAPPROVED", $userads->userad_id);
      }
      $userads->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirect('admin/communityad/viewad');
  }

  // RENEW ADVERTIESMENT
  public function renewAction() {
    $id = $this->_getParam('id');
    $this->view->userad = $userads = Engine_Api::_()->getItem('userads', $id);
    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        // AD
        $userads = Engine_Api::_()->getItem('userads', $id);
        $approved = $userads->approved;
        $diff_days = 0;
        // PACKAGE FOR AD
        $package = Engine_Api::_()->getItem('package', $userads->package_id);

        if (empty($userads->approve_date))
          $userads->approve_date = date('Y-m-d H:i:s');

        switch ($userads->price_model) {
          // FOR VIEWS
          case "Pay/view":
            if ($userads->limit_click != -1) {
              if ($package->model_detail == -1)
                $userads->limit_view = $package->model_detail;
              else
                $userads->limit_view += $package->model_detail;
            }
            break;
          // FOR CLICKS
          case "Pay/click":
            if ($userads->limit_click != -1) {
              if ($package->model_detail == -1)
                $userads->limit_click = $package->model_detail;
              else
                $userads->limit_click += $package->model_detail;
            }
            break;
          // FOR DAYS
          case "Pay/period":
            $diff_days = 0;
            if (!empty($userads->expiry_date) && date('Y-m-d', strtotime($userads->expiry_date)) > date('Y-m-d')) {
              $diff_days = round((strtotime($userads->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
            }

            if (($userads->expiry_date !== '2250-01-01') || empty($userads->expiry_date)) {
              if ($diff_days < 0)
                $diff_days = 0;
              if ($package->model_detail == -1) {
                $userads->expiry_date = '2250-01-01';
              } else {
                $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($package->model_detail + $diff_days);
              }
            }
            break;
        }

        $approved = $userads->approved;
        $userads->status = 1;
        if (empty($approved)) {
          $userads->approved = $package->auto_aprove;
          $userads->enable = 1;
        }
        $userads->renewbyadmin_date = date("Y-m-d H:i:s");
        $userads->save();
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
    $this->renderScript('admin/renew.tpl');
  }

}
?>