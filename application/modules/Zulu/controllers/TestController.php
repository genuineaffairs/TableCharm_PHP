<?php

class Zulu_TestController extends Core_Controller_Action_Standard {

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
        // If the user is logged in, they can't sign up now can they?
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $formSequenceHelper = $this->_helper->formSequence;
        foreach (Engine_Api::_()->getDbtable('signup', 'user')->fetchAll() as $row) {
            if ($row->enable == 1) {
                $class = $row->class;
                $formSequenceHelper->setPlugin(new $class, $row->order);
            }
        }

        // This will handle everything until done, where it will return true
        if (!$this->_helper->formSequence()) {
            return;
        }
    }

}
