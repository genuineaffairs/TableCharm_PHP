<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Form_Custom_Fields extends Fields_Form_Standard {

  public $_error = array();
  protected $_name = 'fields';
  protected $_elementsBelongTo = 'fields';

  public function init() {
    // custom sitepagedocument fields
    if (!$this->_item) {
      $sitepagedocument_item = new Sitepagedocument_Model_Document(array());
      $this->setItem($sitepagedocument_item);
    }
    parent::init();

    $this->removeElement('submit');
  }

  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this
              ->addDecorator('FormElements')
      ; //->addDecorator($decorator);
    }
  }

}
?>