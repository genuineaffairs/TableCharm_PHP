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
 
 
 
class Resume_Form_Custom_Fields extends Fields_Form_Standard
{
  public $_error = array();

  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';

  public function init()
  { 
    // custom resume fields
    if (!$this->_item){
      $resume_item = new Resume_Model_Resume(array());
      $this->setItem($resume_item);
    }
    parent::init();

    $this->removeElement('submit');
  }

  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ; //->addDecorator($decorator);
    }
  }
}