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

class Epayment_TestController extends Core_Controller_Action_Standard
{
  public function init()
  {
      
  }

  public function ipnAction()
  {
    
  	$viewer = Engine_Api::_()->user()->getViewer();
  	
  	if (!$viewer->isAdmin())
  	{
  	  return $this->_forward('requireauth', 'error', 'core');
  	}
  	
  	$this->view->form = $form = new Epayment_Form_Test_Ipn();
  	$form->setAction($this->getFrontController()->getRouter()->assemble(array('action'=>'notify'), 'epayment_general', true));
  	
  }
  
}