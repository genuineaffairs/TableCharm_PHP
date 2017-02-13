<?php
class Advgroup_Widget_GroupsListingController extends Engine_Content_Widget_Abstract
{
 public function indexAction(){
      //Get fields for filtering group search.
     $request = Zend_Controller_Front::getInstance()->getRequest();
     $form = new Advgroup_Form_Search();
     $form->isValid($request->getParams());
     $this->view->formValues = $params = $form->getValues();
     $params['search'] = '1';
     
      // Viewer 's friends field.
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() && $params['view']) {
          $params['users'] = array();
          foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
            $params['users'][] = $memberinfo->user_id;
          }
          if(empty($params['users'])) $params['users'][] = 0;
      }
      $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('group') ->getGroupPaginator($params);

      //Set curent page
      $itemsPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.page', 10);
      $paginator->setItemCountPerPage($itemsPerPage);
      $paginator->setCurrentPageNumber($params['page']);

 }
}