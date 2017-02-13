<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mgsl_Widget_CirclesGridController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    //Get Main and Quick Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('advgroup_main');
    
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
         ->getNavigation('advgroup_quick');

    //Create & modify search form.
    $this->view->form = $search_form = new Advgroup_Form_Search();
    $search_form->removeElement('view');
    $search_form->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '0' => 'All My Groups',
        '2' => 'Only Groups I Lead',
      ),
      'onchange' => '$(this).getParent("form").submit();',
    ));
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search_form->isValid($request->getParams());
    $params = $search_form->getValues();
    
    //Get form values
    $this->view->formValues = $params = $search_form->getValues();
    
    //Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    //Filter parameters
    if($params['view'] != "2"){
        $memTable = Engine_Api::_()->getDbtable('membership', 'advgroup');
        $select = $memTable->select()
                    ->where('user_id = ?',$viewer->getIdentity())
                    ->where('active = 1');
        $memberships = $memTable->fetchAll($select);
        $group_ids = array(0);
        foreach($memberships as $membership) $group_ids[] = $membership->resource_id;
        $params['group_ids'] = $group_ids;
    }
    else {
        $params['user_id'] = $viewer->getIdentity();
    }

    //Get data
    $this->view->paginator = $paginator =  Engine_Api::_()->getItemTable('group')->getGroupPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $itemsPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.page', 10);
    $paginator->setItemCountPerPage($itemsPerPage);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
