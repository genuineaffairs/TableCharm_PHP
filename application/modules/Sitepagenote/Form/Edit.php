<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Form_Edit extends Sitepagenote_Form_Create {

  protected $_item;

  public function getItem() {
    
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    
    $this->_item = $item;
    return $this;
  }

  public function init() {
    
    parent::init();
    $this->setTitle('Edit Note')
            ->setDescription('Edit the information of your note using the form below.');
    $this->addElement('Radio', 'cover', array(
        'label' => 'Album Cover',
    ));
    $this->execute->setLabel('Save Changes');
  }

}

?>