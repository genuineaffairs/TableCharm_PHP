<?php
class Document_DocumentController extends Core_Controller_Action_Standard
{
  public function composeUploadAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer->getIdentity() )
    {
      $this->_redirect('login');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    // check to make sure the user has not surpassed their quota of allowed document uploads
    $paginator = Engine_Api::_()->getItemTable('document')->getDocumentPaginator(array(
      'owner_id' => $viewer->getIdentity()
    ));
    $this->view->$quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'max');
    if (!empty($quota) && $paginator->getTotalItemCount() >= $quota)
    {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of documents allowed. If you would like to upload a new document, please delete an old one first.');
      return;
    }

    if( empty($_FILES['Filedata']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $legal_extensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx');
    if( !in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $legal_extensions) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid upload');
      return;
    }

    $table = Engine_Api::_()->getDbtable('documents', 'document');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $composer_type = $this->_getParam('c_type', 'wall');

      $document = $table->createRow();
      $document->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity(),
        'parent_type' => 'user', // set to 'user' here as a default, but this can change by the time the post is complete (e.g. to 'group' if posted to a group)
        'parent_id' => $viewer->getIdentity()
      ));
      $document->title = $_FILES['Filedata']['name']; // set document title to filename by default
      $document->save();

      // write the document file (currently uploaded to a temp location) to file storage
      // note: this will link a document record to a storage_file record (foreign key: file_id)
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($_FILES['Filedata'], array(
        'parent_id' => $document->getIdentity(),
        'parent_type' => $document->getType(),
        'user_id' => $document->owner_id
      ));
      @unlink($_FILES['Filedata']['tmp_name']); // remove temporary file
      $document->file_id = $storageObject->file_id;
      $document->save();

      if( $composer_type === 'wall' )
      {
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role)
        {
          $auth->setAllowed($document, $role, 'view', ($i <= $roles));
          $auth->setAllowed($document, $role, 'comment', ($i <= $roles));
        }
      }

      // if document is from the composer, keep it hidden until the post is complete
      if ( $composer_type )
      {
        $document->search = 0; // this will eventually be set to 1 in Document_Plugin_Composer (unless the user cancels their post!)
      }

      $document->save();
      $db->commit();

      $this->view->status = true;
      $this->view->document_id = $document->document_id;
      $this->view->document_title = $document->title;
      $this->view->document_file_path = $document->getFilePath(); // note: at this point, the file will be at a temp location on the server
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Document saved successfully');
    }
    catch( Exception $e )
    {
      $db->rollBack();
      //throw $e;
      $this->view->status = false;
    }
  }
}