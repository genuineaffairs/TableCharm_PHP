<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Resume_Widget_ProfileDetailsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->resume = $subject = Engine_Api::_()->core()->getSubject('resume');
    
    if( !($subject instanceof Resume_Model_Resume) ) {
      return $this->setNoRender();
    }    
        
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);     
    
    $this->_removeFieldsFromFieldStructureByLabels($fieldStructure, array('Gender', 'Date of Birth'));
    
    $this->view->fieldValues = $this->view->fieldValueLoop($subject, $fieldStructure);
    
    if (!$this->view->fieldValues) {
      return $this->setNoRender();
    }
  }
  
  protected function _removeFieldsFromFieldStructureByLabels(&$fieldStructure, $labels) {
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