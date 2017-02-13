<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Edit extends Communityad_Form_Admin_Create {

  public $_error = array();
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

    $this->setTitle('Edit Ad Package')
            ->setDescription("Edit your advertisement package over here. Below, you can configure various settings for this package like advertised content, ad placement, pricing model, etc. Please note that payment parameters (Price, Pricing Model) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.");

    // DISABLE TYPE
    $this->getElement('type')
            ->setIgnore(true)
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);
    // DISABLE PRICE
    $this->getElement('price')
            ->setIgnore(true)
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);

    // DISABLE PRICE MODE
    $this->getElement('price_model')
            ->setIgnore(true)
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);

    $this->submit->setLabel('Edit Package');

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    if ($this->_item->type == 'sponsored_stories') {
      $this->removeElement('sponsored');
      $this->removeElement('featured');
     // $this->removeElement('network');
      $this->removeElement('public');
      $multiOptions=$this->urloption->getMultiOptions();
      unset ($multiOptions['website']);
      $this->urloption->setMultiOptions($multiOptions);
      $this->urloption->setDescription('Select the content types that you want to be advertised in this package. (Press Ctrl and click to select multiple types.)');
    }

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}