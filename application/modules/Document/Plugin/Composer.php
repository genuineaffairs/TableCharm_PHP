<?php
class Document_Plugin_Composer extends Core_Plugin_Abstract
{
    /* this function is called when one makes a news feed post or sends a private message */
    public function onAttachDocument($data)
    {
        if( !is_array($data) || empty($data['document_id']) ) {
            return;
        }

        $document = Engine_Api::_()->getItem('document', $data['document_id']);

        if( !empty($data['document_title']) ) {
            $document->title = trim(strip_tags($data['document_title']));
        }

        if( !empty($data['document_description']) ) {
            $document->description = trim(strip_tags($data['document_description']));
        }

        if (Engine_Api::_()->core()->hasSubject()) {
            // set parent
            $subject = Engine_Api::_()->core()->getSubject();
            $subject_type = $subject->getType();
            $subject_id = $subject->getIdentity();

            $document->parent_type = $subject_type;
            $document->parent_id = $subject_id;
        }

        $document->search = 1; // make the document visible now (see DocumentController for where search is initially set to zero)
        $document->save();

        if( !($document instanceof Core_Model_Item_Abstract) || !$document->getIdentity() )
        {
            return;
        }

        return $document;
    }
}