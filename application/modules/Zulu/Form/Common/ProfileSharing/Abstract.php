<?php

abstract class Zulu_Form_Common_ProfileSharing_Abstract extends Engine_Form {

  protected $_accessLevel = array(
      'full' => array(
          'label' => 'Full access to all sections of Medical Record',
          'description' => 'ie. Parents, Guardians and Medicos who you give permission to edit, modify and print your Medical Record on your behalf. (International Travel Insurance, Next of Kin, Blood Type, Medications, Concussion Reports, Allergies, Overseas Travel, Immunisations, Personal History, Physical History, Family History, Medical Imaging & Reports)'),
      'read_only' => array(
          'label' => 'Read-only access to all sections of Medical Record',
          'description' => 'ie. Parents, Coaches, Teachers and Medicos who can read and print your complete Medical Record but cannot edit it. (International Travel Insurance, Next of Kin, Blood Type, Medications, Concussion Reports, Allergies, Overseas Travel, Immunisations, Personal History, Physical History, Family History, Medical Imaging & Reports)'),
      'limited' => array(
          'label' => 'Emergency Summary of Medical Record',
          'description' => 'ie. Anyone that you nominate to view and print the following components of your Medical Record: International Travel Insurance, Next of Kin, Blood Type, Medications, Concussion Reports, Allergies, Overseas Travel and Immunisations.'),
  );
  protected $_submitLabel = null;
  protected $_formAction = null;
  protected $_actionParams = array();

  /**
   * Get user id from db (for registered user), from session (for sign up user)
   * 
   * @return int user_id
   */
  abstract public function getUserId();

  /**
   * Re-populate selected item in access list from db
   * 
   * @return array = array(array[access_level_1], array[access_level_2], ...)
   */
//  abstract public function getUserSelectedList($user_id = null);

  /**
   * Get user's access list from db
   */
  protected function _getUserPreselectedAccessList() {
    $access_list = array();

    // If user logged in, get user's pre-selected profile share data
    if (Engine_Api::_()->core()->hasSubject('user')) {
      $user = Engine_Api::_()->core()->getSubject('user');
      if (is_numeric($user->user_id)) {
        $profileShareTable = Engine_Api::_()->getDbTable('profileshare', 'zulu');
        $access_list = $profileShareTable->getAccessListOfUser($user->user_id);
        
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if(is_null($view)) {
          return $access_list;
        }

        if (!empty($access_list)) {
          // Prepend pre-selected profile share list
          $access_list_json = json_encode($access_list);

          // Prepend javascript variable to be used in onAddPeople function
          $view->headScript()->offsetSetScript(0, "var access_list = {$access_list_json};");
        } else {
          // Prepend javascript variable to be used in onAddPeople function
          $view->headScript()->offsetSetScript(0, "var access_list = '';");
        }
      }
    }
    return $access_list;
  }

  public function init() {

    $access_list = $this->_getUserPreselectedAccessList();

    // Init form
    $this->setTitle('My Medical Record Sharing Preferences')->setName('profile_sharing_form');
    
    $note = new Zulu_Form_Element_Note(
            'disclaimer_text', array(
        'value' => $this->getTranslator()->translate('Your medical information is sensitive and personal to you. You should only share it with people you trust, and who you are satisfied will not share your information with anyone else without your consent. We take no responsibility, and have no liability for any consequences arising from you sharing this information.'),
        'order' => -1
    ));
    $this->addElement($note);

    if (!Engine_Api::_()->zulu()->isMobileMode()) {
      $this->_addPCControls();
    } else {
      $this->_addMobileControls($access_list);
    }

    $this->addElement('Button', 'next', array(
        'label' => $this->_submitLabel,
        'type' => 'submit',
        'order' => 10000,
    ));

    // Set default action
    if (!is_null($this->_formAction)) {
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($this->_actionParams, $this->_formAction, true));
    }
  }

  protected function _addPCControls() {
    $orderIndex = 0;

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if (!is_null($view)) {
      $baseUrl = $view->layout()->staticBaseUrl;
    }

    // Open wrapper HTML Tag
    $noteName = 'custom_element_' . $orderIndex;
    $note = new Zulu_Form_Element_Note(
            $noteName, array(
        'value' => '<div class="access_list_wrapper">',
        'order' => $orderIndex++,
    ));
    $note->removeDecorator('HtmlTag')->removeDecorator('HtmlTag2');
    $this->addElement($note);

    foreach ($this->_accessLevel as $key => $levelInfo) {
      // Frame to contain user list
      $noteName = $key . '_access_list';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'label' => $levelInfo['label'],
          'description' => $levelInfo['description'],
          'value' => '<ul class="tag-autosuggest zulu_access_list ' . $key . '_access_list">'
          . '<img class="loader" src="' . $baseUrl . 'application/modules/Zulu/externals/images/load.gif" />'
          . '</ul>',
          'order' => $orderIndex++,
      ));
      $note->getDecorator('Description')->setEscape(false);
      Engine_Form::addDefaultDecorators($note);
      $this->addElement($note);

      // Hidden field contains user ids which belong to each list
      $this->addElement('Hidden', $key, array(
          'order' => $orderIndex++,
      ));

      // Links to edit people in list
      $link = '/zulu/index/add-people/access_type/' . $key;
      $noteName = $key . '_edit_list';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => '<div class="access_edit_links">'
          . '<a class="sitepage_button zulu_access_edit_link ' . $noteName . '" href="javascript:void(0)" onclick="showSmoothBox(\'' . $link . '\')"><i class="add_people"></i><span>Add People</span></a>'
          . '<a class="sitepage_button zulu_access_edit_link ' . $noteName . '" href="javascript:void(0)" onclick="javascript:jQuery.removeListItems(\'' . $key . '\')"><span>Remove People</span></a>'
          . '</div>',
          'order' => $orderIndex++,
      ));
      $this->addElement($note);
    }

    // Close wrapper HTML Tag
    $noteName = 'custom_element_' . $orderIndex;
    $note = new Zulu_Form_Element_Note(
            $noteName, array(
        'value' => '</div>',
        'order' => $orderIndex++,
    ));
    $note->removeDecorator('HtmlTag')->removeDecorator('HtmlTag2');
    $this->addElement($note);
  }

  protected function _addMobileControls($access_list = array()) {
    $tabIndex = 1;

    $user_id = $this->getUserId();

    // Generate sharing access elements
    $userTable = new User_Model_DbTable_Users();

    // Select user list for sharing options
    $select = $userTable->select()
            ->from($userTable->info('name'), array('user_id', 'displayname', 'email'))
            ->where('verified = ?', 1)
            ->order('displayname');
    if ($user_id !== null) {
      $select->where('user_id != ?', $user_id);
    }
    $users = $userTable->fetchAll($select);

    $userList = $this->makeUserSelectOptions($users);

    foreach ($this->_accessLevel as $key => $levelInfo) {
      $this->addElement('Multiselect', $key, array(
          'label' => $levelInfo['label'],
          'description' => $levelInfo['description'],
          'required' => false,
          'allowEmpty' => true,
          'validators' => array(
              array('Db_RecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'user_id'))
          ),
          'filters' => array(
              'StringTrim'
          ),
          // fancy stuff
          'autofocus' => 'autofocus',
          'tabindex' => $tabIndex++,
          'multiOptions' => $userList,
      ));
      $this->$key->setValue($access_list[$key]);
      $this->$key->getDecorator('Description')->setEscape(false)->setOptions(array('placement' => 'PREPEND'));
      $this->$key->getValidator('Db_RecordExists')->setMessage('Users do not exist.', Zend_Validate_Db_Abstract::ERROR_NO_RECORD_FOUND);
    }
  }

  public function checkBannedEmail($value, $emailElement) {
    $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
    return !$bannedEmailsTable->isEmailBanned($value);
  }

  public function checkBannedUsername($value, $usernameElement) {
    $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
    return !$bannedUsernamesTable->isUsernameBanned($value);
  }

  public function makeUserSelectOptions($users) {
    $userList = array();

    foreach ($users->toArray() as $user) {
      $userList[$user['user_id']] = "{$user['displayname']} ({$user['email']})";
    }

    return $userList;
  }

}
