<?php

class Mgsl_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  
  public function printSharingMedicalAction() {
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }
    if( !$this->_executeSearch() ) {
      // throw new Exception('error');
    }
    $this->_helper->layout->disableLayout();
  }
  
  protected function _executeSearch()
  {
    // Check form
    $form = new User_Form_Search(array(
      'type' => 'user'
    ));
    // Get viewer object
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$form->isValid($this->_getAllParams()) ) {
      $this->view->error = true;
      $this->view->totalUsers = 0; 
      $this->view->userCount = 0; 
      $this->view->page = 1;
      return false;
    }

    $this->view->form = $form;

    // Get search params
    $page = (int)  $this->_getParam('page', 1);
    $ajax = (bool) $this->_getParam('ajax', false);
    $options = $form->getValues();
    
    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach( $options as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    //extract($options); // displayname
    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }
    
    // Contruct query
    $select = $table->select()
      //->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      //->group("{$userTableName}.user_id")
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1);
//      ->order("{$userTableName}.displayname ASC");
      
//    if($this->_getParam('medical_record_shared')) {
    $select->join('engine4_zulu_profileshare', "`engine4_zulu_profileshare`.`subject_id` = `{$userTableName}`.`user_id`", null)
      ->where('`engine4_zulu_profileshare`.`viewer_id` = ?', $viewer->getIdentity());
//    }
      
    if(isset($options['order'])) {
      switch($options['order']) {
        case 'recent':
          $select->order("{$userTableName}.creation_date DESC");
          break;
        case 'alphabet':
          $select->order("{$userTableName}.displayname ASC");
          break;
      }
    } else {
      $select->order("{$userTableName}.displayname ASC");
    }

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }
    
    if($this->_getParam('user_type') === 'friends')
    {
      $userMembershipTable = Engine_Api::_()->getDbTable('membership', 'user');
      $select->setIntegrityCheck(false)->join(array('um' => $userMembershipTable->info('name')), "`{$userTableName}`.`user_id` = `um`.`user_id`");
      $select->where('um.active = 1')->where('um.resource_id = ?', $viewer->getIdentity());
      $this->view->is_friend_list = true;
    }
    
    if($this->_getParam('participation_level')) {
      $valueTable = Engine_Api::_()->fields()->getTable('user', 'values');
      $valueTableName = $valueTable->info('name');
      
      $participationLevelField = Engine_Api::_()->user()->getParticipationLevelField();
      $participationLevelValue = $this->_getParam('participation_level');
      
      $select->join($valueTableName, "`{$userTableName}`.`user_id` = `{$valueTableName}`.`item_id`", null);
      $select->where("`{$valueTableName}`.`field_id` = ?", $participationLevelField->field_id);
      $select->where("`{$valueTableName}`.`value` = ?", $participationLevelValue);
    }
    
    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(1000);
    $paginator->setCurrentPageNumber($page);
    
    $this->view->page = $page;
    $this->view->ajax = $ajax;
    $this->view->users = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    $this->view->topLevelId = $form->getTopLevelId();
    $this->view->topLevelValue = $form->getTopLevelValue();
    $this->view->formValues = array_filter($originalOptions);

    return true;
  }
}
