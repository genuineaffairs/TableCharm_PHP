<?php

class Document_IndexController extends Core_Controller_Action_Standard
{
    public function browseAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        // get documents
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('document')->getDocumentPaginator(array(
            'search' => 1 // only show documents that are searchable. for example, this will exclude documents attached in private messages!
        ));
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('document.page', 8);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // render
        $this->_helper->content->setEnabled();
    }

    public function manageAction()
    {
        $this->_helper->content->setEnabled();

        if(!$this->_helper->requireUser->isValid()) return;

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->paginator = $paginator =  Engine_Api::_()->getItemTable('document')->getDocumentPaginator(array(
            'owner_id' => $viewer->getIdentity()
        ));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $itemsPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.page', 10);
        $paginator->setItemCountPerPage($itemsPerPage);
    }

    public function editAction()
    {
        if (!$this->_helper->requireUser()->isValid())
        {
            return;
        }

        if (0 !== ($document_id = (int) $this->_getParam('document_id')) && null !== ($document = Engine_Api::_()->getItem('document', $document_id)) && $document instanceof Document_Model_Document)
        {
            Engine_Api::_()->core()->setSubject($document);
        }
        if (!$this->_helper->requireSubject('document')->isValid())
        {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $document->owner_id && !$this->_helper->requireAuth()->setAuthParams($document, null, 'edit')->isValid())
        {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->document = $document;
        $this->view->form = $form = new Document_Form_Edit(array(
            'document' => $document,
            'title' => 'Edit Document',
            'parent_type' => $document->parent_type,
            'parent_id' => $document->parent_id));

        if (!$this->getRequest()->isPost())
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost()))
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        $db = Engine_Api::_()->getDbtable('documents', 'document')->getAdapter();
        $db->beginTransaction();
        try
        {
            $values = $form->getValues();
            $document->setFromArray($values);
            $document->save();

            // set up viewing permissions
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            $auth_view = $values['auth_view'] ? $values['auth_view'] : "everyone";
            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role)
            {
                $auth->setAllowed($document, $role, 'view', ($i <= $viewMax));
            }

            // add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $document->tags()->setTagMaps($viewer, $tags);

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try
        {
            // rebuild privacy for all activity items (e.g. feed posts) associated with this document around the site
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($document) as $action)
            {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'document_general', true);
    }

    public function deleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $document = Engine_Api::_()->getItem('document', $this->getRequest()->getParam('document_id'));
        if (!$this->_helper->requireAuth()->setAuthParams($document, null, 'delete')->isValid())
            return;

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Document_Form_Delete();

        if (!$document)
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Document doesn't exist or not authorized to delete.");
            return;
        }

        if (!$this->getRequest()->isPost())
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $db = $document->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            Engine_Api::_()->getApi('core', 'document')->deleteDocument($document);
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Document has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'document_general', true),
            'messages'       => Array($this->view->message)
        ));
    }

    public function viewAction()
    {
        $document_id = $this->_getParam('document_id');
        $document = Engine_Api::_()->getItem('document', $document_id);
        if ($document)
        {
            Engine_Api::_()->core()->setSubject($document);
        }
        if (!$this->_helper->requireSubject()->isValid())
        {
            return;
        }
        $type = $document->getType();

        $document = Engine_Api::_()->core()->getSubject('document');
        $viewer = Engine_Api::_()->user()->getViewer();

        // get document Url
        $documentUrl = $document->getHref();
        $pos = strpos($documentUrl, "http");
        if ($pos === false) {
            $documentUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $documentUrl;
        }

        // adding meta tags for sharing
        $view = Zend_Registry::get('Zend_View');
        $og  .= '<meta property="og:title" content="'. $document->getTitle() .'" />';
        $og  .= '<meta property="og:url" content="'. $documentUrl .'" />';
        $view->layout()->headIncludes .= $og;

        // if this is sending a message id, the user is being directed from a conversation (i.e. the
        // document was attached in a conversation), so check if member is part of the conversation
        $message_id = $this->getRequest()->getParam('message');
        $message_view = false;
        if ($message_id)
        {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
            {
                $message_view = true;
            }
        }
        $this->view->message_view = $message_view;

        if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($document, null, 'view')->isValid())
        {
            return;
        }

        $this->view->documentTags = $document->tags()->getTagMaps();

        // check if edit/delete is allowed
        $this->view->can_edit = $can_edit = $this->_helper->requireAuth()->setAuthParams($document, null, 'edit')->checkRequire();
        $this->view->can_delete = $can_delete = $this->_helper->requireAuth()->setAuthParams($document, null, 'delete')->checkRequire();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->document = $document;
        $this->view->viewer_id = $viewer->getIdentity();
        $this->view->content = $document->getRichContent(true);

        // render
        $this->_helper->content->setEnabled();
    }
}
