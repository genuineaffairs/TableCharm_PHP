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

class Epayment_Form_Test_Ipn extends Engine_Form
{
  protected $_item;	
	
  public function init()
  {
    $this->setTitle('Epayment Ipn Test')
      ->setDescription('Please review and confirm the item you are about to check out, then press Pay Now button to proceed.')
      ;
    
		$this->addElement('Select', 'payment_status', array(
			'label' => 'Payment Status',
			'multiOptions' => array(
				'Completed' => 'Completed',
				'Processed' => 'Processed',
				'Pending' => 'Pending',
				'Refunded' => 'Refunded',
				'Reversed' => 'Reversed',
				'Cancelled_Reversal' => 'Cancelled_Reversal',
			),
		));      
      
		$this->addElement('Text', 'item_number', array(
			'label' => 'item_number (guid)',
			'value' => 'job_',
		));
		
		$this->addElement('Text', 'txn_id', array(
			'label' => 'txn_id',
			'value' => time()
		));
		
		$this->addElement('Text', 'custom', array(
			'label' => 'custom (package id)',
		));

		$this->addElement('Text', 'first_name', array(
			'label' => 'first_name',
			'value' => 'Tester'
		));
		
		$this->addElement('Text', 'last_name', array(
			'label' => 'last_name',
			'value' => 'Sceptre'
		));
		
		$this->addElement('Text', 'payer_email', array(
			'label' => 'payer_email',
			'value' => 'payer@test.com'
		));		
		
		$this->addElement('Text', 'mc_gross', array(
			'label' => 'mc_gross',
			'value' => '1.23'
		));	

		$this->addElement('Text', 'mc_currency', array(
			'label' => 'mc_currency',
			'value' => 'USD'
		));		

    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit Data',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addDisplayGroup(array(
      'submit',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
    

  }
 
  
}