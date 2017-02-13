<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Epayment_Model_Epayment extends Core_Model_Item_Abstract
{ 
  // enum('pending','cancelled','failed','incomplete','complete')
  const STATUS_COMPLETED = 'completed';
  const STATUS_PENDING = 'pending';
  const STATUS_CANCELLED = 'cancelled';
  const STATUS_FAILED = 'failed';
  const STATUS_REFUNDED = 'refunded';
  
  const METHOD_PAYPAL = 'paypal';
  const METHOD_CHECK = 'check';
  const METHOD_CASH = 'cash';
  const METHOD_CREDITCARD = 'cc';
  const METHOD_OTHER = 'other';
  
  // Interfaces
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'epayment_view',
      'reset' => true,
      'user_id' => $this->user_id,
      'epayment_id' => $this->epayment_id,
      'slug' => $this->getSlug(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function isStatus($status)
  {
  	return $this->status == $status;
  }
  
  public function getStatusText()
  {
    return $this->getStatusTypes($this->status);
  }
  
  public static function getStatusTypes($key = null)
  {
    $types = array(
      self::STATUS_COMPLETED => 'Completed',
      self::STATUS_PENDING => 'Pending',
      self::STATUS_CANCELLED => 'Cancelled',
      self::STATUS_FAILED => 'Failed',
      self::STATUS_REFUNDED => 'Refunded',
    );
    
    if ($key !== null) {
      return (isset($types[$key])) ? $types[$key] : 'Pending';
    }
    
    return $types;
  }
  
  public function getMethodText()
  {
    return $this->getMethodTypes($this->method);  
  }
  
  public static function getMethodTypes($key = null)
  {
    $methods = array(
      self::METHOD_PAYPAL => 'PayPal',
      self::METHOD_CHECK => 'Check',
      self::METHOD_CASH => 'Cash',
      self::METHOD_CREDITCARD => 'CreditCard',
      self::METHOD_OTHER => 'Other',
    );
    
    if ($key !== null) {
      return (isset($methods[$key])) ? $methods[$key] : 'PayPal';
    }    
    
    return $methods;
  }
  
  public function getPackage()
  {
    try {
      return Engine_Api::_()->getItemApi($this->resource_type)->getPackage($this->package_id);
    }
    catch (Exception $e) {
      return Engine_Api::_()->getItemTable($this->resource_type.'_package')->getPackage($this->package_id);
    }
  }
  
  public function printAmount()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $view = Zend_Registry::get('Zend_View');    
    $str = $view->locale()->toCurrency($this->amount, $this->currency);
    
    return $str;
  }
  
  public function updateProcessed($value)
  {
    $this->processed = $value ? 1 : 0;
    $this->processed_date = date('Y-m-d H:i:s');
  }
}