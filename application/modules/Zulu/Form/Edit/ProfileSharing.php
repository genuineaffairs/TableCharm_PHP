<?php

class Zulu_Form_Edit_ProfileSharing extends Zulu_Form_Common_ProfileSharing_Abstract {

    protected $_submitLabel = 'Save';
    
    public function getUserId() {
        return Engine_Api::_()->zulu()->getSigninUserId();
    }

//    public function getUserSelectedList($user_id = null) {
//        $shareUserSelectedList = array();
//
//        if ($user_id !== null) {
//            $reversedAccessTypeMap = array_flip(Zulu_Model_DbTable_AccessLevel::$accessTypeMap);
//
//            $profileShareTable = Engine_Api::_()->getDbTable('profileshare', 'zulu');
//            
//            $db = $profileShareTable->getAdapter();
//            $result = $db->fetchAll("SELECT access_level, viewer_id FROM {$profileShareTable->info('name')} WHERE subject_id = ?", array($user_id));
//            
//            $accessList = $this->convertAccessList($result);
//
//            $shareUserSelectedList = array(
//                $reversedAccessTypeMap[Zulu_Model_DbTable_AccessLevel::FULL] => $accessList[Zulu_Model_DbTable_AccessLevel::FULL],
//                $reversedAccessTypeMap[Zulu_Model_DbTable_AccessLevel::READ_ONLY] => $accessList[Zulu_Model_DbTable_AccessLevel::READ_ONLY],
//                $reversedAccessTypeMap[Zulu_Model_DbTable_AccessLevel::LIMITED] => $accessList[Zulu_Model_DbTable_AccessLevel::LIMITED],
//            );
//        }
//
//        return $shareUserSelectedList;
//    }
    
    /**
     * Convert fetched data from db to array[access_level] = array(viewer_id,...)
     * 
     * @param array $result
     * 
     * @return array
     */
    public function convertAccessList($result = array()) {
        $accessList = array();
        
        foreach($result as $item) {
            $accessList[$item['access_level']][] = $item['viewer_id'];
        }
        
        return $accessList;
    }
}
