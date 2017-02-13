<?php
class Document_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // get navigation menu
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main');
    }
}