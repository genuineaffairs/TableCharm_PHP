<?php
class Document_AdminManageController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('document_admin_main', array(), 'document_admin_main_manage');

    $page = $this->_getParam('page', 1);
    $this->view->paginator = Engine_Api::_()->getItemTable('document')->getDocumentPaginator(array(
      'order' => 'document_id',
      'direction' => 'ASC'
    ));
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function deleteSelectedAction() // i.e. multi delete via checkboxes
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    if($this->getRequest()->isPost() && $confirm == true)
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $ids_array = explode(",", $ids);
        foreach($ids_array as $id)
        {
          $document = Engine_Api::_()->getItem('document', $id);
          if($document) $document->delete();
        }
        $db->commit();
      }
      catch(Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $this->_helper->redirector->gotoRoute(array('action' => ''));
    }
  }

  public function deleteAction() // i.e. single delete
  {
    // in smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->document_id = $id = $this->_getParam('id');
    $confirm = $this->_getParam('confirm', false);

    if($this->getRequest()->isPost() && $confirm == true)
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $document = Engine_Api::_()->getItem('document', $id);
        $document->delete();
        $db->commit();
      }
      catch(Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    $this->renderScript('admin-manage/delete.tpl');
  }
}