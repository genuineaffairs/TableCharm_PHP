<?php

/**
 * Description of Hooks
 *
 * @author Chips Invincible <gachip11589@gmail.com> :))
 */
class Zulu_Controller_Action_Helper_Hooks extends Zend_Controller_Action_Helper_Abstract {

  public function postDispatch()
  {
    parent::postDispatch();

    if (Zend_Registry::isRegistered(('Zend_View')) && ($form = Zend_Registry::get('Zend_View')->form)) {
      foreach ($form->getElements() as $element) {
        if (preg_match('/file/', $element->getType())) {
          $form->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
          break;
        }
      }
    }
  }

}
