<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileAdvancedsearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $zendInstance = Zend_Controller_Front::getInstance();
    $request = $zendInstance->getRequest();
    $this->view->params = $p = $request->getParams();
    $this->view->action = $action = $zendInstance->getRouter()->assemble(array());
    if ($p['module'] == 'ynforum')
      $p['module'] = 'forum';
    $this->view->pageName = $pageName = $module_controller_action = $p['module'] . '_' . $p['controller'] . '_' . $p['action'];
    $widgetParams = $this->_getAllParams();

    if (isset($widgetParams['module_search']) && $widgetParams['module_search'] && Engine_Api::_()->sitemobile()->isSupportedModule($widgetParams['module_search'])) {
      $pageName = $widgetParams['module_search'] . "_index_home";
    }
    if (!isset($widgetParams['search'])) {
      $widgetParams['search'] = 2;
    }
//     if ($this->checkIfInArrayString($p, 'category_id') || $this->checkIfInArrayString($p, 'badge_id')) {
//       $widgetParams['search'] = 3;
//     }

    $params['name'] = $pageName;
    $this->view->searchRow = $searchRow = Engine_Api::_()->getDbtable('searchform', 'sitemobile')->getSearchForm($params);

    if (!empty($searchRow)) { 
      $params = array();
      $className = $searchRow->class;
      if (!empty($searchRow->params)) {
        $params = Zend_Json_Decoder::decode($searchRow->params);
      }
      $params['hasMobileMode'] = true;
      
      //FOR SITEEVENT BY DEFAULT BROWSE BY STARTTIME
      if($p['module'] == 'siteevent'){
        $p['orderby'] = $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
          $p['orderby'] = $this->_getParam('orderby', 'starttime');
        }
      }
    
      $this->view->form = $form = new $className($params);
      $this->view->form->populate($p);
      $this->view->searchField = $searchRow->search_filed_name;
      $this->view->search = $request->getParam($searchRow->search_filed_name, null);
//      if (!empty($searchRow->action) && 0) {
//        $action = Zend_Json_Decoder::decode($searchRow->action);
//        $route = $action['route'];
//        if ($route == 'sitereview_general_listtype_1')
//          $route = 'sitereview_general_listtype_' . $p['listingtype_id'];
//        unset($action['route']);
//        $reset = true;
//        if (isset($action['reset'])) {
//          $reset = $action['reset'];
//          unset($action['reset']);
//        }
//        $this->view->action = Zend_Controller_Front::getInstance()->getRouter()
//                ->assemble($action, $route, $reset);
//      } else {
      $this->view->action = $form->getAction();
      //     }
    } elseif ($module_controller_action == 'messages_messages_inbox' || $module_controller_action == 'messages_messages_outbox' || $module_controller_action == 'messages_messages_search') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array('action' => 'search'));
      $widgetParams['search'] = 1;
    } elseif ($module_controller_action == 'forum_index_index') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array('controller' => 'search'), 'default', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'query';
    } elseif ($module_controller_action == 'suggestion_index_viewfriendsuggestion') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array(), 'user_extended', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'displayname';
    } elseif ($module_controller_action == 'peopleyoumayknow_index_index') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array(), 'user_extended', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'displayname';
    } else {
      // check public settings
      $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
      if (!$require_check) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity())
          return $this->setNoRender();
      }
      $this->view->form = $form = new Sitemobile_modules_Core_Form_Filter_Search();
      $this->view->action = 'search';
      // $widgetParams['search'] = 3;
    }

    $this->view->widgetParams = $widgetParams;
    if (isset($widgetParams['location']) && $widgetParams['location']) {
      $this->view->locationFieldName = 'location';
      if (in_array($p['module'], array('sitepage', 'sitegroup', 'sitebusiness', 'sitestore'))) {
        $this->view->locationFieldName = $p['module'] . "_" . $this->view->locationFieldName;
      }
      $this->view->location = $request->getParam($this->view->locationFieldName, null);
    }
    //FIELDS ADDED FOR GEOGRAPHICAL SEARCH - SITEEVENT HOME
    $this->view->locationmiles = $request->getParam($this->view->locationmiles, '1000');
    $this->view->category_id = $request->getParam($this->view->category_id, 0);
    
//    foreach ($this->view->form->getElements() as $element){
//      if($element->getType()=='Engine_Form_Element_Text'){
//        $element->setAttrib("placeholder",$element->getLabel());
//        $element->setLabel('');
//      }
//    }

    $reqview_selected = Zend_Controller_Front::getInstance()->getRequest()->getParam('view_selected');
    if ($reqview_selected && $this->view->form) {
      $this->view->form->addElement('Hidden', 'view_selected', array(
          'value'=>$reqview_selected
      ));
    }
  }

//  function checkIfInArrayString($array, $searchingFor) {
//
//    foreach ($array as $key => $element) {
//      if (strpos($key, $searchingFor) !== false) {
//        return true;
//      }
//    }
//    return false;
//  }

}