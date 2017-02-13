<?php

class Zulu_IndexController extends Core_Controller_Action_Standard {

  public function init() {
    parent::init();

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    // Init JS file
    $jsFiles = array('jquery.js', 'bootstrap.min.js');
    foreach ($jsFiles as $file) {
      $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/js/' . $file);
    }

    // Init CSS file
    $cssFiles = array('bootstrap.min.css', 'bootstrap-theme.min.css', 'main.css');
    foreach ($cssFiles as $file) {
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/css/' . $file);
    }

    // Init Meta tag
    $view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1');
  }

  public function indexAction() {
    $this->view->someVar = 'someVal';
  }

  public function addPeopleAction() {
    // Get access_type of clicked links
    $this->view->access_type = $this->_getParam('access_type');
    
    $this->view->form = $form = new Zulu_Form_InviteMembers();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Remove form
    $this->view->form = null;
    
    $this->view->user_ids = $this->_getParam('toValues');

//    return $this->_forwardCustom('success', 'utility', 'core', array(
//                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully added to your sharing list.')),
//                'layout' => 'default-simple',
//    ));
  }

  public function getMembersAction() {
    $data = array();
    $user_id = null;

    $subject = Engine_Api::_()->user()->getViewer();

    // Id of current user
    if (isset($subject->user_id) && is_numeric($subject->user_id)) {
      $user_id = $subject->user_id;
    }

    /* @var $usersTable User_Model_DbTable_Users */
    $usersTable = Engine_Api::_()->getDbTable('users', 'user');
    $usersTableName = $usersTable->info('name');

    // Select user list for sharing options
    $select = $usersTable->select()
            ->where('verified = ?', 1)
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('user_ids', null) . '%')
            ->order('displayname');
    
    Engine_Api::_()->getApi('core', 'sharedResources')->addSiteSeprationCondition($select);

    if ($user_id !== null) {
      $select->where($usersTableName . '.user_id != ?', $user_id);
    }

    $users = $usersTable->fetchAll($select);

    foreach ($users as $user) {
      $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
      $data[] = array(
          'id' => $user->user_id,
          'label' => $user->displayname,
          'photo' => $user_photo,
      );
    }

    return $this->_helper->json($data);
  }

  public function getMembersByIdAction() {
    $data = array();
    $user_id = null;
    $user_ids = explode(',', $this->_getParam('user_ids'));

    $subject = Engine_Api::_()->user()->getViewer();

    // Id of current user
    if (isset($subject->user_id) && is_numeric($subject->user_id)) {
      $user_id = $subject->user_id;
    }

    /* @var $usersTable User_Model_DbTable_Users */
    $usersTable = Engine_Api::_()->getDbTable('users', 'user');

    // Select user list for sharing options
    $select = $usersTable->select()
            ->where('verified = ?', 1)
            ->where('user_id IN (?)', (array)$user_ids)
            ->order('displayname');

    if ($user_id !== null) {
      $select->where('user_id != ?', $user_id);
    }

    $users = $usersTable->fetchAll($select);

    foreach ($users as $user) {
      $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
      $data[] = array(
          'id' => $user->user_id,
          'label' => $user->displayname,
          'photo' => $user_photo,
      );
    }

    return $this->_helper->json($data);
  }

  protected function _forwardCustom($action, $controller = null, $module = null, array $params = null) {
    // Parent
    $request = $this->getRequest();

    if (null !== $params) {
      $request->setParams($params);
    }

    if (null !== $controller) {
      $request->setControllerName($controller);

      // Module should only be reset if controller has been specified
      if (null !== $module) {
        $request->setModuleName($module);
      }
    }

    $request->setActionName($action);
    if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
      $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
    }
    $request->setDispatched(false);
  }

}
