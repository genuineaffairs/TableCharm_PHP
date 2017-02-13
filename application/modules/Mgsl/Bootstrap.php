<?php

class Mgsl_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    $this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
            . 'application/modules/Mgsl/externals/scripts/core.js');
  }

  protected function _initRequest() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $file = 'global.css';
    $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Mgsl/externals/styles/' . $file);
  }

}
