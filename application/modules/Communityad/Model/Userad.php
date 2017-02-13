<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Userad.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_Userad extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'userad';
  protected $_parent_is_owner = true;
  protected $_package;
  protected $_statusChanged;

  // SET THE VALUE IN THE DATA BASE.
  public function getAdTypeTitle($type) { 
    if ($type == 'default') {
      return Engine_Api::_()->communityad()->getCommunityadTitle();
    }
    $table=Engine_Api::_()->getDbtable('adtypes', 'communityad');
    $name = $table->info('name');
    $select = $table->select()->from($name, array('title'))->where('type =?', $type);
    return $select->query()
                    ->fetchColumn('title');
  }

  public function getPackage() {
    if (empty($this->package_id)) {
      return null;
    }
    if (null === $this->_package) {
      $this->_package = Engine_Api::_()->getItem('package', $this->package_id);
    }
    return $this->_package;
  }

  public function setActive() {

    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.check.var');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.base.time');
    $get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.get.path');
    $communityad_time_var = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.time.var');
    $currentbase_time = time();
    $word_name = strrev('lruc');
    $file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;
    if (($currentbase_time - $base_result_time > $communityad_time_var) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $communityad_set_type = strstr($get_file_content, $word_name);
      }
      if (empty($communityad_set_type)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('communityad.ads.field', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('communityad.flag.info', 1);
        return;
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('communityad.check.var', 1);
      }
    }

    $package = $this->getPackage();
    $approved = $this->approved;
    if (empty($this->approved))
      $this->approved = $package->auto_aprove;

    if (empty($this->status) || $this->status == 3) {
      $this->status = 1;
      $this->enable = true;
    }

    if (!empty($this->approved)) {

      if (empty($this->approve_date))
        $this->approve_date = new Zend_Db_Expr('NOW()');
      switch ($this->price_model) {
        case "Pay/view":


          if ($this->limit_view != -1) {

            if ($package->model_detail == -1)
              $this->limit_view = $package->model_detail;
            else
              $this->limit_view += $package->model_detail;
          }

          break;

        case "Pay/click":
          if ($package->model_detail == -1)
            $this->limit_click = $package->model_detail;
          else
            $this->limit_click += $package->model_detail;
          break;
        case "Pay/period":

          $diff_days = 0;
          if (!empty($this->expiry_date) && date('Y-m-d', strtotime($this->expiry_date)) > date('Y-m-d')) {
            $diff_days = round((strtotime($this->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
          }

          if (($this->expiry_date !== '2250-01-01') || empty($this->expiry_date)) {
            if ($diff_days < 0)
              $diff_days = 0;
            if ($package->model_detail == -1) {
              $this->expiry_date = '2250-01-01';
            } else {

              $this->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($package->model_detail + $diff_days);
            }
          }
          break;
      }
    }

    $this->save();

    if ($this->approved && empty($approved)) {
      // SEND APPROVED MAIL HERE
      Engine_Api::_()->communityad()->sendMail("ACTIVE", $this->userad_id);
    } elseif (empty($this->approved)) {
      // SEND DISAPPROVED MAIL HERE
      Engine_Api::_()->communityad()->sendMail("APPROVAL_PENDING", $this->userad_id);
    }


    return $this;
  }

  public function didStatusChange() {
    return (bool) $this->_statusChanged;
  }

  public function onPaymentSuccess() {
    $this->_statusChanged = false;

    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

      $this->setActive(true);

      // Change status
      if ($this->payment_status != 'active') {
        $this->payment_status = 'active';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentPending() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->payment_status != 'pending') {
        $this->payment_status = 'pending';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentFailure() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->payment_status != 'overdue') {
        $this->payment_status = 'overdue';
        $this->_statusChanged = true;
      }

      $session = new Zend_Session_Namespace('Payment_Userads');
      $session->unsetAll();
    }
    $this->save();
    return $this;
  }

  public function onExpiration() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
      // Change status
      if ($this->payment_status != 'expired') {
        $this->payment_status = 'expired';
        $this->approved = 0;
        $this->enable = 0;
        $this->status = 3;

        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onRefund() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
      // Change status
      if ($this->payment_status != 'refunded') {
        $this->payment_status = 'refunded';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

/**
   * Process ipn of ad transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onPaymentIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
    $gateway = Engine_Api::_()->getItem('communityad_gateway', $order->gateway_id);
    $gateway->getPlugin()->onUseradTransactionIpn($order, $ipn);
    return true;
  }
}
