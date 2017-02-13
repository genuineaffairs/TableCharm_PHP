<?php

class Grandopening_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown() {
        $frontController = Zend_Controller_Front::getInstance();
        $request = $frontController->getRequest();
        $pathinfo = $request->getPathInfo();

        $cookie = $request->getCookie('whGOadmin');
        if ($cookie == 1 && $pathinfo != '/pages/grandopening')
            return;

        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!$settings->getSetting('grandopening_enable', 0) && $pathinfo != '/pages/grandopening')
            return;

        $time = Engine_Api::_()->grandopening()->getEndTime();
        if (trim($settings->getSetting('grandopening_endtime', 0), '0-') && $time < time() && $pathinfo != '/pages/grandopening')
            return;

        if (Engine_Api::_()->user()->getViewer()->getIdentity() && $pathinfo != '/pages/grandopening')
            return;

        $path_ok = array('admin', 'login', 'grandopening/email', 'getslide', 'utility/tasks', 'signup', 'user/auth/forgot', 'auth/reset/code', 'payment/subscription');
        foreach ($path_ok as $value_path) {
            if (strpos($pathinfo, $value_path))
                return;
        }

        if ($pathinfo != '/' && $pathinfo != '/pages/grandopening') {
            $response = $frontController->getResponse();
            return $response->setRedirect(Zend_Registry::get('StaticBaseUrl'));
        }

        $bg = $request->getParam('bg', false);

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;

        $request->setModuleName('core')
                ->setControllerName('pages')
                ->setActionName('grandopening');
        if (!$bg) {
            $coversTable = Engine_Api::_()->getItemTable('cover');
            $cover = $coversTable->getCover();
            $bg = $cover['title'];
        }

        $styles = 'html#smoothbox_window {background-image: url(public/opening_cover/' . $bg . ')}';

        $view->headStyle()->appendStyle($styles);
    }

}