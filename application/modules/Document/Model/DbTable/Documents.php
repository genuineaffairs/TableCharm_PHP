<?php
class Document_Model_DbTable_Documents extends Engine_Db_Table
{
    protected $_rowClass = 'Document_Model_Document';

    public function getDocumentPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getDocumentSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getDocumentSelect($params = array())
    {
        $documentTable = Engine_Api::_()->getItemTable('document');
        $select = $documentTable->select();

        if(!isset($params['direction'])) {
            $params['direction'] = 'DESC';
        }

        if(!empty($params['order'])) {
            $select->order($params['order'].' '.$params['direction']);
        }
        else {
            $select->order('document_id DESC');
        }

        if(!empty($params['owner_id'])) {
            $select->where('owner_id = ?', $params['owner_id']);
        }

        if (isset($params['search']) && is_numeric($params['search'])) {
            $select->where('search = ?', $params['search']);
        }

        if (!empty($params['parent_type'])) {
            $select->where('parent_type = ?', $params['parent_type']);
        }

        if (!empty($params['parent_id'])) {
            $select->where('parent_id = ?', $params['parent_id']);
        }

        return $select;
    }
}
