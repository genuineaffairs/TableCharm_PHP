<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Widget_ClinicalFieldsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $css_files = array('main.css', 'grid-fields.css');
    foreach($css_files as $file) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . "application/modules/Zulu/externals/css/{$file}");
    }

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
//    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
//      return $this->setNoRender();
//    }
    if (!Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($subject, $viewer, 'view_clinical')) {
      return $this->setNoRender();
    }

    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $spec = Engine_Api::_()->getDbTable('zulus', 'zulu')->getZuluByUserId($subject->getIdentity());

    $this->view->subject = $spec;

    // Values
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($spec);
    if (count($fieldStructure) <= 1) { // @todo figure out right logic
      return $this->setNoRender();
    }
  }

}
