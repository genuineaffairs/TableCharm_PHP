<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Form_Photo_Edit extends Engine_Form {

  protected $_item;

  public function getItem() {

    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {

    $this->_item = $item;
    return $this;
  }

  public function init() {
    //GET PAGE ID
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);

    //GET TAB ID
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GET VIEW
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //GET URL
    $url = $view->item('sitepage_page', $page_id)->getHref(array('tab' => $tab_id));
    parent::init();
    $this->setTitle('Edit Event')
            ->setDescription('Edit the information of your event using the form below.');
    $this->addElement('Radio', 'cover', array(
        'label' => 'Album Cover',
    ));
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => $url,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}

?>