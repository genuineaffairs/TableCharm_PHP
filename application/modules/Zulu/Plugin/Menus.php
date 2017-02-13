<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Menus.php 9770 2012-08-30 02:36:05Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Plugin_Menus {

  // core_main
  function onMenuInitialize_ZuluEditSharing($row) {
    $subject = Engine_Api::_()->core()->getSubject('user');
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject->authorization()->isAllowed($viewer, 'edit')) {
      return true;
    } else {
      return false;
    }
  }

  function onMenuInitialize_ZuluEditProfile($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    
    if ($subject->authorization()->isAllowed($viewer, 'edit')) {
      return true;
    } else {
      return false;
    }
  }

  function onMenuInitialize_ZuluEditClinical($row) {
    return true;
  }

  public function onMenuInitialize_UserProfileEdit($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $profileEditMenu = User_Plugin_Menus::onMenuInitialize_UserProfileEdit($row);

    if (empty($profileEditMenu)) {
      
      if(Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($subject, $viewer, 'view_clinical')) {
        // Set selected tab as Medical Record on profile viewing page
//        $db = Engine_Db_Table::getDefaultAdapter();
//        $tab_id = $db->select()
//                ->from('engine4_core_content', 'content_id')
//                ->where('`name` = ?', 'zulu.clinical-fields')
//                ->query()
//                ->fetchColumn();
//        Zend_Controller_Front::getInstance()->getRequest()->setParam('tab', $tab_id);
      }
      
      if (Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($subject, $viewer, 'edit') === Authorization_Api_Core::LEVEL_MODERATE) {
        // Add Edit Medical Record link (under profile photo)
        $label = 'Edit Medical Record';

        return array(
            'label' => $label,
            'icon' => 'application/modules/User/externals/images/edit.png',
            'route' => 'zulu_extended',
            'params' => array(
                'controller' => 'edit',
                'action' => 'clinical',
                'id' => ( $viewer->getGuid(false) == $subject->getGuid(false) ? null : $subject->getIdentity() ),
            )
        );
      } else {
        return false;
      }
    } else {
      return $profileEditMenu;
    }
  }
  
  public function onMenuInitialize_ZuluClinicalEdit($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $label = "Edit My Medical Record";
    if( !$viewer->isSelf($subject) ) {
      $label = "Edit Member Medical Record";
    }

    if( $subject->authorization()->isAllowed($viewer, 'edit') ) {
      return array(
        'label' => $label,
        'icon' => 'application/modules/User/externals/images/edit.png',
        'route' => 'zulu_extended',
        'params' => array(
          'controller' => 'edit',
          'action' => 'clinical',
          'id' => ( $viewer->getGuid(false) == $subject->getGuid(false) ? null : $subject->getIdentity() ),
        )
      );
    }

    return false;
  }

}
