<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fields.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Form_Signup_ProfileFields extends Zulu_Form_Common_ProfileFields_Abstract {

  protected $_responsiveBlockClass = 'col-xs-12 col-md-12';

  public function init() {
    // Init form
    $this->setTitle('Profile Information');

    $this
            ->setIsCreation(true)
            ->setItem(Engine_Api::_()->user()->getUser(null))
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('id', 'SignupForm');

    parent::init();
  }

  public function generate() {
    $this->_editUserHelper->setFormClass('Zulu_Form_Signup_Photo');

    parent::generate();

    $this->addElement('Button', 'next', array(
        'label' => 'Continue',
        'type' => 'submit',
        'order' => 10000,
    ));
  }

}
