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

class Resume_Form_Admin_Epayment_Process extends Epayment_Form_Admin_Manage_Process
{
  
  protected $_type = 'resume';
  
  public function init()
  {
    parent::init();
  }  
  
  protected function _addSettingFields()
  {
    if (!$this->_item->isStatus(Epayment_Model_Epayment::STATUS_COMPLETED))
    {
      $this->addElement('Hidden', 'base_start_expiration_date', array(
        'value' => 'skip'
      ));
      
      return;
    }    
    
    if ($this->_item->processed)
    {
      if ($this->_item->getPackage()->isForever())
      {
        $options = array(
          'skip' => 'Do not update - leave as is',
          'auto' => 'Never expired'
        );
      }
      else 
      {
        $options = array(
          'skip' => 'Do not update - leave as is',
          'current_date' => 'Today + duration',
          'payment_date' => 'Payment Date + duration',
          'status_date' => 'Current Status Date + duration',
        );
        if ($this->_item->getParent()->hasExpirationDate())
        {
          $options['expiration_date'] = 'Current Expiration Date + duration';
        }
      }
      $value = 'skip';
    }
    else 
    {
      if ($this->_item->getPackage()->isForever())
      {
        $options = array(
          'skip' => 'Do not update - leave as is',
          'auto' => 'Never expired'
        );
        $value = 'auto';
      }
      else 
      {
        $options = array(
          'skip' => 'Do not update - leave as is',
          'current_date' => 'Today + duration',
          'payment_date' => 'Payment Date + duration',
          'status_date' => 'Current Status Date + duration',
        );
        if ($this->_item->getParent()->hasExpirationDate())
        {
          $options['expiration_date'] = 'Current Expiration Date + duration';
        }
        
        // already paid previously
        $totalEpayment = $this->_item->getParent()->epayments()->getEpaymentCount();
        if ($totalEpayment > 1)
        {
          // renew
          if ($this->_item->getPackage()->isSelf($this->_item->getParent()->getPackage()))
          {
            if (array_key_exists('expiration_date', $options))
            {
              $value = 'expiration_date';
            }
            else 
            {
              $value = 'current_date';
            }
          }
          // upgrade
          else
          {
            $value = 'current_date';
          }
        }
        else 
        {
          $value = 'current_date';
        }
        
      }
      //$value = 'skip';
    }
    
    $this->addElement('Heading', 'process_header', array(
      'label' => 'Processing Options:',
      'value' => 'Processing Options:',
    ));  
    
    $this->process_header->removeDecorator('Label')
          ->removeDecorator('HtmlTag')
          ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');    
    
    $this->addElement('Radio', 'base_start_expiration_date', array(
      'Label' => 'Update Expiration To',
      'multiOptions' => $options,
      'value' => $value
    ));
  }
  

}