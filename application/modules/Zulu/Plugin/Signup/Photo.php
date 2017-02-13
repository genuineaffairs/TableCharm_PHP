<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photo.php 10044 2013-05-15 17:45:46Z andres $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Plugin_Signup_Photo extends User_Plugin_Signup_Photo {

    protected $_formClass = 'Zulu_Form_Signup_Photo';
    protected $_script = array('signup/form/photo.tpl', 'zulu');

    public function onSubmit(Zend_Controller_Request_Abstract $request) {
        // Form was valid
        if ($this->getForm()->isValid($request->getPost())) {
            
            // Store form value in Session
            $this->getSession()->data = $this->getForm()->getValues();
            
            // Save file(s)
            $this->getSession()->Filedata = $this->getForm()->Filedata->getFileInfo();
            $this->_resizeImages($this->getForm()->Filedata->getFileName());
            
            $this->setActive(false);
            $this->onSubmitIsValid();
            return true;
        }

        // Form was not valid
        else {
            $this->getSession()->active = true;
            $this->onSubmitNotIsValid();
            return false;
        }
    }

}
