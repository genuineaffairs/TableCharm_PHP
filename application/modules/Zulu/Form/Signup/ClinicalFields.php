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
class Zulu_Form_Signup_ClinicalFields extends Zulu_Form_Common_ClinicalFields_Abstract {

    public function init() {
        // Init form
        if (!$this->_item) {
            $this->setItem(new Zulu_Model_Zulu(array()));
        }

        parent::init();
    }

    public function generate() {
        parent::generate();
        
        $this->addElement('Hidden', 'clinical_submit', array('value' => 1));
        
        $this->addElement('Button', 'submit', array(
            'label' => 'Continue',
            'type' => 'submit',
            'order' => 10001,
        ));
    }

}
