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

class Epayment_Plugin_Gateway_PayPal extends Epayment_Plugin_Gateway_Abstract
{

  protected $_ipnData = array();
  protected $_log;
 
  public function init()
  {
    $this->_log = new Zend_Log();
    //$this->_log->addWriter(new Zend_Log_Writer_Firebug());
    $this->_log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/epayment-paypal-ipn.log'));
  }
  
  public function setIpnData($data)
  {
    $this->_ipnData = $data;
  }
  
  public function getNormalizedData()
  {
    if (!$this->_ipnData['txn_id'] && $this->_ipnData['txn_type'] == 'subscr_signup')
    {
      throw new Epayment_Plugin_Gateway_Exception("Invalid transaction type 'subscr_signup'");
    }

    if ( $this->_ipnData['payment_status'] == 'Completed' )
    {
      $payment_status = Epayment_Model_Epayment::STATUS_COMPLETED;
    }
    else if ( $this->_ipnData['payment_status'] == 'Processed' || $this->_ipnData['payment_status'] == 'Pending' )
    {
      $payment_status = Epayment_Model_Epayment::STATUS_PENDING;
    }
    else if ( $this->_ipnData['payment_status'] == 'Refunded' )
    {
      $payment_status = Epayment_Model_Epayment::STATUS_REFUNDED;
    }    
    else if( $this->_ipnData['payment_status'] == 'Reversed' || $this->_ipnData['txn_type'] == 'subscr_cancel' || $this->_ipnData['payment_status'] == 'Cancelled_Reversal' ) 
    {
      $payment_status = Epayment_Model_Epayment::STATUS_CANCELLED;
    }
    else 
    {
      $payment_status = Epayment_Model_Epayment::STATUS_FAILED;
    }
        
    $normalizedData = array(
      'method' => 'PayPal',
      'status' => $payment_status,
      'subject' => $this->_ipnData['item_number'],
      'transaction_code' => $this->_ipnData['txn_id'],
      'package_id' => $this->_ipnData['custom'],
      'payer_account' => $this->_ipnData['payer_email'],
      'payer_name' => trim($this->_ipnData['first_name'].' '.$this->_ipnData['last_name']),
      'amount'        => $this->_ipnData['mc_gross'],
      'currency'      => $this->_ipnData['mc_currency'],
      'data' => $this->_ipnData
    );

    return $normalizedData;
  }
  
  public function getProcessingUrl()
  {
    if ($this->isTestMode())
    {
      $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    }
    else
    {
      $url = 'https://www.paypal.com/cgi-bin/webscr';
    }
    
    return $url;
  }
  
  public function validateIpn()
  {
    $processingUrl = $this->getProcessingUrl();
    $processingUrl = str_replace('https', 'http', $processingUrl);
    
    $client = new Zend_Http_Client();
    $client->setUri($processingUrl);
    $client->setConfig(array(
        'maxredirects' => 0,
        'timeout'      => 30));

    $post_data = array_merge($this->_ipnData, array('cmd' => '_notify-validate'));
    $client->setParameterPost($post_data);
    
    $response = $client->request('POST');
    
    if ($response->isError()) 
    {
      $message = "Error transmitting data.\n" .
        "Server reply was: " . $response->getStatus() .
        " " . $response->getMessage() . "\n";
      
      throw new Epayment_Plugin_Gateway_Exception($message);
    }
    
    $body = $response->getBody();
    
    /*
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
      return true;
    }
    */
    
    if (eregi("VERIFIED", $body))
    {
      return true;
    }
    return false;
  }
  
  
  public function log($message, $priority = Zend_Log::INFO)
  {
    $this->_log->log($message, $priority);
  }
  
  
}