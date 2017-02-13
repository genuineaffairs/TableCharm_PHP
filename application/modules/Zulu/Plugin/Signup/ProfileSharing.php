<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Plugin_Signup_ProfileSharing extends Zulu_Plugin_Common_ProfileSharing_Abstract {

    protected $_formClass = 'Zulu_Form_Signup_ProfileSharing';
    
    public function onSubmit(Zend_Controller_Request_Abstract $request) {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (null === $request->getPost('profileshare_submit')) {
            $this->getSession()->active = true;
            $this->onSubmitNotIsValid();
            return false;
        }

        return parent::onSubmit($request);
    }
    
    /**
     * Get user id from plugin's session
     * 
     * @return int
     */
    public function getUserId() {
        return $this->_registry->user->user_id;
    }
}
