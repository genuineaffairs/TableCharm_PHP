<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photo.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Form_Signup_Photo extends Engine_Form {

    public function init() {
        $orderIndex = 0;
        
        // Init form
//        $this->setTitle('Add Your Photo');

        $this
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('id', 'SignupForm');

        // Photo Upload -->
        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formSignupImage.tpl',
                        'class' => 'form element'
                    ))),
            'order' => $orderIndex++
        ));
        Engine_Form::addDefaultDecorators($this->current);

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'description' => 'Maximise your exposure by uploading a profile picture',
            'multiFile' => 1,
            'validators' => array(
                array('Count', false, 1),
                array('Extension', false, 'jpg,png,gif,jpeg'),
            ),
            'onchange' => 'javascript:uploadSignupPhoto();',
            'order' => $orderIndex++
        ));

        $this->addElement('Hash', 'token', array('timeout' => 600));

        $this->addElement('Hidden', 'coordinates', array(
            'order' => $orderIndex++
        ));
        $this->addElement('Hidden', 'uploadPhoto', array(
            'order' => $orderIndex++
        ));
        // <-- Photo Upload
    }
    
    public function isValid($data) {
      
      if(!$this->getElement('token')->isValid($data['token'])) {
        $this->getElement('token')->setErrors(array('The registration page has been left idle for too long. Please try to submit the registration form again.'));
        return false;
      }
      
      return parent::isValid($data);
    }

}
