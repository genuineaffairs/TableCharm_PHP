<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynevent_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
  	$this
		->addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator')
		->addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element')
		->addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');
		
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
	
	$ynevent_day = new Ynevent_Form_Element_YnCalendarSimple('ynevent_day');
    $ynevent_day -> setLabel("Maximum end day repeat");
    $ynevent_day -> setAllowEmpty(true);  
	$ynevent_day -> setValue(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.day', ''));
	$this -> addElement($ynevent_day);
	/*	
	$this->addElement('Text', 'ynevent_day',array(
		      'label'=>'Maximum end day repeat',
		      'title' => '',  
		      'description' => '',
		      'filters' => array(
		        new Engine_Filter_Censor(),
		      ),
		     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.day', ''),
	 )); 
	*/   
	$this->addElement('Text', 'ynevent_instance',array(
		      'label'=>'Maximum instances of each repeat events',
		      'title' => '',  
		      'description' => '',
		      'filters' => array(
		        new Engine_Filter_Censor(),
		      ),
		     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.instance', 50),
	    ));	
	$this->addElement('Text', 'ynevent_google_api',array(
		      'label'=>'Google API key',
		      'title' => '',  
		      'description' => '',
		      'filters' => array(
		        new Engine_Filter_Censor(),
		      ),
		     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.google.api', ''),
	    ));	
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}