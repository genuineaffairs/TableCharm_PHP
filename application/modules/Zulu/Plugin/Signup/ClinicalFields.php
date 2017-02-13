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
class Zulu_Plugin_Signup_ClinicalFields extends Zulu_Plugin_Common_ClinicalFields_Abstract {

  protected $_formClass = 'Zulu_Form_Signup_ClinicalFields';
  protected $_script = array('signup/form/fields.tpl', 'zulu');

  public function onSubmit(Zend_Controller_Request_Abstract $request) {
    $request = Zend_Controller_Front::getInstance()->getRequest();

    if (null === $request->getPost('clinical_submit')) {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }

    if (parent::onSubmit($request) === true) {
      // Store temporary files
      $form = $this->getForm();
      $this->getSession()->ZuluFileData = array();

      foreach ($form->getElements() as $element) {
        if ($element->getType() === 'file') {
          $element->makeTmpFileFromUpload();
          $this->getSession()->ZuluFileData[$element->getName()] = $element->getTmpFile();
        }
      }
      return true;
    } else {
      return false;
    }
  }

  public function getUser() {
    if ($this->_registry->user instanceof User_Model_User) {
      return $this->_registry->user;
    }
    return null;
  }

  public function onProcess() {
    // Get form
    $form = $this->getForm();
    foreach ($this->getSession()->ZuluFileData as $key => $tmpFile) {
      $form->$key->setTmpFile($tmpFile);
      $form->$key->skipValidation();
    }

    parent::onProcess();

    foreach ($this->getSession()->ZuluFileData as $key => $tmpFile) {
      $form->$key->setTmpFile($tmpFile);
      $form->$key->removeTmpFile();
    }
  }

}
