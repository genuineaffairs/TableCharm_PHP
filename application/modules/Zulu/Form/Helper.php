<?php

class Zulu_Form_Helper {

  /**
   * This function is used in case of multiple accounts using one email
   */
  function addSubAccountFields(Engine_Form $form) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($view === null) {
      return;
    }

    $is_child_mode = Zend_Controller_Front::getInstance()->getRequest()->getParam('is_children_account');

    $js = $view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/js/sign-up.js';
    $view->headScript()->appendFile($js);

    if ($is_child_mode == 1) {

      $form->addElement('Select', 'is_children_account', array(
          'label' => 'Are you creating an account for your child ?',
          'description' => 'If your child does not have an email address, please tick on above option to create a sub account.'
          . ' Every email which is sent to a sub account will be redirected to parental email.',
          'multiOptions' => array(
              0 => 'No',
              1 => 'Yes'
          ),
          'value' => $is_child_mode,
          'order' => -9999
      ));
      $form->is_children_account->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

      $form->addElement('Text', 'parental_email', array(
          'label' => 'Parental Email Address',
          'description' => 'A confirmation email will be sent to parental email address to activate the sub account.',
          'required' => true,
          'allowEmpty' => false,
          'validators' => array(
              array('NotEmpty', true),
              array('EmailAddress', true),
              array('Db_RecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'email'))
          ),
          'filters' => array(
              'StringTrim'
          ),
          // fancy stuff
          'inputType' => 'email',
          'autofocus' => 'autofocus',
          'order' => -9998,
          'tabindex' => 998
      ));
      $form->parental_email->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

      // Modify email field
      $form->email->setOptions(array(
          'label' => 'Login Name',
          'inputType' => 'text',
          'description' => 'You will use this to login.'
      ));
      $form->email->removeValidator('EmailAddress')->addValidator('Alnum');
    }
    // If we are not in child mode, provide link to go to register child accounts page
    else {
      if (!Engine_Api::_()->zulu()->isMobileMode()) {
        $form->setAttrib('id', 'signup_form');

        $note = new Zulu_Form_Element_Note(
                'signup_childaccounts_link', array(
            'order' => -9999,
            'value' => "<div class='mbl'>Click <a href='signup?is_children_account=1'>here</a> if you want to register child accounts.</div>"
        ));
        $form->addElement($note);
      }
    }
  }

  function checkSubAccountFields(Engine_Form $form, $data = array()) {
    if (isset($data['is_children_account']) && $data['is_children_account'] == 1) {
//      $form->email->removeValidator('EmailAddress');
    }
  }

}
