<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fields.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Plugin_Signup_ProfileFields extends User_Plugin_Signup_Fields {

  protected $_formClass = 'Zulu_Form_Signup_ProfileFields';
  protected $_script = array('signup/form/fields.tpl', 'zulu');
  protected $_pluginSignupAccount = 'User_Plugin_Signup_Account';
  public $pluginSignupPhoto = null;

  public function __construct() {
    $this->pluginSignupPhoto = new Zulu_Plugin_Signup_Photo();
  }

  public function onView() {
    $this->pluginSignupPhoto->onView();
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request) {

    // Photo input validation failed
    if ($request->getParam('uploadPhoto') == true && !$this->pluginSignupPhoto->onSubmit($request)) {
      // Get error messages of Upload photo form
      $errorMessages = $this->pluginSignupPhoto->getForm()->getMessages();
      // Converted error messages array
      $messages = array();

      foreach ($errorMessages as $field => $errors) {
        foreach ($errors as $error) {
          if (is_string($error)) {
            $messages[] = $error;
          }
        }
      }

      // Add Photo form errors into Profile form
      $this->getForm()->addErrors($messages);
    }

    // Re-populate form values
    $this->getForm()->populate($request->getPost());
    // Prevent uploadPhoto input field from being populated
    $this->getForm()->getSubForm('EditPhoto')->getElement('uploadPhoto')->setValue(false);

    if ($request->getParam('uploadPhoto') == true) {
      $this->getSession()->active = true;
      return false;
    } else {
      // Remove EditPhoto SubForm in case of Mobile
      if (Engine_Api::_()->zulu()->isMobileMode() && $request->isPost()) {
        $this->getForm()->removeSubForm('EditPhoto');
      }
      return parent::onSubmit($request);
    }
  }

  public function getForm() {
    if (is_null($this->_form)) {
      $formArgs = array();

      // Preload profile type field stuff
      $profileTypeField = $this->getProfileTypeField();
      if ($profileTypeField) {
        $accountSession = new Zend_Session_Namespace($this->_pluginSignupAccount);
        $profileTypeValue = @$accountSession->data['profile_type'];
        if ($profileTypeValue) {
          $formArgs = array(
              'topLevelId' => $profileTypeField->field_id,
              'topLevelValue' => $profileTypeValue,
          );
        } else {
          $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
          if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            if (count($options) == 1) {
              $formArgs = array(
                  'topLevelId' => $profileTypeField->field_id,
                  'topLevelValue' => $options[0]->option_id,
              );
            }
          }
        }
      }

      // Create form
      Engine_Loader::loadClass($this->_formClass);
      $class = $this->_formClass;
      $this->_form = new $class($formArgs);
      $data = $this->getSession()->data;

      if (!empty($_SESSION['facebook_signup'])) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if ($facebook && $settings->core_facebook_enable) {
            // Load Faceboolk data
            $apiInfo = $facebook->api('/me');
            $fb_data = array();
            $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
            foreach ($fb_keys as $key) {
              if (isset($apiInfo[$key])) {
                $fb_data[$key] = $apiInfo[$key];
              }
            }
            if (isset($apiInfo['birthday']) && !empty($apiInfo['birthday'])) {
              $fb_data['birthdate'] = date("Y-m-d", strtotime($fb_data['birthday']));
            }

            // populate fields, using Facebook data
            $struct = $this->_form->getFieldStructure();
            foreach ($struct as $fskey => $map) {
              $field = $map->getChild();
              if ($field->isHeading())
                continue;

              if (isset($field->type) && in_array($field->type, $fb_keys)) {
                $el_key = $map->getKey();
                $el_val = $fb_data[$field->type];
                $el_obj = $this->_form->getElement($el_key);
                if ($el_obj instanceof Zend_Form_Element &&
                        !$el_obj->getValue()) {
                  $el_obj->setValue($el_val);
                }
              }
            }
          }
        } catch (Exception $e) {
          // Silence?
        }
      }

      // Attempt to preload information
      if (!empty($_SESSION['janrain_signup']) &&
              !empty($_SESSION['janrain_signup_info'])) {
        try {
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if ($settings->core_janrain_enable) {
            $jr_info = $_SESSION['janrain_signup_info'];
            $jr_poco = @$_SESSION['janrain_signup_info']['merged_poco'];
            $jr_data = array();
            if (!empty($jr_info['displayName'])) {
              if (false !== strpos($jr_info['displayName'], ' ')) {
                list($jr_data['first_name'], $jr_data['last_name']) = explode(' ', $jr_info['displayName']);
              } else {
                $jr_data['first_name'] = $jr_info['displayName'];
              }
            }
            if (!empty($jr_info['name']['givenName'])) {
              $jr_data['first_name'] = $jr_info['name']['givenName'];
            }
            if (!empty($jr_info['name']['familyName'])) {
              $jr_data['last_name'] = $jr_info['name']['familyName'];
            }
            if (!empty($jr_info['email'])) {
              $jr_data['email'] = $jr_info['email'];
            }
            if (!empty($jr_info['url'])) {
              $jr_data['website'] = $jr_info['url'];
            }
            if (!empty($jr_info['birthday'])) {
              $jr_data['birthdate'] = date("Y-m-d", strtotime($jr_info['birthday']));
            }

            if (!empty($jr_poco['url']) && false !== stripos($jr_poco['url'], 'www.facebook.com/profile.php?id=')) {
              list($null, $jr_data['facebook']) = explode('www.facebook.com/profile.php?id=', $jr_poco['url']);
            } else if (!empty($jr_data['url']) && false !== stripos($jr_poco['url'], 'http://www.facebook.com/')) {
              list($null, $jr_data['facebook']) = explode('http://www.facebook.com/', $jr_data['url']);
            }
            if (!empty($jr_poco['currentLocation']['formatted'])) {
              $jr_data['location'] = $jr_poco['currentLocation']['formatted'];
            }
            if (!empty($jr_poco['religion'])) {
              // Might not match any values
              $jr_data['religion'] = str_replace(' ', '_', strtolower($jr_poco['religion']));
            }
            if (!empty($jr_poco['relationshipStatus'])) {
              // Might not match all values
              $jr_data['relationship_status'] = str_replace(' ', '_', strtolower($jr_poco['relationshipStatus']));
            }
            if (!empty($jr_poco['politicalViews'])) {
              // Only works if text
              $jr_data['political_views'] = $jr_poco['politicalViews'];
            }

            // populate fields, using janrain data
            $struct = $this->_form->getFieldStructure();
            foreach ($struct as $fskey => $map) {
              $field = $map->getChild();
              if ($field->isHeading())
                continue;

              if (!empty($field->type) && !empty($jr_data[$field->type])) {
                $val = $jr_data[$field->type];
              } else if (!empty($field->alias) && !empty($jr_data[$field->alias])) {
                $val = $jr_data[$field->alias];
              } else {
                continue;
              }

              $el_key = $map->getKey();
              $el_val = $val;
              $el_obj = $this->_form->getElement($el_key);
              if ($el_obj instanceof Zend_Form_Element &&
                      !$el_obj->getValue()) {
                $el_obj->setValue($el_val);
              }
            }
          }
        } catch (Exception $e) {
          echo $e;
          // Silence?
        }
      }

      if (!empty($data)) {
        foreach ($data as $key => $val) {
          $el = $this->_form->getElement($key);
          if ($el instanceof Zend_Form_Element) {
            $el->setValue($val);
          }
        }
      }
    }

    return $this->_form;
  }

  public function onProcess() {
    // In this case, the step was placed before the account step.
    // Register a hook to this method for onUserCreateAfter
    if (!$this->_registry->user) {
      // Register temporary hook
      Engine_Hooks_Dispatcher::getInstance()->addEvent('onUserCreateAfter', array(
          'callback' => array($this, 'onProcess'),
      ));
      return;
    }
    $user = $this->_registry->user;

    // Preload profile type field stuff
    $profileTypeField = $this->getProfileTypeField();
    if ($profileTypeField) {
      $accountSession = new Zend_Session_Namespace($this->_pluginSignupAccount);
      $profileTypeValue = @$accountSession->data['profile_type'];
      if ($profileTypeValue) {
        $values = Engine_Api::_()->fields()->getFieldsValues($user);
        $valueRow = $values->createRow();
        $valueRow->field_id = $profileTypeField->field_id;
        $valueRow->item_id = $user->getIdentity();
        $valueRow->value = $profileTypeValue;
        $valueRow->save();
      } else {
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
          $profileTypeField = $topStructure[0]->getChild();
          $options = $profileTypeField->getOptions();
          if (count($options) == 1) {
            $values = Engine_Api::_()->fields()->getFieldsValues($user);
            $valueRow = $values->createRow();
            $valueRow->field_id = $profileTypeField->field_id;
            $valueRow->item_id = $user->getIdentity();
            $valueRow->value = $options[0]->option_id;
            $valueRow->save();
          }
        }
      }
    }

    // Save them values
    $form = $this->getForm()->setItem($user);
    $form->setProcessedValues($this->getSession()->data);
    $form->saveValues();

    $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
    $user->setDisplayName($aliasValues);
    $user->save();

    $this->pluginSignupPhoto->setRegistry($this->getRegistry());
    $this->pluginSignupPhoto->onProcess();

    $email = $user;

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $guardianEmailRequired = ($settings->getSetting('user.signup.guardianverification', 0) == 1);
    $formMap = $this->getFormMap($form);
    if ($guardianEmailRequired) {
      $dateOfBirth = $formMap['Birthday'];
      $dateOfBirthString = $dateOfBirth['year'] . '-' . $dateOfBirth['month'] . '-' . $dateOfBirth['day'];

      $usersAge = $this->getAge($dateOfBirthString);

      if ($usersAge < 15) {
        $this->_registry->mailType = 'core_verification_guardian';
        $this->_registry->mailParams['email'] = $formMap['Guardian Email'];
        $this->_registry->mailParams['recipient_title'] = $formMap['First Name'] . ' ' . $formMap['Last Name'];
        $email = $formMap['Guardian Email'];
      }
    }

    // If user input parental email, send verification mail to parent's email
    if (isset($this->_registry->parentalEmail) && $this->_registry->parentalEmail) {
      $email = $this->_registry->parentalEmail;
      $parent_id = Engine_Api::_()->user()->getUser($email)->getIdentity();
      $user->parent_id = $parent_id;
      $user->save();
    }

    // Send Welcome E-mail
    if (isset($this->_registry->mailType) && $this->_registry->mailType) {
      $mailType = $this->_registry->mailType;
      $mailParams = $this->_registry->mailParams;
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $email, $mailType, $mailParams
      );
    }

    // Send No Profile Notification Email
    // Moved to successful verification
//    if ($user->photo_id == 0) {
//      $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'edit', 'action' => 'profile'), 'zulu_extended', true);
//      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
//              $email, 'zulu_no_profilephoto', $mailParams
//      );
//    }
    
    // Send Medical Record Announcement Email
    // Moved to scheduled task
//    $timestamp = time() + 172800;
//    $designated_sending_time = date('Y-m-d G:i:s', $timestamp);
//    $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'edit', 'action' => 'clinical'), 'zulu_extended', true);
//    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
//            $email, 'zulu_announcement', $mailParams, $designated_sending_time
//    );

    // Send Notify Admin E-mail
    if (isset($this->_registry->mailAdminType) && $this->_registry->mailAdminType) {
      $mailAdminType = $this->_registry->mailAdminType;
      $mailAdminParams = $this->_registry->mailAdminParams;
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $email, $mailAdminType, $mailAdminParams
      );
    }
  }

  public function getRegistry() {
    return $this->_registry;
  }

  public function resetSession() {
    $this->pluginSignupPhoto->resetSession();

    $session = $this->getSession();
    $session->unsetAll();
    $session->active = true;
    return $this;
  }

}
