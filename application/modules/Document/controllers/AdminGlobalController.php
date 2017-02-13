<?php
class Document_AdminGlobalController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_admin_main', array(), 'document_admin_main_global');
        $this->view->form = $form = new Document_Form_Admin_Global();
        if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
            $form->saveValues();
        }
    }
}
?>
