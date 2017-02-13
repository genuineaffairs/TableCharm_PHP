<?php

class Mgslapi_Controller_Action_Helper_ResumeAPI extends Zend_Controller_Action_Helper_Abstract {

  public function getResumeBasicInfo($resume) {

    if (!($resume instanceof Core_Model_Item_Abstract)) {
      return array();
    }
    
    $view = Zend_Registry::get('Zend_View');

    return array(
        'id' => $resume->getIdentity(),
        'type' => $resume->getType(),
        'title' => $resume->getTitle(),
        'thumb_photo' => $resume->getPhotoUrl('thumb.normal'),
        'sport' => $resume->getFieldValueString('Sport'),
        'participation_level' => $resume->getCategory()->getTitle(),
        'creation_date' => strip_tags($view->timestamp($resume->creation_date)),
        'posted_by' => $this->getActionController()->getHelper('commonAPI')->getBasicInfoFromItem($resume->getOwner()),
        'description' => $resume->getDescription(),
        'view_count' => $resume->view_count,
    );
  }
  
  public function removeFieldsFromFieldStructureByLabels(&$fieldStructure, $labels) {
    if (!is_array($labels)) {
      $labels = array($labels);
    }

    $metaData = Engine_Api::_()->fields()->getFieldsMeta('resume');

    $fieldIds = array();
    foreach ($labels as $label) {
      $fieldIds[] = $metaData->getRowMatching(array('label' => $label))->field_id;
    }
    
    foreach($fieldStructure as $key => $map) {
      if(in_array(explode('_', $key)[2], $fieldIds)) {
        unset($fieldStructure[$key]);
      }
    }
  }

}
