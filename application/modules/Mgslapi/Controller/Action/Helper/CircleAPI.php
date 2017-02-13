<?php

class Mgslapi_Controller_Action_Helper_CircleAPI extends Zend_Controller_Action_Helper_Abstract {

  public function getCircleBasicInfo($sitepage) {

    if (!($sitepage instanceof Core_Model_Item_Abstract)) {
      return array();
    }
    
    $view = Zend_Registry::get('Zend_View');

    return array(
        'id' => $sitepage->getIdentity(),
        'type' => $sitepage->getType(),
        'title' => $sitepage->getTitle(),
        'thumb_photo' => $sitepage->getPhotoUrl('thumb.normal'),
        'creation_date' => strip_tags($view->timestamp($sitepage->creation_date)),
        'posted_by' => $this->getActionController()->getHelper('commonAPI')->getBasicInfoFromItem($sitepage->getOwner()),
        'location' => Zend_Registry::get('Zend_Translate')->_($sitepage->location),
        'description' => $sitepage->body,
        'comment_count' => $sitepage->comment_count,
        'member_count' => $sitepage->member_count,
        'view_count' => $sitepage->view_count,
        'like_count' => $sitepage->like_count,
    );
  }

}
