<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Resume_Form_Resume_Checkout extends Epayment_Form_Checkout
{
  public function init()
  {
    parent::init();
    
    $baseUrl = $this->_getBaseUrl();
    $router = Zend_Controller_Front::getInstance()->getRouter();
    
    $this->return->setValue($baseUrl . $this->_item->getActionHref('payment-success'));
    $this->cancel_return->setValue($baseUrl . $this->_item->getActionHref('payment-cancel'));
    
    $view = Zend_Registry::get('Zend_View');
    $item_name = $view->translate('%s resume with %s package', $this->_item->getTitle(), $this->getPackage()->getTitle());
    $this->item_name->setValue($item_name);
 
    
    $this->setTitle('Resume Payment Checkout')
      ->setDescription('Please review and confirm the item you are about to check out, then press "Pay by PayPal" button to proceed.');
  }
}