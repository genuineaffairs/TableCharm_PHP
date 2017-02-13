<?php

class Mgslapi_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    public function __construct($application) 
    {
        parent::__construct($application);
        $this->initViewHelperPath();
        $this->initActionHelperPath();
    }
    public function _bootstrap($resource = null) {
      Zend_Controller_Front::getInstance()->registerPlugin(new Mgslapi_Plugin_Core());
    }
}