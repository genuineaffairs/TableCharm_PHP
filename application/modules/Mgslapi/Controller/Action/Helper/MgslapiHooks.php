<?php

/**
 * Description of MgslapiHooks
 *
 * @author Chips Invincible <gachip11589@gmail.com> :))
 */
class Mgslapi_Controller_Action_Helper_MgslapiHooks extends Zend_Controller_Action_Helper_Abstract {

  protected $_isApp;
  protected $_isMobile;

  public function __construct() {
    $this->_isApp = $this->getRequest()->getParam('from_app');
    $this->_isMobile = Engine_Api::_()->hasModuleBootstrap('zulu') && Engine_Api::_()->zulu()->isMobileMode();
  }

  public function preDispatch() {
    parent::preDispatch();

    if ($this->_isMobile) {
      $fileNames = array('photo');

      foreach ($fileNames as $file) {
        // Hack to eliminate silly errors when posting file from webview of phone app
        if (!array_key_exists($file, $_FILES)) {
          $_FILES[$file] = array(
              'name' => '',
              'type' => '',
              'tmp_name' => '',
              'error' => 4,
              'size' => 0
          );
        }
      }
    }
  }

  public function postDispatch() {
    parent::postDispatch();

    if ($this->_isApp) {
      // Attempt to add from_app param to form action
      if (Zend_Registry::isRegistered(('Zend_View')) && ($form = Zend_Registry::get('Zend_View')->form)) {
        /* @var $form Zend_Form */
        $form->addElement('hidden', 'from_app', array(
            'value' => 1,
            'order' => 999999
        ));
      }
      $redirect = $this->getRequest()->getParam('redirect');
      if (Zend_Registry::isRegistered('Zend_View') && Zend_Registry::get('Zend_View')->notSuccessMessage) {
        // If the web page does not pop up a success notification, and attempt to redirect to another url instead,
        // Then add from_app variable at the end of the URL to ensure layout consistency
        $this->getRequest()->setParam('redirect', $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . 'from_app=1');
      } else {
        $this->getRequest()->setParam('redirect', false)
//              ->setParam('smoothboxClose', false)
        ;
      }

      if (null === $this->getRequest()->getParam('messages')) {
        $this->getRequest()->setParam('messages', Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
      }
    }
  }

}
