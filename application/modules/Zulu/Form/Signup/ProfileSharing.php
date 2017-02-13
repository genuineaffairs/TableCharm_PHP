<?php

class Zulu_Form_Signup_ProfileSharing extends Zulu_Form_Common_ProfileSharing_Abstract {

    protected $_submitLabel = 'Continue';
    
    protected $_formAction = 'user_signup';
    
    public function getUserId() {
        // Do not need to acquire user id here
        return null;
    }
    
//    public function getUserSelectedList($user_id = null) {
//        $reversedAccessTypeMap = array_flip(Zulu_Model_DbTable_AccessLevel::$accessTypeMap);
//        
//        return array(
//            $reversedAccessTypeMap[Zulu_Model_DbTable_AccessLevel::FULL] => array(),
//            $reversedAccessTypeMap[Zulu_Model_DbTable_AccessLevel::READ_ONLY] => array(),
//            $reversedAccessTypeMap[Zulu_Model_DbTable_AccessLevel::LIMITED] => array(),
//        );
//    }
    
    public function init() {
        parent::init();
        $this->addElement('Hidden', 'profileshare_submit', array('value' => 1));
    }
}
