<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Folder_Widget_SearchFormController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->form = $form = new Folder_Form_Filter_Browse();

    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'browse'), 'folder_general', true));

    $request = Zend_Controller_Front::getInstance()->getRequest();
  	$params = $request->getParams();
  	
  	foreach (array('action','module','controller','rewrite') as $system_key) {
  	  unset($params[$system_key]);
  	}    
    
    // Populate form data
    if( $form->isValid($params) )
    {
      $params = $form->getValues();
    }    
    
  }

}