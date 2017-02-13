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
class Zulu_Form_Edit_ProfileFields extends Zulu_Form_Common_ProfileFields_Abstract {

    public function init() {
        // Init form
        $this->setTitle('Profile Information');

        $this->setAttrib('enctype', 'multipart/form-data')->setAttrib('id', 'EditProfile');

        parent::init();
    }

    public function generate() {

        parent::generate();

        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
            'order' => 10000,
        ));
    }

}
