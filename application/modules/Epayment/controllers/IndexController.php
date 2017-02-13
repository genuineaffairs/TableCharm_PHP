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

class Epayment_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {   
    // Get subject
    $epayment = null;
    if( null !== ($epaymentIdentity = $this->_getParam('epayment_id')) ) {
      $epayment = Engine_Api::_()->getItem('epayment', $epaymentIdentity);
      if( null !== $epayment ) {
        Engine_Api::_()->core()->setSubject($epayment);
      }
    }

    
    $this->_helper->requireUser->addActionRequires(array(
      'checkout',
    ));          
  }


  public function checkoutAction()
  {
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if (!$subject->getOwner('user')->isSelf($viewer)) 
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
   
    if (method_exists($subject, 'getCheckoutForm'))
    {
      $this->view->form = $form = $subject->getCheckoutForm();
    }
    else 
    {
      $this->view->form = $form = new Epayment_Form_Checkout(array('item' => $subject));
    }
    
    //echo get_class($form);
  }
  
  public function cancelReturnAction()
  {
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if (!$subject->getOwner('user')->isSelf($viewer)) 
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
  }
  
  
  public function returnAction()
  {
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if (!$subject->getOwner('user')->isSelf($viewer)) 
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
  }
  
  public function notifyAction()
  {
    $data = $this->getRequest()->getPost();
    
    $gateway = new Epayment_Plugin_Gateway_PayPal();
    
    $gateway->log("=== Start Processing IPN Data ===\n" . print_r($data, true));

    try
    {
      $gateway->setIpnData($data);
      if ($gateway->validateIpn())
      {
        // valid
        $gateway->log("Valid IPN Data");
        
        $normalizedData = $gateway->getNormalizedData();

        if (!$normalizedData['transaction_code']) {
          throw new Epayment_Plugin_Gateway_Exception("No transaction code was found in IPN Data");
        }
        
        $subject = Engine_Api::_()->getItemByGuid($normalizedData['subject']);
        if( !($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() )
        {
          throw new Epayment_Plugin_Gateway_Exception("Subject '".$normalizedData['subject']."' could not be found");
        }
        
        $epayment = $subject->processIpnData($normalizedData);
        
        if ($epayment instanceof Epayment_Model_Epayment)
        {
          $gateway->log("Added/Updated Epayment #".$epayment->getIdentity());
        }
      }
      else 
      {
        $gateway->log("Invalid IPN Data", Zend_Log::WARN);
        // invalid
      }
    }
    catch (Exception $e)
    {
      $gateway->log("Exception - Processing failed: " . $e->__toString(), Zend_Log::ERR);
      exit(1);
    }
    exit(1);
  }

}