<?php
class Document_Installer extends Engine_Package_Installer_Module
{
    public function onInstall()
    {
        $this->_documentBrowsePage();
        $this->_documentManagePage();
        $this->_documentViewPage();

        parent::onInstall();
    }

    protected function _documentBrowsePage()
    {
        $db = $this->getDb();

        // profile page
        $page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'document_index_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if(!$page_id)
        {
            // insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'document_index_browse',
                'displayname' => 'Document Browse Page',
                'title' => 'Browse Documents',
                'description' => 'This page lists documents.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'parent_content_id' => null,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
                'order' => 1,
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'document.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'parent_content_id' => null,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }

        return $this;
    }

    protected function _documentManagePage()
    {
        $db = $this->getDb();

        // profile page
        $page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'document_index_manage')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if(!$page_id)
        {
            // insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'document_index_manage',
                'displayname' => 'Document Manage Page',
                'title' => 'My Documents',
                'description' => 'This page lists a user\'s documents.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'parent_content_id' => null,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
                'order' => 1,
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'document.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'parent_content_id' => null,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }

        return $this;
    }

    protected function _documentViewPage()
    {
        $db = $this->getDb();

        // profile page
        $page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'document_index_view')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if(!$page_id)
        {
            // insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'document_index_view',
                'displayname' => 'Document View Page',
                'title' => 'View Document',
                'description' => 'This is the view page for a document.',
                'provides' => 'subject=document',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'parent_content_id' => null,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
                'order' => 1,
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'document.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }

        return $this;
    }
}
?>