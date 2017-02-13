<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Form_Custom_Fields extends Fields_Form_Standard
{
  public $_error = array();

  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';

  public function init()
  { 
    if( !$this->_item ) {
      $document_item = new Document_Model_Document(array());
      $this->setItem($document_item);
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
        ->addDecorator('FormElements');
    }
  }
}