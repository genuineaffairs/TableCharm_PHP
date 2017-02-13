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
class Resume_Form_Element_Map extends Zend_Form_Element
{
  protected $_item;


  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }	
	
  public function render(Zend_View_Interface $view = null)
  {
    $this->removeDecorator('ViewHelper');
    if (null !== $view) {
      $this->setView($view);
    }

	  //$map_options = array('width' => 400,'height' => 360, 'draggable'=>true);
	  //echo $this->radcodes()->map()->item($this->resume, $map_options);    
    
    $attributes = $this->getAttribs();
    $content = $this->getView()->radcodes()->map()->item($this->getItem(), $attributes);
    foreach ($this->getDecorators() as $decorator) {
      $decorator->setElement($this);
      $content = $decorator->render($content);
    }
    return $content;
  }
  
  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      Engine_Form::addDefaultDecorators($this);
    }
  }
}